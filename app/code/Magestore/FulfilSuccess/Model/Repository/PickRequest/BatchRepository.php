<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Model\Repository\PickRequest;

use Magestore\FulfilSuccess\Api\BatchRepositoryInterface;
use Magestore\FulfilSuccess\Api\Data;
use Magestore\FulfilSuccess\Api\Data\BatchInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class BatchRepository implements BatchRepositoryInterface
{
    /**
     * @var \Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\Batch  
     */
    protected $resource;
    
    /**
     * @var \Magestore\FulfilSuccess\Model\PickRequest\BatchFactory  
     */
    protected $batchFactory;
    
    /**
     * @var Data\BatchSearchResultsInterfaceFactory 
     */
    protected $searchResultsFactory;
    
    /**
     * @var \Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\Batch\CollectionFactory  
     */
    protected $collectionFactory;
    
    /**
     * @var \Magento\Backend\Model\Auth\Session 
     */
    protected $session;
    
    public function __construct(
        \Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\Batch $resource,
        \Magestore\FulfilSuccess\Model\PickRequest\BatchFactory $batchFactory,
        Data\BatchSearchResultsInterfaceFactory $searchResultsFactory,
        \Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\Batch\CollectionFactory $collectionFactory,
        \Magento\Backend\Model\Auth\Session $session
    )
    {
        $this->resource = $resource;
        $this->batchFactory = $batchFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionFactory = $collectionFactory;
        $this->session = $session;
    }
    
    /**
     * Save Batch.
     *
     * @param \Magestore\FulfilSuccess\Api\Data\BatchInterface $batch
     * @return \Magestore\FulfilSuccess\Api\Data\BatchInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(BatchInterface $batch)
    {
        try {
            $this->resource->save($batch);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $batch;        
    }
    
    /**
     * create a new batch
     * 
     * @return BatchInterface
     */
    public function newBatch()
    {
        $batch = $this->batchFactory->create();
        $batch->setUserId($this->session->getUser()->getId());
        return $this->save($batch);
    }       

    /**
     * Retrieve batch.
     *
     * @param int $batchId
     * @return \Magestore\FulfilSuccess\Api\Data\BatchInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($batchId)
    {
        $batch = $this->batchFactory->create();
        $this->resource->load($batch, $batchId);
        if (!$batch->getId()) {
            throw new NoSuchEntityException(__('The Batch with id "%1" does not exist.', $batchId));
        }
        return $batch;        
    }

    /**
     * Retrieve batchs matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\FulfilSuccess\Api\Data\BatchSearchResultsInterface
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
     * Retrieve requets in a Batch
     * 
     * @param \Magestore\FulfilSuccess\Api\Data\BatchInterface $batch
     * @return \Magestore\FulfilSuccess\Api\Data\PickRequestSearchResultsInterface
     */
    public function getPickRequestList(BatchInterface $batch)
    {
        
    }

    /**
     * Delete batch.
     *
     * @param \Magestore\FulfilSuccess\Api\Data\BatchInterface $batch
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(BatchInterface $batch)
    {
        try {
            $this->resource->delete($batch);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;        
    }

    /**
     * Delete batch by ID.
     *
     * @param int $batchId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($batchId)
    {
        return $this->delete($this->getById($batchId));        
    }
}

