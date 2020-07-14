<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Adminhtml\Tags\Edit;

use Amasty\Blog\Controller\Adminhtml\Tags\Edit;

class DeleteButton extends \Amasty\Blog\Block\Adminhtml\DeleteButton
{
    /**
     * @return int
     */
    public function getItemId()
    {
        return (int)$this->getRegistry()->registry(Edit::CURRENT_AMASTY_BLOG_TAG)->getTagId();
    }
}
