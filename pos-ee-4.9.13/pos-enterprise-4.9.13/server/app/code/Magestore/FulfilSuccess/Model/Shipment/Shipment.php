<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Model;

use Magestore\FulfilSuccess\Api\Data\ShipmentInterface;

class Shipment extends \Magento\Sales\Model\Order\Shipment implements ShipmentInterface
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\FulfilSuccess\Model\ResourceModel\Shipment\Shipment');
    }

    /**
     * @inheritDoc
     */
    public function getFulfilStatus()
    {
        return $this->getData(self::FULFIL_STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setFulfilStatus($fulfilStatus)
    {
        return $this->setData(self::FULFIL_STATUS, $fulfilStatus);
    }


}