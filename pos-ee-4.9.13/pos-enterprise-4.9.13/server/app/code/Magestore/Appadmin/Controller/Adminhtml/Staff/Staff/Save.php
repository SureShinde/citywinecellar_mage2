<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Controller\Adminhtml\Staff\Staff;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;

/**
 * class \Magestore\Appadmin\Controller\Adminhtml\Staff\Staff\Save
 *
 * Save user
 * Methods:
 *  execute
 *
 * @category    Magestore
 * @package     Magestore\Appadmin\Controller\Adminhtml\Staff\Staff
 * @module      Appadmin
 * @author      Magestore Developer
 */

/**
 * Class \Magestore\Appadmin\Controller\Adminhtml\Staff\Staff\Save
 */
class Save extends \Magestore\Appadmin\Controller\Adminhtml\Staff\Staff implements HttpPostActionInterface
{

    /**
     * Execute save
     *
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $modelId = (int)$this->getRequest()->getParam('staff_id');
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }
        $email = $data['email'];
        if ($email) {
            $findEmailExist = $this->staffInterfaceFactory->create()->load($email, 'email');
            if ($findEmailExist->getId() && $findEmailExist->getId() != $modelId) {
                $this->messageManager->addErrorMessage(__('Email %1 already exists.', $email));
                return $resultRedirect->setPath(
                    '*/*/edit',
                    ['id' => $this->getRequest()->getParam('staff_id')]
                );
            }
        }
        $username = $data['username'];
        if ($username) {
            $findUsernameExist = $this->staffInterfaceFactory->create()->load($username, 'username');
            if ($findUsernameExist->getId() && $findUsernameExist->getId() != $modelId) {
                $this->messageManager->addErrorMessage(__('Username already exists.', $username));
                return $resultRedirect->setPath(
                    '*/*/edit',
                    ['id' => $this->getRequest()->getParam('staff_id')]
                );
            }
        }

        if ($modelId) {
            $model = $this->staffRepository->getById($modelId);
        } else {
            $model = $this->staffInterfaceFactory->create();
        }

        if (isset($data['password']) && $data['password'] == '') {
            unset($data['password']);
        } else {
            $data['new_password'] = $data['password'];
        }

        if ($data['location_ids']) {
            $data['location_ids'] = implode(',', $data['location_ids']);
        } else {
            $data['location_ids'] = '';
        }

        $model->setData($data);
        if ($model->hasNewPassword() && $model->getNewPassword() === '') {
            $model->unsNewPassword();
        }
        if ($model->hasPasswordConfirmation() && $model->getPasswordConfirmation() === '') {
            $model->unsPasswordConfirmation();
        }
        $result = $model->validate(); /* validate data */
        if (is_array($result)) {
            foreach ($result as $message) {
                $this->messageManager->addErrorMessage($message);
            }
            $this->_redirect('*/*/edit', ['_current' => true]);
            return $resultRedirect->setPath('*/*/');
        }
        try {
            $this->staffRepository->save($model);
            $this->messageManager->addSuccessMessage(__('Staff was successfully saved'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('staff_id')]);
        }
        if ($this->getRequest()->getParam('back') == 'edit') {
            return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
