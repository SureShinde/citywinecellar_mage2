<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposZippay\Model\Data;
use Magestore\WebposZippay\Api\Data\ZippayRefundRequestInterface;

/**
 * Interface ZippayServiceInterface
 * @package Magestore\WebposZippay\Model\Data
 */
class ZippayRefundRequest extends \Magento\Framework\DataObject implements ZippayRefundRequestInterface
{
    public function getCreditmemo()
    {
        return $this->_getData(self::CREDITMEMO);
    }

    public function setCreditmemo($creditmemo)
    {
        return $this->setData(self::CREDITMEMO, $creditmemo);
    }

}
