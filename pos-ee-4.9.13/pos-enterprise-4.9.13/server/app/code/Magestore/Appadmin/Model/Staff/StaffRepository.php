<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Model\Staff;
/**
 * Class StaffManagement
 * @package Magestore\Appadmin\Model\Staff
 */
class StaffRepository implements \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface
{
    /**
     * @var StaffFactory
     */
    protected $staffFactory;
    /**
     * @var \Magestore\Appadmin\Model\ResourceModel\Staff\Staff
     */
    protected $staffResource;
    /**
     * @var \Magestore\Appadmin\Api\Data\Staff\StaffSearchResultsInterface
     */
    protected $staffSearchResults;
    /**
     * @var \Magestore\Appadmin\Model\ResourceModel\Staff\Staff\CollectionFactory
     */
    protected $staffCollectionFactory;
    /**
     * StaffRepository constructor.
     * @param StaffFactory $staffFactory
     */
    public function __construct(
        \Magestore\Appadmin\Api\Data\Staff\StaffInterfaceFactory $staffFactory,
        \Magestore\Appadmin\Model\ResourceModel\Staff\Staff $staffResource,
        \Magestore\Appadmin\Model\ResourceModel\Staff\Staff\CollectionFactory $staffCollectionFactory,
        \Magestore\Appadmin\Api\Data\Staff\StaffSearchResultsInterfaceFactory $staffSearchResultsInterfaceFactory
    ){
        $this->staffFactory = $staffFactory;
        $this->staffResource = $staffResource;
        $this->staffSearchResults = $staffSearchResultsInterfaceFactory;
        $this->staffCollectionFactory = $staffCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Magestore\Appadmin\Api\Data\Staff\StaffInterface $staff) {
        try {
            /* @var \Magestore\Appadmin\Model\Staff\Staff $staff */
            /* @var \Magestore\Appadmin\Model\Staff\Staff $staffModel */
            $staffModel = $this->staffResource->save($staff);
            return $staffModel;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__('Could not save staff.'), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getById($staffId) {
        $staff = $this->staffFactory->create();
        $this->staffResource->load($staff, $staffId);
        if (!$staff->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Staff with id "%1" does not exist.', $staffId));
        } else {
            return $staff;
        }
    }


    /**
     * {@inheritdoc}
     */
    public function getByRoleId($roleId) {
        $collection = $this->staffCollectionFactory->create();
        $collection->addFieldToFilter('role_id', $roleId);
        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria) {
        $collection = $this->staffCollectionFactory->create();
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
        $searchResults = $this->staffSearchResults->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Magestore\Appadmin\Api\Data\Staff\StaffInterface $staff)
    {
        return $this->deleteById($staff->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($staffId)
    {
        /* @var \Magestore\Appadmin\Model\Staff\Staff $staff */
        $staff = $this->getById($staffId);
        if ($staff->getId()) {
            $this->staffResource->delete($staff);
            return true;
        } else {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Staff with id "%1" does not exist.', $staffId));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAllStaff(){
        /** @var \Magestore\Appadmin\Model\ResourceModel\Staff\Staff\Collection $collection */
        $collection = $this->staffCollectionFactory->create();
        return $collection;
    }

}