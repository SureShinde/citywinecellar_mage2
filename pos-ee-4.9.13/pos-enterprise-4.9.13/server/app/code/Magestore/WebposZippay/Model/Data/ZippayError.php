<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposZippay\Model\Data;
use Magestore\WebposZippay\Api\Data\ZippayErrorInterface;

/**
 * Interface ZippayServiceInterface
 * @package Magestore\WebposZippay\Model\Data
 */
class ZippayError extends \Magento\Framework\DataObject implements ZippayErrorInterface
{
    public function getCode()
    {
        return $this->_getData(self::CODE);
    }

    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
    }

    public function getMessage()
    {
        return $this->_getData(self::MESSAGE);
    }

    public function setMessage($message)
    {
       return $this->setData(self::MESSAGE, $message);
    }

    public function getItems()
    {
        return $this->_getData(self::ITEMS);
    }

    public function setItems($items)
    {
        return $this->setData(self::ITEMS, $items);
    }

}
