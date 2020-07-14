<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer;

/**
 * Class Edit
 * @package Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer
 */
class Edit extends \Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer\InventoryTransfer
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id', null);
        /** @var \Magestore\TransferStock\Model\InventoryTransfer $model */
        $model = $this->inventoryTransferFactory->create();
        $resultPage = $this->_resultPageFactory->create();
        if($id){
            $this->inventoryTransferResource->load($model, $id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This inventory transfer no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
            $this->_coreRegistry->register('current_inventory_transfer', $model);
            $resultPage->getConfig()->getTitle()->prepend(__('Sending "%1"', $model->getInventorytransferCode()));
        }else{
            $resultPage->getConfig()->getTitle()->prepend(__('New Stock Sending'));
        }
        return $resultPage;
    }

    /**
     * Init page.
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Magestore_TransferStock::inventorytransfer');
        return $resultPage;
    }
}
