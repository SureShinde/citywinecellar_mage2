<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer;

/**
 * Class Index
 * @package Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer
 */
class ReceiveAll extends \Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer\InventoryTransfer
{
    /**
     * History action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $transferId = $this->getRequest()->getParam('id');

        if($this->transferManagement->receiveAllProducts($transferId)) {
            $this->messageManager->addSuccessMessage(__('Receiving was created successfully!'));
        } else {
            $this->messageManager->addErrorMessage(__('Cannot receive all product in this inventory transfer.'));
        }

        return $resultRedirect->setPath('*/*/edit', ['id' => $transferId]);
    }
}
