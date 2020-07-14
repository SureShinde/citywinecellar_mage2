<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Model\PackRequest;

use Magento\Framework\Model\AbstractModel;
use Magestore\FulfilSuccess\Api\Data\PackRequestItemInterface;

class PackRequestItem extends AbstractModel implements PackRequestItemInterface
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequestItem');
    }

    /**
     * @inheritDoc
     */
    public function getPackRequestItemId()
    {
        return $this->getData(self::PACK_REQUEST_ITEM_ID);
    }

    /**
     * @inheritDoc
     */
    public function setPackRequestItemId($packRequestItemId)
    {
        return $this->setData(self::PACK_REQUEST_ITEM_ID, $packRequestItemId);
    }

    /**
     * @inheritDoc
     */
    public function getPackRequestId()
    {
        return $this->getData(self::PACK_REQUEST_ID);
    }

    /**
     * @inheritDoc
     */
    public function setPackRequestId($packRequestId)
    {
        return $this->setData(self::PACK_REQUEST_ID, $packRequestId);
    }

    /**
     * @inheritDoc
     */
    public function getItemId()
    {
        return $this->getData(self::ITEM_ID);
    }

    /**
     * @inheritDoc
     */
    public function setItemId($itemId)
    {
        return $this->setData(self::ITEM_ID, $itemId);
    }
    
    /**
     * @inheritDoc
     */
    public function getParentItemId()
    {
        return $this->getData(self::PARENT_ITEM_ID);
    }

    /**
     * @inheritDoc
     */
    public function setParentItemId($itemId)
    {
        return $this->setData(self::PARENT_ITEM_ID, $itemId);
    }
    

    /**
     * @inheritDoc
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * @inheritDoc
     */
    public function getItemSku()
    {
        return $this->getData(self::ITEM_SKU);
    }

    /**
     * @inheritDoc
     */
    public function setItemSku($itemSku)
    {
        return $this->setData(self::ITEM_SKU, $itemSku);
    }

    /**
     * @inheritDoc
     */
    public function getItemName()
    {
        return $this->getData(self::ITEM_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setItemName($itemName)
    {
        return $this->setData(self::ITEM_NAME, $itemName);
    }

    /**
     * @inheritDoc
     */
    public function getItemBarcode()
    {
        return $this->getData(self::ITEM_BARCODE);
    }

    /**
     * @inheritDoc
     */
    public function setItemBarcode($barcode)
    {
        return $this->setData(self::ITEM_BARCODE, $barcode);
    }

    /**
     * @inheritDoc
     */
    public function getRequestQty()
    {
        return $this->getData(self::REQUEST_QTY);
    }

    /**
     * @inheritDoc
     */
    public function setRequestQty($requestQty)
    {
        return $this->setData(self::REQUEST_QTY, $requestQty);
    }

    /**
     * @inheritDoc
     */
    public function getPackedQty()
    {
        return $this->getData(self::PACKED_QTY);
    }

    /**
     * @inheritDoc
     */
    public function setPackedQty($packedQty)
    {
        return $this->setData(self::PACKED_QTY, $packedQty);
    }

}