<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Model\Repository;

use Magestore\OrderSuccess\Api\BatchRepositoryInterface;
use Magestore\OrderSuccess\Api\Data\BatchSearchResultsInterfaceFactory;
use Magestore\OrderSuccess\Api\Data\BatchInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magestore\OrderSuccess\Model\Db\QueryProcessorInterface;

/**
 * Class BatchRepository
 * @package Magestore\OrderSuccess\Model\Repository
 */
class BatchRepository implements BatchRepositoryInterface
{
    /**
     * @var \Magestore\OrderSuccess\Model\ResourceModel\Batch
     */
    protected $resource;
    
    /**
     * @var \Magestore\OrderSuccess\Model\BatchFactory
     */
    protected $batchFactory;
    
    /**
     * @var BatchSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;
    
    /**
     * @var \Magestore\OrderSuccess\Model\ResourceModel\Batch\CollectionFactory
     */
    protected $collectionFactory;
    
    /**
     * @var \Magento\Backend\Model\Auth\Session 
     */
    protected $session;

    /**
     * @var \Magestore\OrderSuccess\Model\Db\QueryProcessorInterface
     */
    protected $queryProcessor;

    /**
     * BatchRepository constructor.
     * @param \Magestore\OrderSuccess\Model\ResourceModel\Batch $resource
     * @param \Magestore\OrderSuccess\Model\BatchFactory $batchFactory
     * @param BatchSearchResultsInterfaceFactory $searchResultsFactory
     * @param \Magestore\OrderSuccess\Model\ResourceModel\Batch\CollectionFactory $collectionFactory
     * @param \Magento\Backend\Model\Auth\Session $session
     */
    public function __construct(
        \Magestore\OrderSuccess\Model\ResourceModel\Batch $resource,
        \Magestore\OrderSuccess\Model\BatchFactory $batchFactory,
        BatchSearchResultsInterfaceFactory $searchResultsFactory,
        \Magestore\OrderSuccess\Model\ResourceModel\Batch\CollectionFactory $collectionFactory,
        \Magento\Backend\Model\Auth\Session $session,
        QueryProcessorInterface $queryProcessor
    )
    {
        $this->resource = $resource;
        $this->batchFactory = $batchFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionFactory = $collectionFactory;
        $this->session = $session;
        $this->queryProcessor = $queryProcessor;
    }

    /**
     * Save Batch.
     *
     * @param \Magestore\OrderSuccess\Api\Data\BatchInterface $batch
     * @return \Magestore\OrderSuccess\Api\Data\BatchInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magestore\OrderSuccess\Api\Data\BatchInterface $batch)
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
     * @return \Magestore\OrderSuccess\Api\Data\BatchInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($batchId)
    {
        $batch = $this->batchFactory->create();
        $this->resource->load($batch, $batchId);
        if (!$batch->getId()) {
            throw new NoSuchEntityException(__('The batch with ID "%1" does not exist.', $batchId));
        }
        return $batch;        
    }

    /**
     * Retrieve batchs matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\OrderSuccess\Api\Data\BatchSearchResultsInterface
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
     * @param \Magestore\OrderSuccess\Api\Data\BatchInterface $batch
     * @return \Magestore\OrderSuccess\Api\Data\PickRequestSearchResultsInterface
     */
    public function getOrderList(BatchInterface $batch)
    {
        
    }

    /**
     * Delete batch.
     *
     * @param \Magestore\OrderSuccess\Api\Data\BatchInterface $batch
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

    /**
     * Mass delete batch
     *
     * @param string $actionKey
     * @param string $actionValue
     * @param array $batchIds
     */
    public function massDelete($batchIds)
    {
        if(!count($batchIds)){
            return;
        }
        $this->queryProcessor->start('massUpdateBatch');

        $this->queryProcessor->addQuery([
            'type' => QueryProcessorInterface::QUERY_TYPE_DELETE,
            'condition' => [BatchInterface::BATCH_ID. ' IN (?)' => $batchIds],
            'table' => $this->resource->getMainTable()
        ], 'massUpdateBatch');
        $this->queryProcessor->process('massUpdateBatch');
    }
}

