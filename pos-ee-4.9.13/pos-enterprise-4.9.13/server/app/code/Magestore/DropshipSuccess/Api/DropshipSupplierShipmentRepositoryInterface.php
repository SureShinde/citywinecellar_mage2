<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Api;

/**
 * Interface DropshipSupplierShipmentRepositoryInterface
 * @package Magestore\DropshipSuccess\Api
 */
interface DropshipSupplierShipmentRepositoryInterface
{
    /**
     * Save dropship supplier shipment.
     *
     * @param \Magestore\DropshipSuccess\Api\Data\DropshipSupplierShipmentInterface $dropshipSupplierShipment
     * @return \Magestore\DropshipSuccess\Api\Data\DropshipSupplierShipmentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magestore\DropshipSuccess\Api\Data\DropshipSupplierShipmentInterface $dropshipSupplierShipment);
}