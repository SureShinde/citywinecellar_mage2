<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Sidebar;

class Custom extends \Amasty\Blog\Block\Sidebar\Recentpost
{
    /**
     * @return bool
     */
    public function getDisplay()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getPostsLimit()
    {
        return $this->getData('record_limit');
    }

    /**
     * @return int|bool
     */
    public function getCategoryId()
    {
        if (($categoryId = $this->getData('category_id')) && ($categoryId !== '-')) {
            return $categoryId;
        }

        return false;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBlockHeader()
    {
        if ($this->getCategoryId()) {
            $category = $this->getCategoryRepository()->getById($this->getCategoryId());

            return $this->escapeHtml($category->getName());
        }

        return parent::getBlockHeader();
    }

    /**
     * @param $collection
     * @return $this|Recentpost
     */
    protected function checkCategory($collection)
    {
        if ($this->getCategoryId()) {
            $collection->addCategoryFilter($this->getCategoryId());
        }

        return $this;
    }
}
