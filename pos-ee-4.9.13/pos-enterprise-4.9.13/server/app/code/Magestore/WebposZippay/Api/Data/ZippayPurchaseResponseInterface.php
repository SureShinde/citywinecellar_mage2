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
interface ZippayPurchaseResponseInterface
{
    const ID = 'id';
    const LOCATION_ID = 'locationId';
    const REF_CODE = 'refCode';
    const STATUS = 'status';
    const REASON = 'reason';
    const RECEIPT_NUMBER = 'receipt_number';
    const ERROR = 'error';


    /**
     * @return string | float | null
     */
    public function getId();

    /**
     * @param $id
     * @return ZippayPurchaseResponseInterface
     */
    public function setId($id);

    /**
     * @return string | float | null
     */
    public function getLocationId();

    /**
     * @param $location_id
     * @return ZippayPurchaseResponseInterface
     */
    public function setLocationId($location_id);

    /**
     * @return string | float | null
     */
    public function getStatus();

    /**
     * @param $status
     * @return ZippayPurchaseResponseInterface
     */
    public function setStatus($status);


    /**
     * @return string | float | null
     */
    public function getRefCode();

    /**
     * @param $ref_code
     * @return ZippayPurchaseResponseInterface
     */
    public function setRefCode($ref_code);

    /**
     * @return string | float | null
     */
    public function getReason();

    /**
     * @param $reason
     * @return ZippayPurchaseResponseInterface
     */
    public function setReason($reason);

    /**
     * @return string | float | null
     */
    public function getReceiptNumber();

    /**
     * @param $receipt_number
     * @return ZippayPurchaseResponseInterface
     */
    public function setReceiptNumber($receipt_number);

    /**
     * @return \Magestore\WebposZippay\Api\Data\ZippayErrorInterface|null
     */
    public function getError();

    /**
     * @param $error
     * @return ZippayPurchaseResponseInterface
     */
    public function setError($error);
}
