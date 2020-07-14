<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposZippay\Model\Data;
use Magestore\WebposZippay\Api\Data\ZippayErrorFieldInterface;

/**
 * Interface ZippayServiceInterface
 * @package Magestore\WebposZippay\Model\Data
 */
class ZippayErrorField extends \Magento\Framework\DataObject implements ZippayErrorFieldInterface
{
    public function getField()
    {
        return $this->_getData(self::FIELD);
    }

    public function setField($field)
    {
        return $this->_getData(self::FIELD);
    }

    public function getMessage()
    {
        return $this->_getData(self::MESSAGE);
    }

    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

}
