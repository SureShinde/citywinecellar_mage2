<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Controller\Adminhtml\Staff\Role;

/**
 * class \Magestore\Appadmin\Controller\Adminhtml\Staff\Role\Delete
 *
 * Delete Role
 * Methods:
 *  execute
 *
 * @category    Magestore
 * @package     Magestore\Appadmin\Controller\Adminhtml\Staff\Role
 * @module      Appadmin
 * @author      Magestore Developer
 */
/**
 * Class Delete
 * @package Magestore\Appadmin\Controller\Adminhtml\Staff\Role
 */
class Delete extends \Magestore\Appadmin\Controller\Adminhtml\Staff\Role
{
    /**
     * @return $this
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $roleId = $this->getRequest()->getParam('id');
        if ($roleId > 0) {
            try {
                $this->roleRepository->deleteById($roleId);
                $this->messageManager->addSuccessMessage(__('Role was successfully deleted'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['_current' => true]);
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
