<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Api\Data;

interface ReturnOrderItemInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */

    const RETURN_ITEM_ID = 'return_item_id';

    const RETURN_ID = 'return_id';

    const PRODUCT_ID = 'product_id';

    const PRODUCT_SKU = 'product_sku';

    const PRODUCT_NAME = 'product_name';

    const PRODUCT_SUPPLIER_SKU = 'product_supplier_sku';

    const QTY_TRANSFERRED = 'qty_transferred';

    const QTY_RETURNED = 'qty_returned';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    /**#@-*/

    /**
     * Get return item id
     *
     * @return int
     */
    public function getReturnItemId();

    /**
     * Set return item id
     *
     * @param int $returnItemId
     * @return $this
     */
    public function setReturnItemId($returnItemId);

    /**
     * Get return id
     *
     * @return int
     */
    public function getReturnId();

    /**
     * Set return id
     *
     * @param int $returnId
     * @return $this
     */
    public function setReturnId($returnId);

    /**
     * Get product id
     *
     * @return int
     */
    public function getProductId();

    /**
     * Set product id
     *
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId);

    /**
     * Get product sku
     *
     * @return string
     */
    public function getProductSku();

    /**
     * Set product sku
     *
     * @param string $productSku
     * @return $this
     */
    public function setProductSku($productSku);

    /**
     * Get product name
     *
     * @return string
     */
    public function getProductName();

    /**
     * Set product name
     *
     * @param string $productName
     * @return $this
     */
    public function setProductName($productName);

    /**
     * Get product supplier sku
     *
     * @return string
     */
    public function getProductSupplierSku();

    /**
     * Set product supplier sku
     *
     * @param string $productSupplierSku
     * @return $this
     */
    public function setProductSupplierSku($productSupplierSku);

    /**
     * Get qty transferred
     *
     * @return float
     */
    public function getQtyTransferred();

    /**
     * Set qty transferred
     *
     * @param float $qtyTransferred
     * @return $this
     */
    public function setQtyTransferred($qtyTransferred);

    /**
     * Get qty returned
     *
     * @return float
     */
    public function getQtyReturned();

    /**
     * Set qty returned
     *
     * @param float $qtyReturned
     * @return $this
     */
    public function setQtyReturned($qtyReturned);

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set created at
     *
     * @param string|null $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Get updated at
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * Set updated at
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);
}