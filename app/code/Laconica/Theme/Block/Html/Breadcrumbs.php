<?php

namespace Laconica\Theme\Block\Html;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
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

    /**
     * @param Context $context
     * @param Data $catalogData
     * @param array $data
     * @param Json|null $serializer
     */
    public function __construct(
        Context $context,
        Data $catalogData,
        array $data = [],
        Json $serializer = null
    ) {
        $this->catalogData = $catalogData;
        parent::__construct($context, $data, $serializer);
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
     * Preparing layout
     *
     * @return Breadcrumbs|\Magento\Catalog\Block\Breadcrumbs
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function _prepareLayout()
    {
        $title = [];
        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbsBlock->addCrumb(
                'home', [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            );
            $path = $this->catalogData->getBreadcrumbPath();
            try {
                $product = $this->catalogData->getProduct();
            } catch (Exception $e) {
                $this->_logger->critical('Breadcrumbs exceptions: ' . $e->getMessage());
                return parent::_prepareLayout();
            }

            if ($product && count($path) == 1) {
                $categoryCollection = clone $product->getCategoryCollection();
                $categoryCollection->clear();
                $categoryCollection->addAttributeToSort('level', $categoryCollection::SORT_ORDER_DESC)
                    ->addAttributeToFilter('path', [
                        'like' => "1/" . $this->_storeManager->getStore()->getRootCategoryId() . "/%"
                    ]);
                $categoryCollection->setPageSize(1);
                $breadcrumbCategories = $categoryCollection->getFirstItem()->getParentCategories();

                foreach ($breadcrumbCategories as $category) {
                    $catbreadcrumb = ["label" => $category->getName(), "link" => $category->getUrl()];
                    $breadcrumbsBlock->addCrumb("category" . $category->getId(), $catbreadcrumb);
                    $title[] = $category->getName();
                }
                //add current product to breadcrumb
                $prodbreadcrumb = ["label" => $product->getName(), "link" => ""];
                $breadcrumbsBlock->addCrumb("product" . $product->getId(), $prodbreadcrumb);
                $title[] = $product->getName();
            } else {
                foreach ($path as $name => $breadcrumb) {
                    $breadcrumbsBlock->addCrumb($name, $breadcrumb);
                    $title[] = $breadcrumb['label'];
                }
            }
            $this->pageConfig->getTitle()->set(join($this->getTitleSeparator(), array_reverse($title)));
            return parent::_prepareLayout();
        }
        $path = $this->catalogData->getBreadcrumbPath();
        foreach ($path as $name => $breadcrumb) {
            $title[] = $breadcrumb['label'];
        }
        $this->pageConfig->getTitle()->set(join($this->getTitleSeparator(), array_reverse($title)));
        return parent::_prepareLayout();
    }
}
