<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Controller\Adminhtml\Staff\Role;

/**
 * class \Magestore\Appadmin\Controller\Adminhtml\Staff\Role\Delete
 *
 * Delete user
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
class Save extends \Magestore\Appadmin\Controller\Adminhtml\Staff\Role
{
    /**
     * @return $this
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getPostValue();
        $roleId = (int)$this->getRequest()->getParam('role_id');
        $role = $this->_roleFactory->create()->load($roleId);
        // If the role id is exist and the role id no longer exists in the database.
        // redirect to the roles list
        if ($roleId && !$role->getRoleId()) {
            $this->messageManager->addError(__('This Role no longer exists.'));
            return $resultRedirect->setPath('*/*/');
        }

        // add the data to save
        $role->setData($data);

        try {
            $role->save();
            $roleId = $role->getRoleId();
            $resources = array();
            if (isset($data['all']) && $data['all']) {
                $resources = array('Magestore_Appadmin::all');
            } else {
                if (isset($data['resource'])) {
                    $resources = $data['resource'];
                    /* Always save label resource to resource*/
                    $labelResources = $this->_objectManager->create('Magestore\Appadmin\Model\Staff\Acl\LabelResources')
                        ->getLabelResources();
                    foreach ($labelResources as $resource) {
                        if (!in_array($resource, $resources)) {
                            $resources[] = $resource;
                        }
                    }
                }
            }

            $authorizeRuleCollection = $this->_objectManager->create('Magestore\Appadmin\Model\Staff\AuthorizationRule')
                ->getCollection()
                ->addFieldToFilter('role_id', $roleId);
            foreach ($authorizeRuleCollection as $authorizeRule) {
                $authorizeRule->delete();
            }

            // check if resource change and not have permission to use pos
            $checkPermissionCheckout = false;
            foreach ($resources as $resource) {
                if(in_array($resource, ['Magestore_Appadmin::all', 'Magestore_Webpos::manage_pos'])) {
                    $checkPermissionCheckout = true;
                }
                $authorizeRuleCollection = $this->_objectManager->create('Magestore\Appadmin\Model\Staff\AuthorizationRule');
                $authorizeRuleCollection->setRoleId($roleId);
                $authorizeRuleCollection->setResourceId($resource);
                $authorizeRuleCollection->save();
            }

            // dispatch event to force sign out
            if(!$checkPermissionCheckout) {
                $staffs = $this->staffRepository->getByRoleId($role->getRoleId());
                foreach ($staffs as $staff) {
                    $this->dispatchService->dispatchEventForceSignOut($staff->getStaffId());
                }
            }

            $this->messageManager->addSuccessMessage(__('Role was successfully saved'));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_getSession()->setFormData($role->getData());
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultRedirect->setPath('*/*/edit', ['id' => $roleId]);
        } catch (\Exception $e) {
            $this->_getSession()->setFormData($role->getData());
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultRedirect->setPath('*/*/edit', ['id' => $roleId]);
        }

        if ($this->getRequest()->getParam('back') == 'edit') {
            return $resultRedirect->setPath('*/*/edit', ['id' => $roleId]);
        }
        return $resultRedirect->setPath('*/*/');

    }
}
