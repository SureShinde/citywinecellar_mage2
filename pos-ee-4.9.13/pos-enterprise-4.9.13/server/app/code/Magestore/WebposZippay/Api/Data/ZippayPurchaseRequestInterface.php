<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposZippay\Api\Data;

/**
 * Interface ZippayServiceInterface
 * @package Magestore\WebposZippay\Api\Data
 */
interface ZippayPurchaseRequestInterface
{
    const ORDER = 'order';
    const STORE_CODE = 'store_code';

    /**
     * @return \Magestore\Webpos\Api\Data\Checkout\OrderInterface
     */
    public function getOrder();

    /**
     * @param \Magestore\Webpos\Api\Data\Checkout\OrderInterface $order
     * @return ZippayPurchaseRequestInterface
     */
    public function setOrder($order);

    /**
     * @return string | float
     */
    public function getStoreCode();

    /**
     * @param string | float $store_code
     * @return ZippayPurchaseRequestInterface
     */
    public function setStoreCode($store_code);

}
