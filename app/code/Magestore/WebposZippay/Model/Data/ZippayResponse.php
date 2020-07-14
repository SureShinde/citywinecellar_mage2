<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposZippay\Model\Data;
use Magestore\WebposZippay\Api\Data\ZippayResponseInterface;

/**
 * Interface ZippayServiceInterface
 * @package Magestore\WebposZippay\Model\Data
 */
class ZippayResponse extends \Magento\Framework\DataObject implements ZippayResponseInterface
{
    public function getError()
    {
        return $this->_getData(self::ERROR);
    }

    public function setError($error)
    {
        return $this->setData(self::ERROR, $error);
    }


}
