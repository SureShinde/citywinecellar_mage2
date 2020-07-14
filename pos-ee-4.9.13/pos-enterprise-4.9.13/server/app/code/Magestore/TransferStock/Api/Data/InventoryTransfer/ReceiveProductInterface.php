<?php
/**
 * Copyright © 2019 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Api\Data\InventoryTransfer;

/**
 * Interface ReceiveProductInterface
 * @package Magestore\TransferStock\Api\Data\InventoryTransfer
 */
interface ReceiveProductInterface {
    const RECEIVE_PRODUCT_ID = 'receive_product_id';
    const RECEIVE_ID = 'receive_id';
    const PRODUCT_ID = 'product_id';
    const PRODUCT_NAME = 'product_name';
    const PRODUCT_SKU = 'product_sku';
    const QTY = 'qty';


    /**
     * Get Receive Product Id
     *
     * @return int|null
     */
    public function getReceiveProductId();

    /**
     * Get Receive Id
     *
     * @return int|null
     */
    public function getReceiveId();

    /**
     * Get Product Id
     *
     * @return int|null
     */
    public function getProductId();

    /**
     * Get Product Name
     *
     * @return string|null
     */
    public function getProductName();

    /**
     * Get Product Sku
     *
     * @return string|null
     */
    public function getProductSku();

    /**
     * Get Qty
     *
     * @return float|null
     */
    public function getQty();


    /**
     * Set Receive Product Id
     *
     * @param int|null $receiveProductId
     * @return $this
     */
    public function setReceiveProductId($receiveProductId);

    /**
     * Set Receive Id
     *
     * @param int|null $receiveId
     * @return $this
     */
    public function setReceiveId($receiveId);

    /**
     * Set Product Id
     *
     * @param int|null $productId
     * @return $this
     */
    public function setProductId($productId);

    /**
     * Set Product Name
     *
     * @param string|null $productName
     * @return $this
     */
    public function setProductName($productName);

    /**
     * Set Product Sku
     *
     * @param string|null $productSku
     * @return $this
     */
    public function setProductSku($productSku);

    /**
     * Set Qty
     *
     * @param float|null $qty
     * @return $this
     */
    public function setQty($qty);
}
