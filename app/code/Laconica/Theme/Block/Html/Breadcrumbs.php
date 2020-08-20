<?php

namespace Laconica\Theme\Block\Html;

use Exception;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Catalog\Helper\Data;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;

class Breadcrumbs extends \Magento\Theme\Block\Html\Breadcrumbs
{
    /**
     * Catalog data
     *
     * @var Data
     */
    protected $catalogData;

    protected $registry;

    protected $categoryFactory;

    /**
     * @param Context $context
     * @param Data $catalogData
     * @param Registry $registry
     * @param CategoryFactory $categoryFactory
     * @param array $data
     * @param Json|null $serializer
     */
    public function __construct(
        Context $context,
        Data $catalogData,
        Registry $registry,
        CategoryFactory $categoryFactory,
        array $data = [],
        Json $serializer = null
    ) {
        parent::__construct($context, $data, $serializer);
        $this->catalogData = $catalogData;
        $this->registry = $registry;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * Retrieve HTML title value separator (with space)
     *
     * @param null|string|bool|int|Store $store
     * @return string
     */
    public function getTitleSeparator($store = null)
    {
        $separator = $this->_scopeConfig->getValue('catalog/seo/title_separator', ScopeInterface::SCOPE_STORE, $store);
        return " $separator ";
    }

    /**
     * @return array
     */
    public function getCrumbs()
    {
        return $this->_crumbs;
    }

    /**
     * @param $product
     * @return array|null
     */
    public function getCategory($product)
    {
        // for multiple categories, select only the first one
        // remember, index = 0 is 'Default' category
        if (!$product->getCategoryIds()) {
            return null;
        } elseif (isset ($product->getCategoryIds()[1])) {
            $categoryId = $product->getCategoryIds()[1];
        }  else {
            $categoryId = $product->getCategoryIds()[0];
        }
        // Additionally for other types of attributes (select, multiselect, ..)
        $category = $this->categoryFactory->create()->load($categoryId);
        return ['label' => $category->getName(), 'url' => $category->getUrl()];

    }

    /**
     * Preparing layout
     *
     * @return Breadcrumbs
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function _prepareLayout()
    {
        $product = $this->registry->registry('current_product');
        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbsBlock->addCrumb(
                'home', [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            );
            $title = [];
            $path = $this->catalogData->getBreadcrumbPath();

            if ($product != null) {
                $foundCatPath = false;
                foreach ($path as $name => $breadcrumb) {
                    if (strpos($name, 'category') > -1)
                        $foundCatPath = true;
                }

                if (!$foundCatPath) {
                    $productCategory = $this->getCategory($product);
                    if ($productCategory) {
                        $categoryPath = ['category' => ['label' => $productCategory['label'], 'link' => $productCategory['url']]];
                        $path = array_merge($categoryPath, $path);
                    }
                }
            }

            foreach ($path as $name => $breadcrumb) {
                $breadcrumbsBlock->addCrumb($name, $breadcrumb);
                $title[] = $breadcrumb['label'];
            }

            $this->pageConfig->getTitle()->set(join($this->getTitleSeparator(), array_reverse($title)));
        }
        return parent::_prepareLayout();
    }
}
