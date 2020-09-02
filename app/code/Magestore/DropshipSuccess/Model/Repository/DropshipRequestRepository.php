<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Model\Repository;

use Magestore\DropshipSuccess\Api\Data;
use Magestore\DropshipSuccess\Api\DropshipRequestRepositoryInterface;
use Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest as ResourceDropshipRequest;
use Magestore\DropshipSuccess\Model\DropshipRequestFactory;
use \Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\CollectionFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class BlockRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DropshipRequestRepository implements DropshipRequestRepositoryInterface
{
    /**
     * @var ResourceDropshipRequest
     */
    protected $resourceDropshipRequest;

    /**
     * @var DropshipRequestFactory
     */
    protected $dropshipRequestFactory;

    /**
     * @var Data\DropshipRequestSearchResultsInterfaceFactory
     */

    protected $searchResultsFactory;
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * DropshipRequestrRepository constructor.
     * @param ResourceDropshipRequest $resource
     * @param Data\DropshipRequestSearchResultsInterfaceFactory $searchResultsFactory
     * @param DropshipRequestFactory $dropshipRequestFactory
     */
    public function __construct(
        ResourceDropshipRequest $resource,
        Data\DropshipRequestSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionFactory $collectionFactory,
        DropshipRequestFactory $dropshipRequestFactory
    )
    {
        $this->resourceDropshipRequest = $resource;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionFactory = $collectionFactory;
        $this->dropshipRequestFactory = $dropshipRequestFactory;
    }

    /**
     * Save dropship request.
     *
     * @param \Magestore\DropshipSuccess\Api\Data\DropshipRequestInterface $dropshipRequest
     * @return \Magestore\DropshipSuccess\Api\Data\DropshipRequestInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\DropshipRequestInterface $dropshipRequest)
    {
        try {
            $this->resourceDropshipRequest->save($dropshipRequest);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $dropshipRequest;
    }

    /**
     * Retrieve DropshipRequests matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\DropshipSuccess\Api\Data\DropshipRequestSearchResultsInterface
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
     * Retrieve dropship request.
     *
     * @param int $dropshipRequestId
     * @return \Magestore\DropshipSuccess\Api\Data\DropshipRequestInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($dropshipRequestId)
    {
        $dropshipRequest = $this->dropshipRequestFactory->create();
        $this->resourceDropshipRequest->load($dropshipRequest, $dropshipRequestId);
        if (!$dropshipRequest->getId()) {
            throw new NoSuchEntityException(__('Dropship request with id "%1" does not exist.', $dropshipRequestId));
        }
        return $dropshipRequest;
    }

    /**
     * Delete dropship request.
     *
     * @param \Magestore\DropshipSuccess\Api\Data\DropshipRequestInterface $dropshipRequest
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\DropshipRequestInterface $dropshipRequest)
    {
        try {
            $this->resourceDropshipRequest->delete($dropshipRequest);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete dropship request by ID.
     *
     * @param int $dropshipRequestId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($dropshipRequestId)
    {
        return $this->delete($this->getById($dropshipRequestId));
    }

    /**
     * Cancel dropship request by ID.
     *
     */
    public function cancelDropshipRequest($request)
    {
        if ($request->getStatus() == Data\DropshipRequestInterface::STATUS_PARTIAL_SHIP) {
            $status = Data\DropshipRequestInterface::STATUS_SHIPPED;
        } else {
            $status = Data\DropshipRequestInterface::STATUS_CANCELED;
        }
        try {
            $request->setStatus($status);
            $this->save($request);
        } catch (Exception $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        }
        return $request;
    }


    /**
     * {@inheritdoc}
     * */
    public function isAllowedAccess(\Magestore\DropshipSuccess\Api\Data\DropshipRequestInterface $dropshipRequest, $supplierId)
    {
        return $dropshipRequest->getSupplierId() == $supplierId;
    }
}
