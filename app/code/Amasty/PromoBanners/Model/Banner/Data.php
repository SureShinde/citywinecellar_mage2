<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PromoBanners
 */


namespace Amasty\PromoBanners\Model\Banner;

use Amasty\PromoBanners\Model\Factories\SegmentFactory;
use Amasty\PromoBanners\Model\Rule;
use Magento\Framework\View\LayoutInterface;
use Amasty\PromoBanners\Helper\Data as PromoBannersHelper;

class Data
{
    /**
     * @var \Amasty\PromoBanners\Model\ResourceModel\Rule\CollectionFactory
     */
    private $ruleCollectionFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Amasty\PromoBanners\Model\ResourceModel\Rule
     */
    private $ruleResource;

    /**
     * @var \Magento\Catalog\Model\Layer
     */
    private $layer;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @var \Amasty\PromoBanners\Model\Factories\SegmentFactory
     */
    private $segmentFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    public function __construct(
        \Amasty\PromoBanners\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Amasty\PromoBanners\Model\ResourceModel\Rule $ruleResource,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        LayoutInterface $layout,
        \Amasty\PromoBanners\Model\Factories\SegmentFactory $segmentFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->checkoutSession = $checkoutSession;
        $this->scopeConfig = $scopeConfig;
        $this->ruleResource = $ruleResource;
        $this->layer = $layerResolver->get();
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->layout = $layout;
        $this->segmentFactory = $segmentFactory;
        $this->coreRegistry = $coreRegistry;
        $this->moduleManager = $moduleManager;
    }

    public function getBanners(
        array $sections = [],
        $categoryId = null,
        $productSku = null,
        $searchQuery = null,
        array $bannersIds = []
    ) {
        $banners = [];
        $product = null;

        /** @var \Amasty\PromoBanners\Model\ResourceModel\Rule\Collection $collection */
        $collection = $this->getBannersCollection($categoryId, $productSku, $bannersIds);

        $quote = $this->checkoutSession->getQuote();
        $address = $quote->getShippingAddress();
        $address->setTotalQty($quote->getItemsQty());

        if ($productSku) {
            $product = $this->productRepository->get($productSku);
        }

        foreach ($collection->getItems() as $banner) {
            if (!$this->validateSegments($banner)) {
                continue;
            }

            if (in_array($banner->getId(), $bannersIds)) {
                $banners[] = $banner;
                continue;
            }

            $bannerSections = $banner->getBannerPositions();
            if (!empty($sections) && empty(array_intersect($sections, $bannerSections))) {
                continue;
            }

            if ($searchQuery
                && !$banner->validateSearch($searchQuery)
            ) {
                continue;
            }

            if ($product
                && $product->getId()
                && !$banner->validateProduct($product)
            ) {
                continue;
            }

            if (!$banner->validate($address)) {
                continue;
            }

            if ($banner->getShowProducts()) {
                $this->loadAssignedProducts($banner, $categoryId);
            }

            $banners[] = $banner;
        }

        return [
            'sections' => $this->getBannersPositions($banners),
            'content' => $this->renderBanners($banners),
            'injectorParams' => $this->getInjectorParams($banners),
            'banners' =>  $this->getIdsBanners($banners)
        ];
    }

    /**
     * @param array $banners
     *
     * @return array
     */
    private function getIdsBanners($banners)
    {
        $result = [];

        /** @var Rule $banner */
        foreach ($banners as $banner) {
            $position = $banner->getBannerPositions();

            if (!$position) {
                $result[] = (int)$banner->getId();
            }
        }

        return $result;
    }

    /**
     * @param array $banners
     *
     * @return array
     */
    protected function getInjectorParams($banners)
    {
        $config = [
            'containerSelector' => $this->scopeConfig->getValue('ampromobanners/general/product_container'),
            'itemSelector' => $this->scopeConfig->getValue('ampromobanners/general/product_item'),
            'banners' => []
        ];

        $columnCount = (int)$this->scopeConfig->getValue('ampromobanners/general/max_banner_width') ?: 1;

        /** @var Rule $banner */
        foreach ($banners as $banner) {
            if (!in_array(Rule::POS_AMONG_PRODUCTS, $banner->getBannerPositions())) {
                continue;
            }

            $config['banners'][$banner->getId()] = [
                'afterProductRow' => $banner->getAfterProductRow(),
                'afterProductNum' => $banner->getAfterProductNum($columnCount),
                'width' => (int)$banner->getData('n_product_width')
            ];
        }

        return $config;
    }

    /**
     * @param array $banners
     *
     * @return array
     */
    protected function renderBanners($banners)
    {
        $bannerBlock = $this->layout->createBlock(\Amasty\PromoBanners\Block\Banner::class);

        $result = [];

        foreach ($banners as $banner) {
            $bannerBlock->setBanner($banner);
            $result[$banner->getId()] = $bannerBlock->toHtml();
        }

        return $result;
    }

