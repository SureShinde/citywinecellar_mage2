<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Content;

class Tag extends Lists implements \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * @var \Amasty\Blog\Model\Tag
     */
    private $tag;

    protected function _construct()
    {
        $this->isTag = true;
        parent::_construct();
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->getToolbar()->setPagerObject($this->getTag());

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
            $breadcrumbs->addCrumb(
                'blog',
                [
                    'label' => $this->getSettingHelper()->getBreadcrumb(),
                    'title' => $this->getSettingHelper()->getBreadcrumb(),
                    'link'  => $this->getUrlResolverModel()->getBlogUrl(),
                ]
            );

            $breadcrumbs->addCrumb(
                $this->getTag()->getUrlKey(),
                [
                    'label' => $this->getTitle(),
                    'title' => $this->getTitle(),
                ]
            );
        }
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTitle()
    {
        return __("Posts tagged '%1'", $this->getTag()->getName());
    }

    /**
     * @return null|string
     */
    public function getMetaTitle()
    {
        return $this->getSettingHelper()->getPrefixTitle($this->getTag()->getMetaTitle())
            ? $this->getTag()->getMetaTitle()
            : $this->getSettingHelper()->getPrefixTitle($this->getTitle());
    }

    /**
     * @return null|string
     */
    public function getKeywords()
    {
        return $this->getTag()->getMetaTags();
    }

    /**
     * @return null|string
     */
    public function getDescription()
    {
        return $this->getTag()->getMetaDescription();
    }

    /**
     * @return \Amasty\Blog\Model\Tag
     */
    private function getTag()
    {
        try {
            if (!$this->tag) {
                $this->tag = $this->getTagRepository()->getByIdAndStore(
                    (int)$this->getRequest()->getParam('id'),
                    $this->_storeManager->getStore()->getId()
                );
            }
        } catch (\Exception $e) {
            $this->_logger->critical($e);

            return $this->getTagRepository()->getTagModel();
        }

        return $this->tag;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Amasty\Blog\Model\Tag::CACHE_TAG . '_' . $this->getTag()->getId()];
    }
}
