<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Config;

/**
 * @api
 */
/**
 * Interface ConfigInterface
 * @package Magestore\Webpos\Api\Data\Config
 */
interface ShippingInterface
{
    const SHIPPING_METHODS = 'shipping_methods';
    const DELIVERY_DATE = 'delivery_date';
    const DEFAULT_SHIPPING_TITLE = 'default_shipping_title';

    /**
     * @return string
     */
    public function getShippingMethods();

    /**
     * @param string $shippingMethod
     * @return ShippingInterface
     */
    public function setShippingMethods($shippingMethod);

    /**
     * @return int
     */
    public function getDeliveryDate();

    /**
     * @param int $deliveryDate
     * @return ShippingInterface
     */
    public function setDeliveryDate($deliveryDate);

    /**
     * @return string
     */
    public function getDefaultShippingTitle();

    /**
     * @param string $defaultShippingTitle
     * @return ShippingInterface
     */
    public function setDefaultShippingTitle($defaultShippingTitle);
}