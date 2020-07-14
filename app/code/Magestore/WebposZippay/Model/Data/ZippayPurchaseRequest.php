<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposZippay\Model\Data;
use Magestore\WebposZippay\Api\Data\ZippayPurchaseRequestInterface;

/**
 * Interface ZippayServiceInterface
 * @package Magestore\WebposZippay\Model\Data
 */
class ZippayPurchaseRequest extends \Magento\Framework\DataObject implements ZippayPurchaseRequestInterface
{
    public function getOrder()
    {
        return $this->getData(self::ORDER);
    }

    public function setOrder($order)
    {
        return $this->setData(self::ORDER, $order);
    }

    public function getStoreCode()
    {
        return $this->getData(self::STORE_CODE);
    }

    public function setStoreCode($store_code)
    {
        return $this->setData(self::STORE_CODE, $store_code);
    }

}
