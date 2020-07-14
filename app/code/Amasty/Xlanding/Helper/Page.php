<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Amasty\Xlanding\Helper;

use Amasty\Xlanding\Block\Adminhtml\Page\Edit\Tab\Meta;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;

/**
 * CMS Page Helper
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
class Page extends \Magento\Framework\App\Helper\AbstractHelper
{
    const FORWARDED_MODULE_NAME = 'module_name';
    const CATALOG_MODULE_NAME = 'catalog';
    const CONFIG_PATH_SEARCH_ENGINE = 'catalog/search/engine';

    /**
     * @var array
     */
    private $allowedEngines = [
        'mysql',
        'elasticsearch',
        'elasticsearch5',
        'elasticsearch6',
        'amasty_elastic'
    ];

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var \Amasty\Xlanding\Model\Page
     */
    private $page;

    /**
     * @var \Magento\Framework\View\DesignInterface
     */
    private $design;

    /**
     * @var \Amasty\Xlanding\Model\PageFactory
     */
    private $pageFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $localeDate;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Magento\Catalog\Model\Layer\Resolver
     */
    private $layerResolver;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    private $pageConfig;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    private $redirectFactory;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Amasty\Xlanding\Model\Page $page,
        \Magento\Framework\View\DesignInterface $design,
        \Amasty\Xlanding\Model\PageFactory $pageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $coreRegistry,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Framework\View\Page\Config $pageConfig,
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
    ) {
        $this->messageManager = $messageManager;
        $this->page = $page;
        $this->design = $design;
        $this->pageFactory = $pageFactory;
        $this->storeManager = $storeManager;
        $this->localeDate = $localeDate;
        $this->escaper = $escaper;
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $coreRegistry;
        $this->categoryRepository = $categoryRepository;
        $this->layerResolver = $layerResolver;
        $this->pageConfig = $pageConfig;
        $this->redirectFactory = $redirectFactory;

        parent::__construct($context);
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function prepareResultPage($request)
    {
        $pageId = $request->getParam('page_id', $request->getParam('id', false));
        if ($pageId !== null && $pageId !== $this->page->getId()) {
            $delimiterPosition = strrpos($pageId, '|');
            if ($delimiterPosition) {
                $pageId = substr($pageId, 0, $delimiterPosition);
            }

            $this->page->setStoreId($this->storeManager->getStore()->getId());
            if (!$this->page->load($pageId)) {
                return false;
            }
        }

        if (!$this->page->getId()) {
            return false;
        }

        if ($this->page->isDynamic() && $this->page->getDynamicCategoryId()) {
            try{
                $category = $this->categoryRepository->get($this->page->getDynamicCategoryId());
                $categoryUrl = $category->getUrl();
                $redirect = $this->redirectFactory->create();

                return $redirect->setUrl($categoryUrl)
                    ->setHttpResponseCode(302);

            }catch (NoSuchEntityException $e) {
                //do nothing
            }

        }


        $rootCategoryId = $this->storeManager->getStore()->getRootCategoryId();

        $category = $this->categoryRepository->get($rootCategoryId, $this->storeManager->getStore()->getId());

        $this->coreRegistry->register('current_category', $category);
        $this->coreRegistry->register('amasty_xlanding_page', $this->page);

        $resultPage = $this->resultPageFactory->create();
        $this->setLayoutType($resultPage);

        $resultPage->addHandle('catalog_category_view');

        $type = $category->hasChildren() ? 'layered' : 'layered_without_children';

        if (!$category->hasChildren()) {
            // Two levels removed from parent.  Need to add default page type.
            $parentType = strtok($type, '_');
            $resultPage->addPageLayoutHandles(
                ['type' => $parentType],
                'catalog_category_view'
            );
        }

        $resultPage->addPageLayoutHandles(
            ['type' => $type, 'id' => $category->getId(), 'landing_id' => $this->page->getId()],
            'catalog_category_view'

        );

        $layoutUpdate = $this->page->getLayoutUpdateXml();

        if (!empty($layoutUpdate)) {
            $resultPage->getLayout()->getUpdate()->addUpdate($layoutUpdate);
        }

        $this->setPageTitle($resultPage);
        $this->addBreadcrumb($resultPage);

        $storeId = $this->storeManager->getStore()->getId();
        $metaData = $this->page->getMetaData(true);

        if ($metaData) {
            $this->pageConfig->getTitle()->set($metaData[Meta::META_TITLE . $storeId]);
            $this->pageConfig->setKeywords($metaData[Meta::META_KEYWORDS . $storeId]);
            $this->pageConfig->setDescription($metaData[Meta::META_DESCRIPTION . $storeId]);
        }

        return $resultPage;
    }

    /**
     * @param $resultPage
     */
    protected function addBreadcrumb($resultPage)
    {
        $breadcrumbs = $resultPage->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $breadcrumbs->addCrumb('amasty_xlanding_page', ['label' => $this->page->getTitle(), 'title' => $this->page->getTitle()]);
        }
    }

    /**
     * @param $resultPage
     */
    protected function setPageTitle($resultPage)
    {
        $contentHeadingBlock = $resultPage->getLayout()->getBlock('page.main.title');

        if ($contentHeadingBlock) {
            $contentHeading = $this->escaper->escapeHtml($this->page->getTitle());
            $contentHeadingBlock->setPageTitle($contentHeading);
        }
    }

    /**
     * @param $resultPage
     * @return mixed
     */
    protected function setLayoutType($resultPage)
    {
        if ($this->page->getPageLayout()) {
            $resultPage->getConfig()->setPageLayout($this->page->getPageLayout());
        }
        return $resultPage;
    }

    /**
     * @return bool
     */
    public function isAllowIndex()
    {
        $engine = $this->scopeConfig->getValue(self::CONFIG_PATH_SEARCH_ENGINE, ScopeInterface::SCOPE_STORE);
        return in_array($engine, $this->allowedEngines, true);
    }
}