    /**
     * @param array $banners
     *
     * @return array
     */
    protected function getBannersPositions($banners)
    {
        $result = [];

        $singleMode = $this->scopeConfig->isSetFlag('ampromobanners/general/single_per_position');

        /** @var Rule $banner */
        foreach ($banners as $banner) {
            $sections = $banner->getBannerPositions();

            foreach ($sections as $section) {
                if ($singleMode && isset($result[$section])) {
                    continue;
                }

                if (!isset($result[$section])) {
                    $result[$section] = [];
                }

                $result[$section] [] = (int)$banner->getId();
            }
        }

        return $result;
    }

    /**
     * @param mixed|null $categoryIds
     * @param string|null $productSku
     * @param array $bannerIds
     *
     * @return \Amasty\PromoBanners\Model\ResourceModel\Rule\Collection
     */
    protected function getBannersCollection($categoryIds = null, $productSku = null, $bannerIds = [])
    {
        $groupId = (int)$this->customerSession->getCustomerGroupId();
        $storeId = (int)$this->storeManager->getStore()->getId();

        /** @var \Amasty\PromoBanners\Model\ResourceModel\Rule\Collection $collection */
        $collection = $this->ruleCollectionFactory->create();

        $collection
            ->addFieldToFilter(
                'from_date',
                [
                    ['null' => true],
                    ['lteq' => new \Zend_Db_Expr('NOW()')]
                ]
            )
            ->addFieldToFilter(
                'to_date',
                [
                    ['null' => true],
                    ['gteq' => new \Zend_Db_Expr('NOW()')]
                ]
            )
            ->addFieldToFilter(
                'cust_groups',
                [
                    '',
                    ['finset' => $groupId]
                ]
            )
            ->addFieldToFilter(
                'stores',
                [
                    '',
                    ['finset' => $storeId]
                ]
            )
            ->addFieldToFilter('is_active', 1)
            ->setOrder('sort_order', 'desc');

        $collection->addFieldToFilter(['banner_position', 'id'], [['neq' => ''], ['in' => $bannerIds]]);

        if ($categoryIds) {
            $condition = [['eq' => '']];
            if (!is_array($categoryIds)) {
                $categoryIds = [$categoryIds];
            }
            foreach ($categoryIds as $value) {
                $condition[] = ['finset' => $value];
            }
            $collection->addFieldToFilter(
                'cats',
                $condition
            );
        }

        if ($productSku) {
            $collection->addFieldToFilter(
                'show_on_products',
                [
                    ['null' => true],
                    ['finset' => $productSku]
                ]
            );
        }

        return $collection;
    }

    /**
     * @param Rule $banner
     * @param $currentCategory
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function loadAssignedProducts(Rule $banner, $currentCategory)
    {
        $productIds = $this->ruleResource
            ->getProducts($banner->getId());

        $categoryId = $this->storeManager->getStore()->getRootCategoryId();

        if ($categoryId) {
            $category = $this->categoryRepository->get($categoryId);

            if ($category && $category->getId()) {
                $this->layer->setCurrentCategory($category);
            }

            $collection = $category->getProductCollection();
            if ($collection instanceof \Magento\Catalog\Model\ResourceModel\Product\Collection) {
                $collection->addPriceData();
                $collection->addAttributeToSelect('*');
            }

            $collection
                ->addStoreFilter()
                ->addAttributeToFilter('entity_id', ['in' => array_values($productIds)]);

            $this->layer->setCurrentCategory($currentCategory ?: $categoryId);

            $banner->setData('product_collection', $collection);
        }
    }

    /**
     * @return mixed
     */
    public function getRule()
    {
        return $this->coreRegistry->registry('current_amasty_promo_banner');
    }

    /**
     * @param Rule $banner
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function validateSegments(Rule $banner)
    {
        $valid = true;
        $segments = $banner->getSegments();

        if (($this->moduleManager->isEnabled(PromoBannersHelper::AMASTY_SEGMENT_MODULE_DEPEND_NAMESPACE)
            && !empty($segments))
        ) {
            if (!$customerId = $this->customerSession->getCustomerId()) {
                return false;
            }
            $arrSegments = explode(',', $segments);
            $segmentIndex = $this->segmentFactory->getSegmentIndex();

            $query = $segmentIndex->getConnection()
                ->select()
                ->from($segmentIndex->getMainTable(), SegmentFactory::CUSTOMER_ID)
                ->where(SegmentFactory::SEGMENT_ID . ' IN (?)', $arrSegments)
                ->where(SegmentFactory::CUSTOMER_ID . ' = ?', $customerId);

            $customerIds = $segmentIndex->getConnection()->fetchAll($query);

            if (empty($customerIds)) {
                return false;
            }
        }

        return $valid;
    }
}
