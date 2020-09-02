<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Service;

/**
 * Class DropshipSupplierShipmentService
 * @package Magestore\DropshipSuccess\Service
 */
class DropshipSupplierShipmentService
{

    /**
     * @var \Magestore\DropshipSuccess\Model\ResourceModel\Supplier\Shipment\CollectionFactory
     */
    protected $supplierShipmentCollectionFactory;

    /**
     * DropshipSupplierShipmentService constructor.
     * @param \Magestore\DropshipSuccess\Model\ResourceModel\Supplier\Shipment\CollectionFactory $supplierShipmentCollectionFactory
     */
    public function __construct(
        \Magestore\DropshipSuccess\Model\ResourceModel\Supplier\Shipment\CollectionFactory $supplierShipmentCollectionFactory
    ) {
        $this->supplierShipmentCollectionFactory = $supplierShipmentCollectionFactory;
    }

    /**
     * get supplier shipment
     * @param $shipmentId
     * @return \Magento\Framework\DataObject
     */
    public function getSupplierShipmentByShipment($shipmentId) {
        /** @var \Magestore\DropshipSuccess\Model\ResourceModel\Supplier\Shipment\Collection $supplierShipmentCollection */
        $supplierShipmentCollection = $this->supplierShipmentCollectionFactory->create();
        return $supplierShipmentCollection->addFieldToFilter('shipment_id', $shipmentId)
            ->setCurPage(1)
            ->setPageSize(1)
            ->getFirstItem();
    }
}