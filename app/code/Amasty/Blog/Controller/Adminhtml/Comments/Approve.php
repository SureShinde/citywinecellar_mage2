<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Controller\Adminhtml\Comments;

use Amasty\Blog\Model\Source\CommentStatus;

/**
 * Class
 */
class Approve extends \Amasty\Blog\Controller\Adminhtml\Comments
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            /** @var \Amasty\Blog\Model\Comments $model */
            $model = $this->getCommentRepository()->getById($id);
            $model->setData('status', CommentStatus::STATUS_APPROVED);
            $this->getCommentRepository()->save($model);
        } catch (\Exception $e) {
            $this->getMessageManager()->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('*/*/');
    }
}
