<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposZippay\Model\Data;
use Magestore\WebposZippay\Api\Data\ZippayCancelRequestInterface;

/**
 * Interface ZippayServiceInterface
 * @package Magestore\WebposZippay\Model\Data
 */
class ZippayCancelRequest extends \Magento\Framework\DataObject implements ZippayCancelRequestInterface
{

    public function getRefCode()
    {
        return $this->_getData(self::REF_CODE);
    }

    public function setRefCode($ref_code)
    {
        return $this->setData(self::REF_CODE, $ref_code);
    }

    public function getOriginator()
    {
        return $this->_getData(self::ORIGINATOR);
    }

    public function setOriginator($originator)
    {
        return $this->setData(self::ORIGINATOR, $originator);
    }


}
