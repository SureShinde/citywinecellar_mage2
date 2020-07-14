<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest;

/**
 * Class DropshipShipment
 * @package Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest
 */
class DropshipShipment extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('os_dropship_shipment', 'dropship_shipment_id');
    }
}