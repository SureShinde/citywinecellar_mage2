<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Api\Data;

interface PurchaseOrderItemInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    
    const PURCHASE_ORDER_ITEM_ID = 'purchase_order_item_id';

    const PURCHASE_ORDER_ID = 'purchase_order_id';

    const PRODUCT_ID = 'product_id';

    const PRODUCT_SKU = 'product_sku';

    const PRODUCT_NAME = 'product_name';

    const PRODUCT_SUPPLIER_SKU = 'product_supplier_sku';

    const QTY_ORDERRED = 'qty_orderred';

    const QTY_RECEIVED = 'qty_received';

    const QTY_TRANSFERRED = 'qty_transferred';

    const QTY_RETURNED = 'qty_returned';

    const QTY_BILLED = 'qty_billed';

    const ORIGINAL_COST = 'original_cost';

    const COST = 'cost';

    const TAX = 'tax';

    const DISCOUNT = 'discount';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    /**#@-*/

    /**
     * Get purchase order item id
     *
     * @return int
     */
    public function getPurchaseOrderItemId();

    /**
     * Set purchase order item id
     *
     * @param int $purchaseOrderItemId
     * @return $this
     */
    public function setPurchaseOrderItemId($purchaseOrderItemId);

    /**
     * Get purchase order id
     *
     * @return int
     */
    public function getPurchaseOrderId();

    /**
     * Set purchase order id
     *
     * @param int $purchaseOrderId
     * @return $this
     */
    public function setPurchaseOrderId($purchaseOrderId);
    
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
     * Get qty orderred
     *
     * @return float
     */
    public function getQtyOrderred();

    /**
     * Set qty orderred
     *
     * @param float $qtyOrderred
     * @return $this
     */
    public function setQtyOrderred($qtyOrderred);

    /**
     * Get qty received
     *
     * @return float
     */
    public function getQtyReceived();

    /**
     * Set qty received
     *
     * @param float $qtyReceived
     * @return $this
     */
    public function setQtyReceived($qtyReceived);

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
     * Get qty billed
     *
     * @return float
     */
    public function getQtyBilled();

    /**
     * Set qty billed
     *
     * @param float $qtyBilled
     * @return $this
     */
    public function setQtyBilled($qtyBilled);

    /**
     * Get original cost
     *
     * @return float
     */
    public function getOriginalCost();

    /**
     * Set original cost
     *
     * @param float $originalCost
     * @return $this
     */
    public function setOriginalCost($originalCost);

    /**
     * Get cost
     *
     * @return float
     */
    public function getCost();

    /**
     * Set cost
     *
     * @param float $cost
     * @return $this
     */
    public function setCost($cost);

    /**
     * Get tax
     *
     * @return float
     */
    public function getTax();

    /**
     * Set tax
     *
     * @param float $tax
     * @return $this
     */
    public function setTax($tax);

    /**
     * Get discount
     *
     * @return float
     */
    public function getDiscount();

    /**
     * Set discount
     *
     * @param float $discount
     * @return $this
     */
    public function setDiscount($discount);

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