<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Api\Data;


interface ShipmentInterface extends \Magento\Sales\Api\Data\ShipmentInterface
{
    CONST FULFIL_STATUS = 'fulfil_status';

    CONST PACKAGE_TYPE_READY_TO_SHIP = 'readytoship';

    CONST PACKAGE_TYPE_SHIPPED = 'shipped';

    CONST NOT_GIVE_CARRIER = 0;

    CONST GIVE_CARRIER = 1;
    /**
     * @return int
     */
    public function getFulfilStatus();

    /**
     * @param $fulfilStatus int
     * @return $this
     */
    public function setFulfilStatus($fulfilStatus);
}