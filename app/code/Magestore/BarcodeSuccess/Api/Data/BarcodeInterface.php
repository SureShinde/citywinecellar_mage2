<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Api\Data;

/**
 * @api
 */
interface BarcodeInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const ID = 'id';

    const BARCODE = 'barcode';

    const QTY = 'qty';

    const PRODUCT_ID = 'product_id';

    const PRODUCT_SKU = 'product_sku';

    const SUPPLIER_ID = 'supplier_id';

    const SUPPLIER_CODE = 'supplier_code';

    const PURCHASED_ID = 'purchased_id';

    const PURCHASED_TIME = 'purchased_time';

    const HISTORY_ID = 'history_id';

    const CREATE_AT = 'created_at';

    /**#@-*/

    /**
     * Barcode id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set product id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * barcode
     *
     * @return string
     */
    public function getBarcode();

    /**
     * Set barcode
     *
     * @param string $barcode
     * @return $this
     */
    public function setBarcode($barcode);

    /**
     * barcode name
     *
     * @return string|null
     */
    public function getQty();

    /**
     * Set barcode qty
     *
     * @param string $qty
     * @return $this
     */
    public function setQty($qty);

    /**
     * barcode product id
     *
     * @return string|null
     */
    public function getProductId();

    /**
     * Set barcode product id
     *
     * @param string $productId
     * @return $this
     */
    public function setProductId($productId);

    /**
     * barcode product sku
     *
     * @return string|null
     */
    public function getProductSku();

    /**
     * Set barcode product sku
     *
     * @param string $productSku
     * @return $this
     */
    public function setProductSku($productSku);

    /**
     * barcode supplier id
     *
     * @return string|null
     */
    public function getSupplierId();

    /**
     * Set barcode supplier id
     *
     * @param string $supplierId
     * @return $this
     */
    public function setSupplierId($supplierId);

    /**
     * barcode supplier code
     *
     * @return string|null
     */
    public function getSupplierCode();

    /**
     * Set barcode supplier code
     *
     * @param string $supplierCode
     * @return $this
     */
    public function setSupplierCode($supplierCode);

    /**
     * barcode $purchasedId
     *
     * @return string|null
     */
    public function getPurchasedId();

    /**
     * Set barcode $purchasedId
     *
     * @param string $purchasedId
     * @return $this
     */
    public function setPurchasedId($purchasedId);

    /**
     * barcode $purchasedTime
     *
     * @return string|null
     */
    public function getPurchasedTime();

    /**
     * Set barcode $purchasedTime
     *
     * @param string $purchasedTime
     * @return $this
     */
    public function setPurchasedTime($purchasedTime);


    /**
     * barcode $historyId
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set barcode $createdAt
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);
}
