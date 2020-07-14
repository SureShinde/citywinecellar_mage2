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
class Save extends \Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer\InventoryTransfer
{
    /**
     * History action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if (!isset($data['inventorytransfer_id'])) {
            $data['status'] = \Magestore\TransferStock\Model\InventoryTransfer\Option\Status::STATUS_OPEN;
            $data['stage'] = \Magestore\TransferStock\Model\InventoryTransfer\Option\Stage::STAGE_NEW;
            $curUser = $this->authSession->getUser();
            $data['created_by'] = $curUser->getUserName();
        }

        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }

        $model = $this->inventoryTransferFactory->create();
        $model->setData($data);
        try {
            $this->inventoryTransferResource->save($model);
            if (!$model->getData('inventorytransfer_code')) {
                $id = $model->getId();
                $inventoryTransferCode = 'TRA' . str_pad($id, 8, '0', STR_PAD_LEFT);
                $model->setData('inventorytransfer_code', $inventoryTransferCode);
                $this->inventoryTransferResource->save($model);
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }

        if ($this->getRequest()->getParam('back') == 'edit') {
            return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
        }
        return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
    }
}
