<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Appadmin\Controller\Adminhtml\Staff\Staff;

/**
 * class \Magestore\Appadmin\Controller\Adminhtml\Staff\Staff\Edit
 *
 * Edit user
 * Methods:
 *  execute
 *
 * @category    Magestore
 * @package     Magestore\Appadmin\Controller\Adminhtml\Staff\Staff
 * @module      Appadmin
 * @author      Magestore Developer
 */
class Edit extends \Magestore\Appadmin\Controller\Adminhtml\Staff\Staff
{

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model = $this->staffRepository->getById($id);
        } else {
            $model = $this->staffInterfaceFactory->create();
        }
        $this->registry->register('current_staff', $model);
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_Webpos::pos');
        if (!$model->getId()) {
            $pageTitle = __('New Staff');
        } else {
            $pageTitle =  __('Edit Staff %1', $model->getName());
        }
        $resultPage->getConfig()->getTitle()->prepend($pageTitle);
        return $resultPage;
    }

}
