<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Controller\Adminhtml\Comments;

use Amasty\Blog\Model\Comments;

/**
 * Class Save
 */
class Save extends \Amasty\Blog\Controller\Adminhtml\Comments
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = (int)$this->getRequest()->getParam('comment_id');
            try {
                $model = $this->getCommentRepository()->getComment();
                $inputFilter = new \Zend_Filter_Input([], [], $data);
                $data = $inputFilter->getUnescaped();

                if ($id) {
                    $model = $this->getCommentRepository()->getById($id);
                }
                $replyTo = $this->getRequest()->getParam('reply_to');
                if ($replyTo) {
                    $parentModel = $this->getCommentRepository()->getById($replyTo);
                    $data['post_id'] = $parentModel->getPostId();
                } else {
                    $data['reply_to'] = null;
                }

                $model->addData($data);

                $this->_getSession()->setPageData($model->getData());
                $this->getCommentRepository()->save($model);
                $this->getMessageManager()->addSuccessMessage(__('You saved the item.'));
                $this->_getSession()->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $model->getCommentId()]);

                    return;
                }
                $this->_redirect('*/*/');

                return;
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $this->getMessageManager()->addErrorMessage($e->getMessage());
                $this->getDataPersistor()->set(Comments::PERSISTENT_NAME, $data);
                if (!empty($id)) {
                    $this->_redirect('*/*/edit', ['id' => $id]);
                } else {
                    $this->_redirect('*/*/new');
                }

                return;
            } catch (\Exception $e) {
                $this->getMessageManager()->addErrorMessage(
                    __('Something went wrong while saving the item data. Please review the error log.')
                );
                $this->getLogger()->critical($e);
                $this->_getSession()->setPageData($data);
                $this->_redirect('*/*/edit', ['id' => $id]);

                return;
            }
        }
        $this->_redirect('*/*/');
    }
}
