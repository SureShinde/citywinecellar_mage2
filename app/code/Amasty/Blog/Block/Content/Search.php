<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Content;

class Search extends \Amasty\Blog\Block\Content\Lists
{
    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->getToolbar()
            ->setSearchPage(true)
            ->setQuery(sprintf("query=%s", $this->getRequest()->getParam('query')));

        return $this;
    }

    /**
     * @return AbstractBlock|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function prepareBreadcrumbs()
    {
        parent::prepareBreadcrumbs();
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $breadcrumbText = $this->getSettingHelper()->getBreadcrumb();
            $breadcrumbs->addCrumb(
                'blog',
                [
                    'label' => $breadcrumbText,
                    'title' => $breadcrumbText,
                    'link' => $this->getUrlResolverModel()->getBlogUrl(),
                ]
            );
            $title = $this->getTitle();
            $breadcrumbs->addCrumb(
                'search',
                [
                    'label' => $title,
                    'title' => $title,
                ]
            );
        }
    }

    /**
     * @return \Amasty\Blog\Model\ResourceModel\Posts\Collection
     */
    public function getCollection()
    {
        if (!$this->collection) {
            $posts = $this->getPostRepository()->getActivePosts()
                ->addSearchFilter($this->getQueryText());
            $this->collection = $posts;
        }

        return $this->collection;
    }

    /**
     * @return string
     */
    private function getQueryText()
    {
        return $this->getRequest()->getParam('query');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTitle()
    {
        return __("Search results for '%1'", $this->escapeHtml($this->getQueryText()));
    }

    /**
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->getSettingHelper()->getPrefixTitle($this->getTitle());
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getDescription()
    {
        return __(
            "There are following posts founded for the search request '%1'",
            $this->escapeHtml($this->getQueryText())
        );
    }

    /**
     * @return string
     */
    public function getKeywords()
    {
        return $this->escapeHtml($this->getQueryText());
    }
}
