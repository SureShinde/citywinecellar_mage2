<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Content;

use Amasty\Blog\Api\Data\CategoryInterface;

class Category extends \Amasty\Blog\Block\Content\Lists implements \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * @var $category
     */
    private $category;

    protected function _construct()
    {
        $this->isCategory = true;
        parent::_construct();
    }

    /**
     * @return $this|Lists
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $this->setTitle($this->getCategory()->getName());
        parent::_prepareLayout();
        $this->getToolbar()->setPagerObject($this->getCategory());

        return $this;
    }

    /**
     * @return Lists|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function prepareBreadcrumbs()
    {
        parent::prepareBreadcrumbs();
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $breadcrumbs->addCrumb(
                'blog',
                [
                    'label' => $this->getSettingHelper()->getBreadcrumb(),
                    'title' => $this->getSettingHelper()->getBreadcrumb(),
                    'link'  => $this->getUrlResolverModel()->getBlogUrl(),
                ]
            );

            $breadcrumbs->addCrumb(
                $this->getCategory()->getUrlKey(),
                [
                    'label' => $this->getCategory()->getName(),
                    'title' => $this->getCategory()->getName(),
                ]
            );
        }
    }

    /**
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->getCategory()->getMetaTitle()
            ? $this->getSettingHelper()->getPrefixTitle($this->getCategory()->getMetaTitle())
            : $this->getSettingHelper()->getPrefixTitle($this->getCategory()->getName());
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getCategory()->getMetaDescription();
    }

    /**
     * @return string
     */
    public function getKeywords()
    {
        return $this->getCategory()->getMetaTags();
    }

    /**
     * @return \Amasty\Blog\Model\Categories|\Magento\Framework\DataObject
     */
    public function getCategory()
    {
        try {
            if (!$this->category) {
                $category = $this->getCategoryRepository()->getByIdAndStore(
                    (int)$this->getRequest()->getParam('id'),
                    $this->_storeManager->getStore()->getId()
                );
                $this->category = $category;
            }
        } catch (\Exception $e) {
            $this->_logger->critical($e->getMessage());
            return $this->getCategoryRepository()->getCategory();
        }

        return $this->category;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Amasty\Blog\Model\Categories::CACHE_TAG . '_' . $this->getCategory()->getId()];
    }
}
