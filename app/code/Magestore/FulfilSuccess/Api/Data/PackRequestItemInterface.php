<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Api\Data;

interface PackRequestItemInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    CONST PACK_REQUEST_ITEM_ID = 'pack_request_item_id';

    /**
     *
     */
    CONST PACK_REQUEST_ID = 'pack_request_id';

    /**
     *
     */
    CONST ITEM_ID = 'item_id';
    CONST PARENT_ITEM_ID = 'parent_item_id';

    const PRODUCT_ID = 'product_id';
    const ITEM_SKU = 'item_sku';
    const ITEM_NAME= 'item_name';

    /**
     *
     */
    CONST REQUEST_QTY = 'request_qty';

    /**
     *
     */
    CONST PACKED_QTY = 'packed_qty';

    const ITEM_BARCODE = 'item_barcode';

    /**
     * @return int
     */
    public function getPackRequestItemId();

    /**
     * @param $packRequestItemId int
     * @return $this
     */
    public function setPackRequestItemId($packRequestItemId);

    /**
     * @return int
     */
    public function getPackRequestId();

    /**
     * @param $packRequestId int
     * @return $this
     */
    public function setPackRequestId($packRequestId);

    /**
     * @return int
     */
    public function getItemId();

    /**
     * @param $itemId int
     * @return $this
     */
    public function setItemId($itemId);
    
    /**
     * @return int
     */
    public function getParentItemId();    
    
    /**
     * @param $itemId int
     * @return $this
     */
    public function setParentItemId($itemId);

    /**
     * get Product id
     *
     * @return int
     */
    public function getProductId();

    /**
     * set Product Id
     *
     * @param int $productId
     */
    public function setProductId($productId);

    /**
     * get Item SKU
     *
     * @return string
     */
    public function getItemSku();

    /**
     * set Sku
     *
     * @param string $itemSku
     */
    public function setItemSku($itemSku);

    /**
     * get Item Name
     *
     * @return string
     */
    public function getItemName();

    /**
     * set Name
     *
     * @param string $itemName
     */
    public function setItemName($itemName);

    /**
     * @return float
     */
    public function getRequestQty();

    /**
     * @param $requestQty float
     * @return $this
     */
    public function setRequestQty($requestQty);

    /**
     * @return float
     */
    public function getPackedQty();

    /**
     * @param $packedQty float
     * @return $this
     */
    public function setPackedQty($packedQty);

    /**
     * get barcode
     *
     * @return string
     */
    public function getItemBarcode();

    /**
     * set barcode
     *
     * @param string $barcode
     */
    public function setItemBarcode($barcode);
}