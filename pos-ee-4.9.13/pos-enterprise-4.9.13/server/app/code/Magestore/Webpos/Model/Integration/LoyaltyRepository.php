<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Integration;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;

/**
 * Customer repository.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class LoyaltyRepository implements \Magestore\Webpos\Api\Integration\LoyaltyRepositoryInterface
{
    /**
     * @var \Magestore\Webpos\Model\Customer\CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var \Magestore\Webpos\Api\Data\Sales\OrderSearchResultInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $helper;
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * LoyaltyRepository constructor.
     * @param \Magestore\Webpos\Model\Customer\CustomerRepository $customerRepository
     * @param \Magestore\Webpos\Api\Data\Sales\OrderSearchResultInterfaceFactory $searchResultsFactory
     * @param \Magestore\Webpos\Helper\Data $helper
     */
    public function __construct(
        \Magestore\Webpos\Model\Customer\CustomerRepository $customerRepository,
        \Magestore\Webpos\Api\Data\Sales\OrderSearchResultInterfaceFactory $searchResultsFactory,
        \Magestore\Webpos\Helper\Data $helper,
        \Magento\Framework\Event\ManagerInterface $eventManager
    )
    {
        $this->customerRepository = $customerRepository;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->helper = $helper;
        $this->eventManager = $eventManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        /** @var \Magento\Customer\Model\ResourceModel\Customer\Collection $collection */
        $collection = $this->customerRepository->getCustomerCollection($searchCriteria, true);
        $this->getLoyaltyCollection($collection);
        //Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $searchResults->setTotalCount($collection->getSize());

        $customers = [];
        /** @var \Magento\Customer\Model\Customer $customerModel */
        foreach ($collection as $customerModel) {
            $customers[] = $customerModel->getDataModel();
        }
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($customers);
        return $searchResults;
    }

    /**
     * @param $collection
     * @return mixed
     */
    public function getLoyaltyCollection($collection)
    {
        $this->eventManager->dispatch('get_loyalty_collection', ['collection' => $collection]);

        if ($this->helper->isStoreCreditEnable()) {
            $collection->getSelect()->joinLeft(
                array('customer_credit' => $collection->getTable('customer_credit')),
                'e.entity_id = customer_credit.customer_id',
                array(
                    'customer_credit_updated_at' => 'customer_credit.updated_at',
                    'credit_balance' => 'customer_credit.credit_balance'
                ));
        }
        return $collection;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Magento\Customer\Model\ResourceModel\Customer\Collection $collection
     * @return void
     */
    public function addFilterGroupToCollection(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Customer\Model\ResourceModel\Customer\Collection $collection
    )
    {
        $checkProcess = new \Magento\Framework\DataObject();
        $updatedAt = '';
        foreach ($filterGroup->getFilters() as $filter) {
            if ($filter->getField() == 'updated_at') {
                $updatedAt = $filter->getValue();
                break;
            }
        }
        $this->eventManager->dispatch('loyalty_add_filter_group',
            ['collection' => $collection, 'updated_at' => $updatedAt, 'check_process' => $checkProcess]);
        if ($updatedAt && !$checkProcess->getData('process_store_credit')) {
            if ($this->helper->isStoreCreditEnable()) {
                $collection->getSelect()->where('
                    customer_credit.updated_at >= "' . $updatedAt . '"
                ');
            }
        }
    }
}
