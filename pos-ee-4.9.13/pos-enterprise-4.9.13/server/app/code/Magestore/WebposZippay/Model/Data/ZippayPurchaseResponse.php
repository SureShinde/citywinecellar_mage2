<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposZippay\Model\Data;
use Magestore\WebposZippay\Api\Data\ZippayPurchaseResponseInterface;

/**
 * Interface ZippayServiceInterface
 * @package Magestore\WebposZippay\Model\Data
 */
class ZippayPurchaseResponse extends \Magento\Framework\DataObject implements ZippayPurchaseResponseInterface
{
    public function getId()
    {
        return $this->_getData(self::ID);
    }

    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    public function getLocationId()
    {
        return $this->_getData(self::LOCATION_ID);    }

    public function setLocationId($location_id)
    {
        return $this->setData(self::LOCATION_ID, $location_id);
    }

    public function getStatus()
    {
        return $this->_getData(self::STATUS);
    }

    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    public function getRefCode()
    {
        return $this->_getData(self::REF_CODE);
    }

    public function setRefCode($ref_code)
    {
        return $this->setData(self::REF_CODE, $ref_code);
    }

    public function getReason()
    {
        return $this->_getData(self::REASON);
    }

    public function setReason($reason)
    {
        return $this->setData(self::REASON, $reason);
    }

    public function getReceiptNumber()
    {
        return $this->_getData(self::RECEIPT_NUMBER);
    }

    public function setReceiptNumber($receipt_number)
    {
        return $this->setData(self::RECEIPT_NUMBER, $receipt_number);
    }

    public function getError()
    {
        return $this->_getData(self::ERROR);
    }

    public function setError($error)
    {
        return $this->setData(self::ERROR, $error);
    }


}
