<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer;

use Magestore\TransferStock\Model\InventoryTransfer\Option\Stage;

/**
 * Class Index
 * @package Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer
 */
class Update extends \Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer\InventoryTransfer
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
        $isGenerallyUpdate = true;

        /** @var \Magestore\TransferStock\Model\InventoryTransfer $model */
        $model = $this->inventoryTransferFactory->create();
        $model->load($data['transfer_summary']['general_information']['inventorytransfer_id']);
        if(!$model->getId()) {
            $this->messageManager->addErrorMessage(__('Could not find inventory transfer request with ID: %1', $data['transfer_summary']['general_information']['inventorytransfer_id']));
        }
        try {
            $model->setReason($data['transfer_summary']['general_information']['reason']);
            $this->inventoryTransferResource->save($model);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }

        // save transfer items
        if(
            isset($data['transfer_summary']['product_list']['product_dynamic_grid'])
            && $model->getStage() == Stage::STAGE_NEW
        ) {
            $this->transferManagement->setProductsForInventoryTransfer($model->getId(), json_decode($data['transfer_summary']['product_list']['product_dynamic_grid']['links']['product_list'], true));
        }

        if ($this->getRequest()->getParam('back') == 'send') {
            $sendResult = $this->transferManagement->startToSendStock($model->getId());
            if($sendResult['status']) {
                $this->messageManager->addSuccessMessage($sendResult['message']);
            } else {
                $this->messageManager->addErrorMessage($sendResult['message']);
            }
            $isGenerallyUpdate = false;
        }

        if (
            ($model->getStage() == Stage::STAGE_SENT || $model->getStage() == Stage::STAGE_RECEIVING)
            && isset($data['transfer_summary']['receive_modal']['receive_product_list']['receive_product_dynamic_grid'])
        ) {
            $this->receiveProducts(
                $model->getId(),
                json_decode($data['transfer_summary']['receive_modal']['receive_product_list']['receive_product_dynamic_grid']['links']['product_list'], true)
            );
            $isGenerallyUpdate = false;
        }

        if($isGenerallyUpdate) {
            $this->messageManager->addSuccessMessage(__('General information was saved successfully.'));
        }

        if ($this->getRequest()->getParam('back') == 'edit') {
            return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
        }
        return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
    }

    /**
     * Receive products
     *
     * @param $transferId
     * @param $productList
     */
    public function receiveProducts($transferId, $productList)
    {
        $dataReceive = [];
        foreach ($productList as $item) {
            $dataReceive[] = [
                'product_id' => $item['id'],
                'product_sku' => $item['sku'],
                'product_name' => $item['name'],
                'qty' => $item['qty_to_receive']
            ];
        }

        if ($this->transferManagement->receiveProducts($transferId, $dataReceive)) {
            $this->messageManager->addSuccessMessage(__('Receiving was created successfully!'));
        } else {
            $this->messageManager->addErrorMessage(__('Cannot receive products in this inventory transfer.'));
        }
    }
}
