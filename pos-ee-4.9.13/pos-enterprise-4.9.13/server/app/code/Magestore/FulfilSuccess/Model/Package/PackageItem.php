<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Model\Package;

use Magento\Framework\Model\AbstractModel;
use Magestore\FulfilSuccess\Api\Data\PackageItemInterface;

class PackageItem extends AbstractModel implements PackageItemInterface
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\FulfilSuccess\Model\ResourceModel\Package\PackageItem');
    }

    /**
     * @inheritDoc
     */
    public function getPackageItemId()
    {
        return $this->getData(self::PACKAGE_ITEM_ID);
    }

    /**
     * @inheritDoc
     */
    public function setPackageItemId($packageItemId)
    {
        return $this->setData(self::PACKAGE_ITEM_ID, $packageItemId);
    }

    /**
     * @inheritDoc
     */
    public function getPackageId()
    {
        return $this->getData(self::PACKAGE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setPackageId($packageId)
    {
        return $this->setData(self::PACKAGE_ID, $packageId);
    }

    /**
     * @inheritDoc
     */
    public function getQty()
    {
        return $this->getData(self::QTY);
    }

    /**
     * @inheritDoc
     */
    public function setQty($qty)
    {
        return $this->setData(self::QTY, $qty);
    }

    /**
     * @inheritDoc
     */
    public function getCustomsValue()
    {
        return $this->getData(self::CUSTOMS_VALUE);
    }

    /**
     * @inheritDoc
     */
    public function setCustomsValue($customsValue)
    {
        return $this->setData(self::CUSTOMS_VALUE, $customsValue);
    }

    /**
     * @inheritDoc
     */
    public function getPrice()
    {
        return $this->getData(self::PRICE);
    }

    /**
     * @inheritDoc
     */
    public function setPrice($price)
    {
        return $this->setData(self::PRICE, $price);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function getWeight()
    {
        return $this->getData(self::WEIGHT);
    }

    /**
     * @inheritDoc
     */
    public function setWeight($weight)
    {
        return $this->setData(self::WEIGHT, $weight);
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
    public function getOrderItemId()
    {
        return $this->getData(self::ORDER_ITEM_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOrderItemId($orderItemId)
    {
        return $this->setData(self::ORDER_ITEM_ID, $orderItemId);
    }

}