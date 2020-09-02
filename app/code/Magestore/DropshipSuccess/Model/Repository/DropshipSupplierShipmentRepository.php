<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Model\Repository;

use Magestore\DropshipSuccess\Api\Data;
use Magestore\DropshipSuccess\Api\DropshipSupplierShipmentRepositoryInterface;
use Magestore\DropshipSuccess\Model\ResourceModel\Supplier\Shipment as ResourceDropshipSupplierShipment;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class BlockRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DropshipSupplierShipmentRepository implements DropshipSupplierShipmentRepositoryInterface
{
    /**
     * @var ResourceDropshipRequest|ResourceDropshipSupplierShipment
     */
    protected $resourceDropshipSupplierShipment;


    /**
     * DropshipSupplierShipmentRepository constructor.
     * @param ResourceDropshipSupplierShipment $resource
     */
    public function __construct(
        ResourceDropshipSupplierShipment $resource
    ) {
        $this->resourceDropshipSupplierShipment = $resource;
    }

    /**
     * Save dropship request.
     *
     * @param \Magestore\DropshipSuccess\Api\Data\DropshipSupplierShipmentInterface $dropshipSupplierShipment
     * @return \Magestore\DropshipSuccess\Api\Data\DropshipSupplierShipmentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\DropshipSupplierShipmentInterface $dropshipSupplierShipment)
    {
        try {
            $this->resourceDropshipSupplierShipment->save($dropshipSupplierShipment);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $dropshipSupplierShipment;
    }
}
