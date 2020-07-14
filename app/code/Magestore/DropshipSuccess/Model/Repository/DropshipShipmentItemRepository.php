<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Model\Repository;

use Magestore\DropshipSuccess\Api\Data;
use Magestore\DropshipSuccess\Api\DropshipShipmentItemRepositoryInterface;
use Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\DropshipShipment\Item as ResourceDropshipShipmentItem;
use Magestore\DropshipSuccess\Model\DropshipRequest\DropshipShipment\ItemFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class DropshipShipmentItemRepository
 * @package Magestore\DropshipSuccess\Model\Repository
 */
class DropshipShipmentItemRepository implements DropshipShipmentItemRepositoryInterface
{
    /**
     * @var ResourceDropshipShipmentItem
     */
    protected $resourceDropshipShipmentItem;

    /**
     * @var ItemFactory
     */
    protected $itemFactory;

    /**
     * DropshipShipmentItemRepository constructor.
     * @param ResourceDropshipShipmentItem $resource
     * @param ItemFactory $itemFactory
     */
    public function __construct(
        ResourceDropshipShipmentItem $resource,
        ItemFactory $itemFactory
    ) {
        $this->resourceDropshipShipmentItem = $resource;
        $this->itemFactory = $itemFactory;
    }

    /**
     * Save dropship shipment item.
     *
     * @param \Magestore\DropshipSuccess\Api\Data\DropshipShipmentItemInterface $dropshipShipmentItem
     * @return \Magestore\DropshipSuccess\Api\Data\DropshipShipmentItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\DropshipShipmentItemInterface $dropshipShipmentItem)
    {
        try {
            $this->resourceDropshipShipmentItem->save($dropshipShipmentItem);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $dropshipShipmentItem;
    }

    /**
     * Retrieve dropship shipment item.
     *
     * @param int $id
     * @return \Magestore\DropshipSuccess\Api\Data\DropshipShipmentItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($id)
    {
        $dropshipShipmentItem = $this->itemFactory->create();
        $this->resourceDropshipShipmentItem->load($dropshipShipmentItem, $id);
        if (!$dropshipShipmentItem->getId()) {
            throw new NoSuchEntityException(__('Dropship shipment with id "%1" does not exist.', $id));
        }
        return $dropshipShipmentItem;
    }

    /**
     * Delete dropship shipment item.
     *
     * @param \Magestore\DropshipSuccess\Api\Data\DropshipShipmentItemInterface $dropshipShipmentItem
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\DropshipShipmentItemInterface $dropshipShipmentItem)
    {
        try {
            $this->resourceDropshipShipmentItem->delete($dropshipShipmentItem);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete dropship shipment item by ID.
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
