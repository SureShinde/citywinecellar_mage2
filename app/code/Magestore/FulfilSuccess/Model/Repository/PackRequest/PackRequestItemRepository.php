<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Model\Repository\PackRequest;

use Magestore\FulfilSuccess\Api\Data\PackRequestInterface;
use Magestore\FulfilSuccess\Api\Data\PackRequestItemInterface;
use Magestore\FulfilSuccess\Api\PackRequestItemRepositoryInterface;
use Magestore\FulfilSuccess\Model\PackRequest\PackRequestItem;
use Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequestItem\CollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;

class PackRequestItemRepository implements PackRequestItemRepositoryInterface
{
    /**
     * @var \Magestore\FulfilSuccess\Model\PackRequest\PackRequestItemFactory
     */
    protected $packRequestItemFactory;

    /**
     * @var \Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequestItem
     */
    protected $packRequestItemResource;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;


    public function __construct(
        \Magestore\FulfilSuccess\Model\PackRequest\PackRequestItemFactory $packRequestItemFactory,
        \Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequestItem $packRequestItemResource,
        CollectionFactory $collectionFactory
    )
    {
        $this->packRequestItemFactory = $packRequestItemFactory;
        $this->packRequestItemResource = $packRequestItemResource;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $searchCriteria)
    {
        // TODO: Implement getList() method.
    }

    /**
     *
     * @param array $productIds
     * @retrurn \Magestore\FulfilSuccess\Api\Data\PackRequestItemInterface[]
     */
    public function getPackingList($productIds = [])
    {
        $collection = $this->collectionFactory->create();
        if (count($productIds)) {
            $collection->addFieldToFilter(PackRequestItemInterface::PRODUCT_ID, ['in' => $productIds]);
        }
        $collection->joinPackRequest([PackRequestInterface::WAREHOUSE_ID, PackRequestInterface::STATUS]);
        $collection->addFieldToFilter(PackRequestInterface::STATUS,
            [
                'in' => [
                    PackRequestInterface::STATUS_PACKING,
                    PackRequestInterface::STATUS_PARTIAL_PACK
                ]
            ]
        );
        return $collection->getItems();
    }

    /**
     * @inheritDoc
     */
    public function get($packRequestItemId)
    {
        $packRequestItem = $this->packRequestItemFactory->create();
        $this->packRequestItemResource->load($packRequestItem, $packRequestItemId);
        if (!$packRequestItem->getId()) {
            throw new NoSuchEntityException(__('The Pack Request Item with id "%1" does not exist.', $packRequestItemId));
        }
        return $packRequestItem;
    }

    /**
     * @inheritDoc
     */
    public function delete(\Magestore\FulfilSuccess\Api\Data\PackRequestItemInterface $packRequestItem)
    {
        try {
            $this->packRequestItemResource->delete($packRequestItem);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function save(\Magestore\FulfilSuccess\Api\Data\PackRequestItemInterface $packRequestItem)
    {
        try {
            $this->packRequestItemResource->save($packRequestItem);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $packRequestItem;
    }

}