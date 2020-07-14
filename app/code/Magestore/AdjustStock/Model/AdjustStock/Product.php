<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Model\AdjustStock;

use \Magento\Framework\Model\AbstractModel;
use \Magestore\AdjustStock\Api\Data\AdjustStock\ProductInterface;

/**
 * Class Product
 * @package Magestore\AdjustStock\Model\AdjustStock
 */
class Product extends AbstractModel implements ProductInterface
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct(){
        parent::_construct();
        $this->_init('Magestore\AdjustStock\Model\ResourceModel\AdjustStock\Product');
    }

    /**
     * @inheritDoc
     */
    public function getAdjuststockProductId()
    {
        return $this->getData(self::ID);
    }

    /**
     * @inheritDoc
     */
    public function setAdjuststockProductId($id)
    {
        return $this->setData(self::ID, $id);
    }


    /**
     * @inheritDoc
     */
    public function getAdjuststockId()
    {
        return $this->getData(self::ADJUSTSTOCK_ID);
    }

    /**
     * @inheritDoc
     */
    public function setAdjuststockId($adjustStockId)
    {
        return $this->setData(self::ADJUSTSTOCK_ID, $adjustStockId);
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
    public function getProductName()
    {
        return $this->getData(self::PRODUCT_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setProductName($productName)
    {
        return $this->setData(self::PRODUCT_NAME, $productName);
    }

    /**
     * @inheritDoc
     */
    public function getProductSku()
    {
        return $this->getData(self::PRODUCT_SKU);
    }

    /**
     * @inheritDoc
     */
    public function setProductSku($productSku)
    {
        return $this->setData(self::PRODUCT_SKU, $productSku);
    }

    /**
     * @inheritDoc
     */
    public function getOldQty()
    {
        return $this->getData(self::OLD_QTY);
    }

    /**
     * @inheritDoc
     */
    public function setOldQty($qty)
    {
        return $this->setData(self::OLD_QTY, $qty);
    }

    /**
     * @inheritDoc
     */
    public function getChangeQty()
    {
        return $this->getData(self::CHANGE_QTY);
    }

    /**
     * @inheritDoc
     */
    public function setChangeQty($qty)
    {
        return $this->setData(self::CHANGE_QTY, $qty);
    }

    /**
     * @inheritDoc
     */
    public function getNewQty()
    {
        return $this->getData(self::NEW_QTY);
    }

    /**
     * @inheritDoc
     */
    public function setNewQty($qty)
    {
        return $this->setData(self::NEW_QTY, $qty);
    }

    /**
     * @return string|null
     */
    public function getBarcode()
    {
        return $this->getData(self::BARCODE);
    }

    /**
     * @param string $barcode
     * @return $this
     */
    public function setBarcode($barcode)
    {
        return $this->setData(self::BARCODE, $barcode);
    }

}
