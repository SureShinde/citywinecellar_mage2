<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Config\Data;
/**
 * Class SystemConfig
 * @package Magestore\Webpos\Model\Config\Data
 */
class Shipping extends \Magento\Framework\DataObject implements \Magestore\Webpos\Api\Data\Config\ShippingInterface
{
    /**
     * @inheritdoc
     */
    public function getShippingMethods(){
        return $this->getData(self::SHIPPING_METHODS);
    }

    /**
     * @inheritdoc
     */
    public function setShippingMethods($shippingMethod){
        return $this->setData(self::SHIPPING_METHODS, $shippingMethod);
    }

    /**
     * @inheritdoc
     */
    public function getDeliveryDate(){
        return $this->getData(self::DELIVERY_DATE);
    }

    /**
     * @inheritdoc
     */
    public function setDeliveryDate($deliveryDate){
        return $this->setData(self::DELIVERY_DATE, $deliveryDate);
    }

    /**
     * @inheritDoc
     */
    public function getDefaultShippingTitle(){
        return $this->getData(self::DEFAULT_SHIPPING_TITLE);
    }

    /**
     * @inheritDoc
     */
    public function setDefaultShippingTitle($defaultShippingTitle){
        return $this->setData(self::DEFAULT_SHIPPING_TITLE, $defaultShippingTitle);
    }
}