<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Model\Repository\PickRequest;

use Magestore\FulfilSuccess\Api\PickRequestRepositoryInterface;
use Magestore\FulfilSuccess\Api\Data\PickRequestInterface;
use Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface;
use Magestore\FulfilSuccess\Model\Db\QueryProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class PickRequestRepository implements PickRequestRepositoryInterface
{
    /**
     * @var \Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\PickRequest  
     */
    protected $resource;
    
    /**
     * @var \Magestore\FulfilSuccess\Model\PickRequest\PickRequestFactory  
     */
    protected $pickRequestFactory;
    
    /**
     * @var \Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\PickRequestItem 
     */
    protected $pickRequestItemResource;
    
    /**
     * @var \Magestore\FulfilSuccess\Api\Data\PickRequestSearchResultsInterfaceFactory 
     */
    protected $searchResultsFactory;
    
    /**
     * @var \Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\PickRequest\CollectionFactory  
     */
    protected $collectionFactory;
    
    /**
     * @var \Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\PickRequestItem\CollectionFactory  
     */
    protected $pickRequestItemCollectionFactory;

    /**
     * @var \Magestore\FulfilSuccess\Api\PickRequestItemRepositoryInterface
     */
    protected $pickRequestItemRepository;

    /**
     * @var \Magestore\FulfilSuccess\Model\Db\QueryProcessorInterface  
     */
    protected $queryProcessor;
    
    public function __construct(
        \Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\PickRequest $resource,
        \Magestore\FulfilSuccess\Model\PickRequest\PickRequestFactory $pickRequestFactory,
        \Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\PickRequestItem $pickRequestItemResource,
        \Magestore\FulfilSuccess\Api\Data\PickRequestSearchResultsInterfaceFactory $searchResultsFactory,
        \Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\PickRequest\CollectionFactory $collectionFactory,
        \Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\PickRequestItem\CollectionFactory $pickRequestItemCollectionFactory,
        \Magestore\FulfilSuccess\Api\PickRequestItemRepositoryInterface $pickRequestItemRepository,
        \Magestore\FulfilSuccess\Model\Db\QueryProcessorInterface $queryProcessor
    )
    {
        $this->resource = $resource;
        $this->pickRequestFactory = $pickRequestFactory;
        $this->pickRequestItemResource = $pickRequestItemResource;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionFactory = $collectionFactory;
        $this->pickRequestItemCollectionFactory = $pickRequestItemCollectionFactory;
        $this->pickRequestItemRepository = $pickRequestItemRepository;
        $this->queryProcessor = $queryProcessor;
    }
    
    /**
     * Save PickRequest.
     *
     * @param \Magestore\FulfilSuccess\Api\Data\PickRequestInterface $pickRequest
     * @return \Magestore\FulfilSuccess\Api\Data\PickRequestInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(PickRequestInterface $pickRequest)
    {
        try {
            $this->resource->save($pickRequest);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $pickRequest;             
    }

    /**
     * Retrieve PickRequest.
     *
     * @param int $pickRequestId
     * @return \Magestore\FulfilSuccess\Api\Data\PickRequestInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($pickRequestId)
    {
        $pickRequest = $this->pickRequestFactory->create();
        $this->resource->load($pickRequest, $pickRequestId);
        if (!$pickRequest->getId()) {
            throw new NoSuchEntityException(__('The picking request with id "%1" does not exist.', $pickRequestId));
        }
        return $pickRequest;          
    }

    /**
     * Retrieve PickRequests matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\FulfilSuccess\Api\Data\PickRequestSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
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
     * Retrieve items list from PickRequest
     * 
     * @param \Magestore\FulfilSuccess\Api\Data\PickRequestInterface $pickRequest
     * @return \Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getItemList(PickRequestInterface $pickRequest)
    {
        $itemCollection = $this->pickRequestItemCollectionFactory->create();
        $itemCollection->addFieldToFilter(PickRequestItemInterface::PICK_REQUEST_ID, $pickRequest->getId());
        $itemCollection->addShelfLocation($pickRequest->getWarehouseId());
        return $itemCollection->getItems();
    }
    
    /**
     * Retrieve PickRequestItem in PickRequest
     * 
     * @param \Magestore\FulfilSuccess\Api\Data\PickRequestInterface $pickRequest
     * @param int $itemId
     * @return \Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface
     */
    public function getPickRequestItem(PickRequestInterface $pickRequest, $itemId)
    {
        $itemCollection = $this->pickRequestItemCollectionFactory->create();
        $itemCollection->addFieldToFilter(PickRequestItemInterface::PICK_REQUEST_ID, $pickRequest->getId())
                        ->addFieldToFilter(PickRequestItemInterface::ITEM_ID, $itemId);
        return $itemCollection->setPageSize(1)->setCurPage(1)->getFirstItem();
    }

    /**
     * Mass Update items in PickRequest
     * 
     * @param \Magestore\FulfilSuccess\Api\Data\PickRequestInterface $pickRequest
     * @param array $itemsData
     */
    public function massUpdateItems(PickRequestInterface $pickRequest, $itemsData)
    {
        if(!count($itemsData)){
            return;
        }
        
        $convertItemsData = [];
        foreach($itemsData as $itemData) {
            $convertItemsData[$itemData['item_id']] = $itemData;
        }

        $connection = $this->resource->getConnection();
        $this->queryProcessor->start('massUpdateItems');
        
        $itemCollection = $this->pickRequestItemCollectionFactory->create();
        $itemCollection->addFieldToFilter(PickRequestItemInterface::PICK_REQUEST_ID, $pickRequest->getId())
                        ->addFieldToFilter(PickRequestItemInterface::ITEM_ID, ['in' => array_keys($convertItemsData)]);
        
        /* prepare update Values for using in CASE query of Mysql */
        $values = $this->prepareUpdateValues($itemCollection->getItems(), $convertItemsData);
        
        $where = [
            PickRequestItemInterface::ITEM_ID . ' IN (?)' => array_keys($convertItemsData),
            PickRequestItemInterface::PICK_REQUEST_ID . '=?' => $pickRequest->getId()
        ];
        if (count($values)) {
            $this->queryProcessor->addQuery([
                'type' => QueryProcessorInterface::QUERY_TYPE_UPDATE,
                'values' => $values,
                'condition' => $where,
                'table' => $this->pickRequestItemResource->getMainTable()
            ], 'massUpdateItems');
        }        
        
        $this->queryProcessor->process('massUpdateItems');        
    }
    
    /**
     * Prepare update data of items in PickRequest
     * $itemsData = [$itemId => []]
     * 
     * @param \Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface[] $pickRequestItems
     * @param array $itemsData
     * @return array
     */
    public function prepareUpdateValues($pickRequestItems, $itemsData)
    {
        $updateValues = [];
        $conditions = [];
        $connection = $this->resource->getConnection();
        foreach ($pickRequestItems as $pickRequestItem) {
            if(!isset($itemsData[$pickRequestItem->getItemId()])) {
                continue;
            }
            $case = $connection->quoteInto('?', $pickRequestItem->getItemId());
            /* scan all fields in $data */
            foreach ($itemsData[$pickRequestItem->getItemId()] as $field => $value) {
                if ($pickRequestItem->getData($field) != $value) {
                    /* if change the data of $field */
                    if(is_numeric($value)) {
                        $operator = $value >= 0 ? '+' : '-';
                        $conditions[$field][$case] = $connection->quoteInto("{$field}{$operator}?", abs($value));
                    } else {
                        $conditions[$field][$case] = $connection->quoteInto('?', $value);
                    }
                }
            }
        }
        /* bind conditions to $updateValues */
        foreach ($conditions as $field => $condition) {
            $updateValues[$field] = $connection->getCaseSql(PickRequestItemInterface::ITEM_ID, $condition, $field);
        }
        return $updateValues;
    }    
    
    /**
     * Mass update batch Id of Pick Requests
     * 
     * @param array $pickRequestIds
     * @param int $batchId
     */
    public function massUpdateBatch($pickRequestIds, $batchId)
    {
        if(!count($pickRequestIds)){
            return;
        }
        $connection = $this->resource->getConnection();
        $this->queryProcessor->start('massUpdateBatch');
 
        $this->queryProcessor->addQuery([
            'type' => QueryProcessorInterface::QUERY_TYPE_UPDATE,
            'values' =>  [PickRequestInterface::BATCH_ID => $batchId], 
            'condition' => [PickRequestInterface::PICK_REQUEST_ID. ' IN (?)' => $pickRequestIds], 
            'table' => $this->resource->getMainTable()
        ], 'massUpdateBatch');   
        
        $this->queryProcessor->process('massUpdateBatch');
    }
    
    /**
     * Delete PickRequest.
     *
     * @param \Magestore\FulfilSuccess\Api\Data\BatchInterface $pickRequest
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(PickRequestInterface $pickRequest)
    {
        try {
            $this->resource->delete($pickRequest);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;            
    }

    /**
     * Delete PickRequest by ID.
     *
     * @param int $pickRequestId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($pickRequestId)
    {
        return $this->delete($this->getById($pickRequestId));          
    }

    /**
     * Retrieve PickRequest.
     *
     * @param string $incrementId
     * @return \Magestore\FulfilSuccess\Api\Data\PickRequestInterface
     */
    public function getByOrderIncrementId($incrementId)
    {
        $pickRequest = $this->pickRequestFactory->create();
        $this->resource->load($pickRequest, $incrementId, PickRequestInterface::ORDER_INCREMENT_ID);
        return $pickRequest;
    }

    /**
     * @inheritDoc
     */
    public function massUpdatePickRequestItems($pickRequest, $data)
    {
        $pickItems = $this->getItemList($pickRequest);
        foreach ($pickItems as $pickItem) {
            if (in_array($pickItem->getId(), array_keys($data))) {
                $pickItem->setPickedQty($data[$pickItem->getId()][PickRequestItemInterface::PICKED_QTY]);
                $this->pickRequestItemRepository->save($pickItem);
            }
        }
    }

    /**
     * Retrieve requets in a Batch
     *
     * @param array $batchIds
     * @return \Magento\Sales\Api\Data\OrderSearchResultInterface
     */
    public function getPickRequestFromBatch($batchId)
    {
        $orders = $this->collectionFactory->create()
            ->addFieldToFilter('batch_id', $batchId);
        return $orders;
    }


}