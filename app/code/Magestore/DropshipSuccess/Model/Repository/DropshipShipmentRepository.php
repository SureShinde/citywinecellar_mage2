<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Model\Repository;

use Magestore\DropshipSuccess\Api\Data;
use Magestore\DropshipSuccess\Api\DropshipShipmentRepositoryInterface;
use Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\DropshipShipment as ResourceDropshipShipment;
use Magestore\DropshipSuccess\Model\DropshipRequest\DropshipShipmentFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class DropshipShipmentRepository
 * @package Magestore\DropshipSuccess\Model\Repository
 */
class DropshipShipmentRepository implements DropshipShipmentRepositoryInterface
{
    /**
     * @var ResourceDropshipShipment
     */
    protected $resourceDropshipShipment;

    /**
     * @var DropshipShipmentFactory
     */
    protected $dropshipShipmentFactory;

    /**
     * DropshipShipmentRepository constructor.
     * @param ResourceDropshipShipment $resource
     * @param DropshipShipmentFactory $dropshipShipmentFactory
     */
    public function __construct(
        ResourceDropshipShipment $resource,
        DropshipShipmentFactory $dropshipShipmentFactory
    ) {
        $this->resourceDropshipShipment = $resource;
        $this->dropshipShipmentFactory = $dropshipShipmentFactory;
    }

    /**
     * Save dropship shipment.
     *
     * @param \Magestore\DropshipSuccess\Api\Data\DropshipShipmentInterface $dropshipShipment
     * @return \Magestore\DropshipSuccess\Api\Data\DropshipShipmentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\DropshipShipmentInterface $dropshipShipment)
    {
        try {
            $this->resourceDropshipShipment->save($dropshipShipment);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $dropshipShipment;
    }

    /**
     * Retrieve dropship shipment.
     *
     * @param int $id
     * @return \Magestore\DropshipSuccess\Api\Data\DropshipShipmentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($id)
    {
        $dropshipShipment = $this->dropshipShipmentFactory->create();
        $this->resourceDropshipShipment->load($dropshipShipment, $id);
        if (!$dropshipShipment->getId()) {
            throw new NoSuchEntityException(__('Dropship shipment with id "%1" does not exist.', $id));
        }
        return $dropshipShipment;
    }

    /**
     * Delete dropship shipment.
     *
     * @param \Magestore\DropshipSuccess\Api\Data\DropshipShipmentInterface $dropshipShipment
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\DropshipShipmentInterface $dropshipShipment)
    {
        try {
            $this->resourceDropshipShipment->delete($dropshipShipment);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete dropship shipment by ID.
     *
     * @param int $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id)
    {
        return $this->delete($this->get($id));
    }
}
