<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposHeartlandTerminal\Model\ConnectedReader;

use Magestore\WebposHeartlandTerminal\Api\Data\ConnectedReaderInterface;
use Magestore\WebposHeartlandTerminal\Api\Data\ConnectedReaderSearchResultsInterfaceFactory as SearchResultFactory;
use Magestore\WebposHeartlandTerminal\Api\ConnectedReaderRepositoryInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magestore\WebposHeartlandTerminal\Model\ResourceModel\ConnectedReader;
use Magestore\WebposHeartlandTerminal\Model\ResourceModel\ConnectedReader\CollectionFactory;

/**
 * Class ConnectedReaderRepository
 *
 * @package Magestore\WebposHeartlandTerminal\Model\ConnectedReader
 */
class ConnectedReaderRepository implements ConnectedReaderRepositoryInterface
{
    /**
     * @var \Magestore\WebposHeartlandTerminal\Helper\Data
     */
    protected $helper;
    /**
     * @var ConnectedReader
     */
    protected $resourceModel;
    /**
     * @var ConnectedReaderFactory
     */
    protected $factory;
    /**
     * @var CollectionFactory
     */
    protected $resourceCollectionFactory;

    /** @var  SearchResultFactory */
    protected $searchResultFactory;
    /**
     * ConnectedReaderRepository constructor.
     *
     * @param ConnectedReader                                $resourceModel
     * @param ConnectedReaderFactory                         $factory
     * @param CollectionFactory                              $resourceCollectionFactory
     * @param SearchResultFactory                            $searchResultFactory
     * @param \Magestore\WebposHeartlandTerminal\Helper\Data $helper
     */
    public function __construct(
        ConnectedReader $resourceModel,
        ConnectedReaderFactory $factory,
        CollectionFactory $resourceCollectionFactory,
        SearchResultFactory $searchResultFactory,
        \Magestore\WebposHeartlandTerminal\Helper\Data $helper
    ) {
        $this->resourceModel = $resourceModel;
        $this->factory = $factory;
        $this->resourceCollectionFactory = $resourceCollectionFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->helper = $helper;
    }
    /**
     * @inheritdoc
     */
    public function save(ConnectedReaderInterface $connectedReader)
    {
        try {
            /** @var \Magento\Framework\Model\AbstractModel $connectedReader */
            return $this->resourceModel->save($connectedReader);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__('Unable to save connectedReader'));
        }
    }

    /**
     * @inheritdoc
     */
    public function getById($connectedReaderId)
    {
        $connectedReader = $this->factory->create();
        $this->resourceModel->load($connectedReader, $connectedReaderId);
        if (!$connectedReader->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __('connectedReader with id "%1" does not exist.', $connectedReaderId)
            );
        }
        return $connectedReader;
    }

    /**
     * @inheritdoc
     */
    public function delete(ConnectedReaderInterface $connectedReader)
    {
        return $this->deleteById($connectedReader->getId());
    }

    /**
     * @inheritdoc
     */
    public function deleteById($connectedReaderId)
    {
        /** @var \Magento\Framework\Model\AbstractModel $connectedReader */
        $connectedReader = $this->getById($connectedReaderId);
        return $this->resourceModel->delete($connectedReader);
    }

    /**
     * @inheritdoc
     */
    public function getConnectedReaderByPosId($posId)
    {
        /** @var \Magestore\WebposHeartlandTerminal\Model\ResourceModel\ConnectedReader\Collection $collection */
        $collection = $this->resourceCollectionFactory->create();
        $collection->addFieldToFilter(ConnectedReaderInterface::POS_ID, $posId);
        return $collection->getFirstItem();
    }

    /**
     * @inheritdoc
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Magestore\WebposHeartlandTerminal\Model\ResourceModel\ConnectedReader\Collection $collection */
        $collection = $this->resourceCollectionFactory->create();
        //Add filters from root filter group to the collection
        /** @var FilterGroup $group */
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders === null) {
            $sortOrders = [];
        }
        /** @var \Magento\Framework\Api\SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $collection->addOrder(
                $field,
                ($sortOrder->getDirection() == \Magento\Framework\Api\SortOrder::SORT_ASC)
                    ? \Magento\Framework\Api\SortOrder::SORT_ASC : \Magento\Framework\Api\SortOrder::SORT_DESC
            );
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->load();
        $searchResults = $this->searchResultFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $collection;
    }
}
