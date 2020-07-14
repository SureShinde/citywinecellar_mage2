<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Model\Repository\PackRequest;

use Magestore\FulfilSuccess\Api\Data\PackRequestInterface;
use Magestore\FulfilSuccess\Api\Data\PackRequestItemInterface;
use Magestore\FulfilSuccess\Api\PackRequestRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;

class PackRequestRepository implements PackRequestRepositoryInterface
{
    /**
     * @var \Magestore\FulfilSuccess\Model\PackRequest\PackRequestFactory
     */
    protected $packRequestFactory;
    
    /**
     * @var \Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequest\CollectionFactory
     */
    protected $collectionFactory;    

    /**
     * @var \Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequest
     */
    protected $packRequestResource;

    /**
     * @var \Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequestItem
     */
    protected $packRequestItemResource;

    /**
     * @var \Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequestItem\CollectionFactory 
     */
    protected $itemCollectionFactory;
    
    /**
     * @var \Magestore\FulfilSuccess\Api\Data\PackRequestSearchResultsInterfaceFactory 
     */
    protected $searchResultsFactory;

    
    /**
     * PackRequestRepository constructor.
     * @param \Magestore\FulfilSuccess\Model\PackRequest\PackRequestFactory $packRequestFactory
     * @param \Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequest $packRequestResource
     * @param \Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequestItem $packRequestItemResource
     */
    public function __construct(
        \Magestore\FulfilSuccess\Model\PackRequest\PackRequestFactory $packRequestFactory,
        \Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequest $packRequestResource,
        \Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequestItem $packRequestItemResource,
        \Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequest\CollectionFactory $collectionFactory,
        \Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequestItem\CollectionFactory $itemCollectionFactory,
        \Magestore\FulfilSuccess\Api\Data\PackRequestSearchResultsInterfaceFactory $searchResultsFactory
    )
    {
        $this->packRequestFactory = $packRequestFactory;
        $this->packRequestResource = $packRequestResource;
        $this->packRequestItemResource = $packRequestItemResource;
        $this->collectionFactory = $collectionFactory;
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * @inheritDoc
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $collection = $this->collectionFactory->create();
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        $searchResults->setItems($collection->getItems());
        
        return $searchResults;    
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        /** @var \Magestore\FulfilSuccess\Model\PackRequest\PackRequest $packRequest */
        $packRequest = $this->packRequestFactory->create();
        $this->packRequestResource->load($packRequest, $id);
        if (!$packRequest->getId()) {
            throw new NoSuchEntityException(__('The batch with ID "%1" does not exist.', $id));
        }
        return $packRequest;
    }
    
    /**
     * Get list of Pack Requests by Order Id
     * 
     * @param int $orderId
     * @return \Magestore\FulfilSuccess\Api\Data\PackRequestItemInterface[]
     */
    public function getByOrderId($orderId)
    {
        $packRequests = $this->collectionFactory->create()
                                ->addFieldToFilter(PackRequestInterface::ORDER_ID, $orderId);
        return $packRequests->getItems();
    }
    
    /**
     * Get Item in Pack Request
     * 
     * @param \Magestore\FulfilSuccess\Api\Data\PackRequestInterface $packRequest
     * @param int $itemId
     * @return \Magestore\FulfilSuccess\Api\Data\PackRequestItemInterface
     */
    public function getItem(\Magestore\FulfilSuccess\Api\Data\PackRequestInterface $packRequest, $itemId)
    {
        $items = $this->itemCollectionFactory->create()
                            ->addFieldToFilter(PackRequestItemInterface::PACK_REQUEST_ID, $packRequest->getId())
                            ->addFieldToFilter(PackRequestItemInterface::ITEM_ID, $itemId);
        return $items->setPageSize(1)->setCurPage(1)->getFirstItem();
    }

    /**
     * @inheritDoc
     */
    public function delete(\Magestore\FulfilSuccess\Api\Data\PackRequestInterface $packRequest)
    {
        try {
            $this->packRequestResource->delete($packRequest);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function save(\Magestore\FulfilSuccess\Api\Data\PackRequestInterface $packRequest)
    {
        try {
            $this->packRequestResource->save($packRequest);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $packRequest;
    }

    /**
     * Retrieve items list from PackRequest
     *
     * @param \Magestore\FulfilSuccess\Api\Data\PackRequestInterface $packRequest
     * @return \Magestore\FulfilSuccess\Api\Data\PackRequestItemInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getItemList(PackRequestInterface $packRequest)
    {
        $itemCollection = $this->itemCollectionFactory->create();
        $itemCollection->addFieldToFilter(PackRequestItemInterface::PACK_REQUEST_ID, $packRequest->getId());
        return $itemCollection->getItems();
    }

    /**
     * Get pack request by pick request Id
     *
     * @param int $pickRequestPick
     * @return \Magestore\FulfilSuccess\Api\Data\PackRequestItemInterface
     */
    public function getByPickRequestId($pickRequestPick)
    {
        $packRequests = $this->collectionFactory->create()
            ->addFieldToFilter(PackRequestInterface::PICK_REQUEST_ID, $pickRequestPick);
        return $packRequests->setPageSize(1)->getFirstItem();
    }
}