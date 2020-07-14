<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Controller\Adminhtml\Comments;

/**
 * Class Reply
 */
class Reply extends \Amasty\Blog\Controller\Adminhtml\Comments
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        return $this->_redirect('*/*/edit', ['reply_to_id' => $this->getRequest()->getParam('id')]);
    }
}
