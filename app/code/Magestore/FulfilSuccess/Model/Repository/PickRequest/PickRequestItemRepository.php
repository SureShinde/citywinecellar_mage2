<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Model\Repository\PickRequest;

use Magestore\FulfilSuccess\Api\PickRequestItemRepositoryInterface;
use Magestore\FulfilSuccess\Api\Data\PickRequestInterface;
use Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class PickRequestItemRepository implements PickRequestItemRepositoryInterface
{
    /**
     * @var \Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\PickRequestItem
     */
    protected $resource;

    /**
     * @var \Magestore\FulfilSuccess\Model\PickRequest\PickRequestItemFactory
     */
    protected $pickRequestItemFactory;

    /**
     * @var \Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\PickRequestItem\CollectionFactory
     */
    protected $collectionFactory;

    public function __construct(
        \Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\PickRequestItem $resource,
        \Magestore\FulfilSuccess\Model\PickRequest\PickRequestItemFactory $pickRequestItemFactory,
        \Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\PickRequestItem\CollectionFactory $collectionFactory
    )
    {
        $this->resource = $resource;
        $this->pickRequestItemFactory = $pickRequestItemFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Save PickRequestItem.
     *
     * @param \Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface $pickRequestItem
     * @return \Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(PickRequestItemInterface $pickRequestItem)
    {
        try {
            $this->resource->save($pickRequestItem);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $pickRequestItem;
    }

    /**
     * Retrieve BatchPickRequest.
     *
     * @param int $pickRequestItemId
     * @return \Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($pickRequestItemId)
    {
        $pickRequestItem = $this->pickRequestItemFactory->create();
        $this->resource->load($pickRequestItem, $pickRequestItemId);
        if (!$pickRequestItem->getId()) {
            throw new NoSuchEntityException(__('The picking request item with ID "%1" does not exist.', $pickRequestItemId));
        }
        return $pickRequestItem;
    }

    /**
     * Get list by Product Id
     *
     * @param int $productId
     * @retrurn \Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface[]
     */
    public function getListByProductId($productId)
    {
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter(PickRequestItemInterface::PRODUCT_ID, $productId);
        return $collection->getItems();
    }

    /**
     * Get list by Product Ids
     *
     * @param array $productIds
     * @retrurn \Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface[]
     */
    public function getListByProductIds($productIds)
    {
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter(PickRequestItemInterface::PRODUCT_ID, ['in' => $productIds])
            ->joinPickRequest([PickRequestInterface::WAREHOUSE_ID]);
        return $collection->getItems();
    }

    /**
     * Get picking item list
     *
     * @param array $productIds
     * @retrurn \Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface[]
     */
    public function getPickingList($productIds = [])
    {
        $collection = $this->collectionFactory->create();
        if (count($productIds)) {
            $collection->addFieldToFilter(PickRequestItemInterface::PRODUCT_ID, ['in' => $productIds]);
        }
        $collection->joinPickRequest(
            [PickRequestInterface::WAREHOUSE_ID, PickRequestInterface::STATUS, PickRequestInterface::SOURCE_CODE]
        );
        $collection->addFieldToFilter(PickRequestInterface::STATUS, PickRequestInterface::STATUS_PICKING);
        return $collection->getItems();
    }

    /**
     * Delete PickRequestItem.
     *
     * @param \Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface $pickRequestItem
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(PickRequestItemInterface $pickRequestItem)
    {
        try {
            $this->resource->delete($pickRequestItem);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete PickRequestItem by ID.
     *
     * @param int $pickRequestItemId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($pickRequestItemId)
    {
        return $this->delete($this->getById($pickRequestItemId));
    }

    /**
     * @param string $pickRequestId
     * @return \Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface[]
     */
    public function getListByRequestId($pickRequestId)
    {
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter(PickRequestItemInterface::PICK_REQUEST_ID, $pickRequestId);
        return $collection->getItems();
    }

    /**
     * @inheritDoc
     */
    public function getByRequestIdAndItemId($pickRequestId, $itemId)
    {
        /** @var \Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\PickRequestItem\Collection $collection */
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter(PickRequestItemInterface::PICK_REQUEST_ID, $pickRequestId)
            ->addFieldToFilter(PickRequestItemInterface::ITEM_ID, $itemId);
        if ($collection->getSize() == 0) {
            throw new NoSuchEntityException(__('The picking request Item does not exist.'));
        }
        return $collection->setPageSize(1)->setCurPage(1)->getFirstItem();
    }


}

