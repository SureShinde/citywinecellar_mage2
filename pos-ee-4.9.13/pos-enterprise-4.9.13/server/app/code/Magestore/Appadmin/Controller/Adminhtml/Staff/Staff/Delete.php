<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Appadmin\Controller\Adminhtml\Staff\Staff;

/**
 * class \Magestore\Appadmin\Controller\Adminhtml\Staff\Staff\Delete
 *
 * Delete user
 * Methods:
 *  execute
 *
 * @category    Magestore
 * @package     Magestore\Appadmin\Controller\Adminhtml\Staff\Staff
 * @module      Appadmin
 * @author      Magestore Developer
 */
/**
 * Class Delete
 * @package Magestore\Appadmin\Controller\Adminhtml\Staff\Staff
 */
class Delete extends \Magestore\Appadmin\Controller\Adminhtml\Staff\Staff
{
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $userId = $this->getRequest()->getParam('id');
        if ($userId > 0) {
            try {
                $this->staffRepository->deleteById($this->getRequest()->getParam('id'));
                $this->messageManager->addSuccessMessage(__('Staff was successfully deleted'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['_current' => true]);
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
