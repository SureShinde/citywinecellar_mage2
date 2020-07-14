<?php

namespace Magestore\TransferStock\Api;

interface TransferManagementInterface {
    /**
     * @param int $transferId
     * @return array
     */
    public function startToSendStock($transferId);

    /**
     * @param int $transferId
     * @return \Magestore\TransferStock\Api\Data\InventoryTransfer\InventoryTransferProductInterface[]
     */
    public function getProductFromInventoryTransfer($transferId);

    /**
     * @param int $transferId
     * @return bool
     */
    public function deleteProductsInInventoryTransfer($transferId);

    /**
     * @param int $transferId
     * @param array $items
     * @return bool
     */
    public function setProductsForInventoryTransfer($transferId, $items);

    /**
     * @param array $productIds
     * @return mixed
     */
    public function getSelectBarcodeProductListJson($productIds = []);

    /**
     * @param int $transferId
     * @return mixed
     */
    public function getSelectBarcodeReceivingProductListJson($transferId);

    /**
     * @param int $transferId
     * @param array $items
     * @return bool
     */
    public function addProductsToInventoryTransfer($transferId, $items);

    /**
     * @param int $transferId
     * @param array $items
     * @return bool
     */
    public function receiveProducts($transferId, $items);

    /**
     * @param int $transferId
     * @return bool
     */
    public function receiveAllProducts($transferId);
}