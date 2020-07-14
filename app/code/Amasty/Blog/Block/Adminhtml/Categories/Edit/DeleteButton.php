<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Adminhtml\Categories\Edit;

use Amasty\Blog\Controller\Adminhtml\Categories\Edit;
use Amasty\Blog\Model\Categories;

class DeleteButton extends \Amasty\Blog\Block\Adminhtml\DeleteButton
{
    /**
     * @return int
     */
    public function getItemId()
    {
        return (int)$this->getCurrentCategory()->getCategoryId();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getConfirmText()
    {
        if ($this->getCurrentCategory()->hasChildren()) {
            return __('This category has children categories. Are you sure you want to delete this?');
        }

        return parent::getConfirmText();
    }

    /**
     * @return Categories
     */
    public function getCurrentCategory()
    {
        return $this->getRegistry()->registry(Edit::CURRENT_AMASTY_BLOG_CATEGORY);
    }
}
