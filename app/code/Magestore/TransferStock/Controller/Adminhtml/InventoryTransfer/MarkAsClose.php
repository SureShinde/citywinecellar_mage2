<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer;

use Magestore\TransferStock\Model\InventoryTransfer\Option\Status;

/**
 * Class Index
 * @package Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer
 */
class MarkAsClose extends \Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer\InventoryTransfer
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $transferId = $this->getRequest()->getParam('id');

        /** @var \Magestore\TransferStock\Model\InventoryTransfer $transferStock */
        $transferStock = $this->inventoryTransferFactory->create()->load($transferId);

        if(!$transferStock->getInventorytransferId()) {
            $this->messageManager->addErrorMessage(__('Could not found transfer with ID %1', $transferId));
            return $resultRedirect->setPath('*/*/edit', ['id' => $transferId]);
        }

        $transferStock->setStatus(Status::STATUS_CLOSED)->save();
        $this->messageManager->addSuccessMessage(__('The transfer %1 has been closed.', $transferStock->getInventorytransferCode()));

        return $resultRedirect->setPath('*/*/edit', ['id' => $transferId]);
    }
}
