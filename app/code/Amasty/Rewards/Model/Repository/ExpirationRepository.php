<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Model\Repository;

use Amasty\Rewards\Api\Data\ExpirationDateInterface;
use Amasty\Rewards\Api\ExpirationDateRepositoryInterface;
use Amasty\Rewards\Model\ExpirationFactory;
use Amasty\Rewards\Model\ResourceModel\Expiration as ExpirationResource;
use Amasty\Rewards\Model\ResourceModel\Expiration\Collection;
use Amasty\Rewards\Model\ResourceModel\Expiration\CollectionFactory;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Data\Collection as DataCollection;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ExpirationRepository implements ExpirationDateRepositoryInterface
{
    /**
     * @var BookmarkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var ExpirationFactory
     */
    private $expirationFactory;

    /**
     * @var ExpirationResource
     */
    private $expirationResource;

    /**
     * Model data storage
     *
     * @var array
     */
    private $expirations;

    /**
     * @var array
     */
    private $dataArraysByCustomer;

    /**
     * @var CollectionFactory
     */
    private $expirationCollectionFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private $criteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    public function __construct(
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        ExpirationFactory $expirationFactory,
        ExpirationResource $expirationResource,
        CollectionFactory $expirationCollectionFactory,
        SearchCriteriaBuilder $criteriaBuilder,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->expirationFactory = $expirationFactory;
        $this->expirationResource = $expirationResource;
        $this->expirationCollectionFactory = $expirationCollectionFactory;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * @inheritdoc
     */
    public function save(ExpirationDateInterface $expiration)
    {
        try {
            if ($expiration->getId()) {
                $expiration = $this->getById($expiration->getId())->addData($expiration->getData());
            }
            $this->expirationResource->save($expiration);
            unset($this->expirations[$expiration->getId()]);
        } catch (\Exception $e) {
            if ($expiration->getId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save expiration with ID %1. Error: %2',
                        [$expiration->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new expiration. Error: %1', $e->getMessage()));
        }

        return $expiration;
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        if (!isset($this->expirations[$id])) {
            /** @var \Amasty\Rewards\Model\Expiration $expiration */
            $expiration = $this->expirationFactory->create();
            $this->expirationResource->load($expiration, $id);

            if (!$expiration->getId()) {
                throw new NoSuchEntityException(__('Expiration with specified ID "%1" not found.', $id));
            }

            $this->expirations[$id] = $expiration;
        }

        return $this->expirations[$id];
    }

    /**
     * @inheritdoc
     */
    public function getItemsByCustomerId($customerId)
    {
        /** @var \Amasty\Rewards\Model\ResourceModel\Expiration\Collection $collection */
        $collection = $this->expirationCollectionFactory->create();
        $collection->addFieldToFilter(ExpirationDateInterface::CUSTOMER_ID, $customerId)
            ->addFieldToFilter(ExpirationDateInterface::DATE, ['notnull' => null]);

        return $collection->getItems();
    }

    /**
     * @inheritdoc
     */
    public function getByCustomerId($customerId, $isGrouped = true)
    {
        if (!isset($this->dataArraysByCustomer[$customerId])) {
            /** @var \Amasty\Rewards\Model\ResourceModel\Expiration\Collection $collection */
            $collection = $this->expirationCollectionFactory->create();
            $collection->getPointsByCustomerId($customerId, $isGrouped);

            $items = $collection->getData();

            if (!$items) {
                throw new NoSuchEntityException(__('Customer with specified ID not found.'));
            }

            $this->dataArraysByCustomer[$customerId] = $items;
        }

        return $this->dataArraysByCustomer[$customerId];
    }

    /**
     * @inheritdoc
     */
    public function delete(ExpirationDateInterface $expiration)
    {
        try {
            $this->expirationResource->delete($expiration);
            unset($this->expirations[$expiration->getId()]);
        } catch (\Exception $e) {
            if ($expiration->getId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove expiration with ID %1. Error: %2',
                        [$expiration->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove expiration. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($id)
    {
        $expirationModel = $this->getById($id);
        $this->delete($expirationModel);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Amasty\Rewards\Model\ResourceModel\Expiration\Collection $expirationCollection */
        $expirationCollection = $this->expirationCollectionFactory->create();

        // Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $expirationCollection);
        }

        $searchResults->setTotalCount($expirationCollection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();

        if ($sortOrders) {
            $this->addOrderToCollection($sortOrders, $expirationCollection);
        }

        $expirationCollection->setCurPage($searchCriteria->getCurrentPage());
        $expirationCollection->setPageSize($searchCriteria->getPageSize());

        $expirations = [];
        /** @var ExpirationDateInterface $expiration */
        foreach ($expirationCollection->getItems() as $expiration) {
            $expirations[] = $this->getById($expiration->getEntityId());
        }

        $searchResults->setItems($expirations);

        return $searchResults;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection  $expirationCollection
     *
     * @return void
     */
    private function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $expirationCollection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ?: 'eq';
            $expirationCollection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
    }

    /**
     * Helper function that adds a SortOrder to the collection.
     *
     * @param SortOrder[] $sortOrders
     * @param Collection  $expirationCollection
     *
     * @return void
     */
    private function addOrderToCollection($sortOrders, Collection $expirationCollection)
    {
        /** @var SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $expirationCollection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_DESC) ? SortOrder::SORT_DESC : SortOrder::SORT_ASC
            );
        }
    }

    /**
     * @return ExpirationDateInterface
     */
    public function getEmptyModel()
    {
        return $this->expirationFactory->create();
    }

    /**
     * @inheritdoc
     */
    public function getCustomerNonExpireRewards($customerId)
    {
        $id = $this->expirationResource->getNonExpireId($customerId);

        if ($id) {
            return $this->getById($id);
        }

        return $this->getEmptyModel();
    }
}
