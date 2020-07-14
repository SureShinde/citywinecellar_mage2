<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Controller\Adminhtml\Staff\Role;

/**
 * class \Magestore\Appadmin\Controller\Adminhtml\Staff\Role\Edit
 *
 * Edit role
 * Methods:
 * execute
 *
 * @category    Magestore
 * @package     Magestore\Appadmin\Controller\Adminhtml\Staff\Role
 * @module      Appadmin
 * @author      Magestore Developer
 */
class Edit extends \Magestore\Appadmin\Controller\Adminhtml\Staff\Role
{

    /**
     * @return $this|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $roleId = (int)$this->getRequest()->getParam('id');
        $role = $this->_roleFactory->create()->load($roleId);
        // If the role id is exist and the role id no longer exists in the database.
        // redirect to the roles list
        if ($roleId && !$role->getRoleId()) {
            $this->messageManager->addErrorMessage(__('This Role no longer exists.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        }

        $formData = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($formData)) {
            $role->setData($formData);
        }

        $this->registry->register('current_role', $role);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_Webpos::pos');
        if (!$role->getId()) {
            $pageTitle = __('New Role');
        } else {
            $pageTitle = __('Edit Role %1', $role->getName());
        }

        $resultPage->getConfig()->getTitle()->prepend($pageTitle);
        return $resultPage;
    }

}
