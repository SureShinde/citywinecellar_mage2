<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposZippay\Api;

/**
 * Interface ZippayServiceInterface
 * @package Magestore\WebposZippay\Api
 */
interface ZippayServiceInterface
{
    /**
     * @return bool
     */
    public function isEnable();

    /**
     * @return string
     */
    public function getConfigurationError();

    /**
     * @param string|null $apiUrl
     * @param string|null $apiKey
     * @return bool
     */
    public function canConnectToApi($apiUrl = null, $apiKey = null);

    /**
     * @param string | float
     * @param \Magestore\Webpos\Api\Data\Checkout\OrderInterface
     * @return \Magestore\WebposZippay\Api\Data\ZippayPurchaseResponseInterface
     */
    public function purchaserRequests($storeCode, $order);

    /**
     * @param string | float
     * @param string | float
     * @param string | float
     * @param \Magestore\WebposZippay\Api\Data\ZippayOriginatorInterface
     * @return \Magestore\WebposZippay\Api\Data\ZippayResponseInterface | \Magestore\WebposZippay\Api\Data\ZippayErrorFieldInterface
     */
    public function purchaserRequestsRefund($id, $refCode, $refundAmount, $originator);

    /**
     * @param string | float $id
     * @return \Magestore\WebposZippay\Api\Data\ZippayPurchaseResponseInterface
     */
    public function fetchTransaction($id);

    /**
     * @param string | float $refCode
     * @param \Magestore\WebposZippay\Api\Data\ZippayOriginatorInterface $originator
     * @return \Magestore\WebposZippay\Api\Data\ZippayResponseInterface | \Magestore\WebposZippay\Api\Data\ZippayErrorFieldInterface
     */
    public function cancelPurchaserRequests($refCode, $originator);

    /**
     * @param string | float
     * @param string | float
     * @param string | float
     * @param \Magestore\WebposZippay\Api\Data\ZippayOriginatorInterface
     * @return \Magestore\WebposZippay\Api\Data\ZippayResponseInterface | \Magestore\WebposZippay\Api\Data\ZippayErrorFieldInterface
     */
    public function cancelRefundRequests($id, $refCode, $refundAmount, $originator);
}
