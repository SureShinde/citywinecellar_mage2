<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Content;

use Amasty\Blog\Api\Data\AuthorInterface;

class Author extends Lists implements \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * @var AuthorInterface
     */
    private $author;

    /**
     * @return $this|Lists
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->getToolbar()->setPagerObject($this->getAuthor());

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
                $this->getAuthorUrlKey(),
                [
                    'label' => $this->getTitle(),
                    'title' => $this->getTitle(),
                ]
            );
        }
    }

    /**
     * @return \Amasty\Blog\Model\ResourceModel\Posts\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCollection()
    {
        if (!$this->collection) {
            parent::getCollection();
            $this->collection->addFieldToFilter(
                AuthorInterface::AUTHOR_ID,
                ['eq' => $this->getAuthor()->getAuthorId()]
            );
        }

        return $this->collection;
    }

    /**
     * @return string
     */
    public function getAuthorName()
    {
        return $this->getAuthor()->getName();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTitle()
    {
        return __("More from '%1'", $this->getAuthorName());
    }

    /**
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->getSettingHelper()->getPrefixTitle(
            $this->getAuthor()->getMetaTitle() ?: $this->getAuthor()->getName()
        );
    }

    /**
     * @return null|string
     */
    public function getKeywords()
    {
        return $this->getAuthor()->getMetaTags();
    }

    /**
     * @return null|string
     */
    public function getDescription()
    {
        return $this->getAuthor()->getMetaDescription();
    }

    /**
     * @return string
     */
    private function getAuthorUrlKey()
    {
        return $this->getAuthor()->getUrlKey();
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Amasty\Blog\Model\Author::CACHE_TAG . '_' . $this->getAuthorUrlKey()];
    }

    /**
     * @return AuthorInterface
     */
    private function getAuthor()
    {
        try {
            if (!$this->author) {
                $this->author = $this->getAuthorRepository()->getByIdAndStore(
                    (int)$this->getRequest()->getParam('id'),
                    $this->_storeManager->getStore()->getId()
                );
            }
        } catch (\Exception $e) {
            $this->_logger->critical($e);

            $this->author = $this->getAuthorRepository()->getAuthorModel();
        }

        return $this->author;
    }
}
