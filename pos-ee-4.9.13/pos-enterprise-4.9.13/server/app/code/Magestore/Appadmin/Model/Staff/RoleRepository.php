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
class RoleRepository implements \Magestore\Appadmin\Api\Staff\RoleRepositoryInterface
{
    /**
     * @var RoleFactory
     */
    protected $roleFactory;
    /**
     * @var \Magestore\Appadmin\Model\ResourceModel\Staff\Role
     */
    protected $roleResource;
    /**
     * @var \Magestore\Appadmin\Api\Data\Staff\RoleSearchResultsInterface
     */
    protected $roleSearchResults;
    /**
     * @var \Magestore\Appadmin\Model\ResourceModel\Staff\Role\CollectionFactory
     */
    protected $roleCollectionFactory;

    /**
     * RoleRepository constructor.
     * @param RoleFactory $roleFactory
     */
    public function __construct(
        \Magestore\Appadmin\Api\Data\Staff\RoleInterfaceFactory $roleFactory,
        \Magestore\Appadmin\Model\ResourceModel\Staff\Role $roleResource,
        \Magestore\Appadmin\Model\ResourceModel\Staff\Role\CollectionFactory $roleCollectionFactory,
        \Magestore\Appadmin\Api\Data\Staff\RoleSearchResultsInterfaceFactory $roleSearchResultsInterfaceFactory
    )
    {
        $this->roleFactory = $roleFactory;
        $this->roleResource = $roleResource;
        $this->roleSearchResults = $roleSearchResultsInterfaceFactory;
        $this->roleCollectionFactory = $roleCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Magestore\Appadmin\Api\Data\Staff\RoleInterface $role)
    {
        try {
            /* @var \Magestore\Appadmin\Model\Staff\Role $role */
            /* @var \Magestore\Appadmin\Model\Staff\Role $roleModel */
            $roleModel = $this->roleResource->save($role);
            return $roleModel;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__('Could not save role.'), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getById($roleId)
    {
        $role = $this->roleFactory->create();
        $this->roleResource->load($role, $roleId);
        if (!$role->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Role with id "%1" does not exist.', $roleId));
        } else {
            return $role;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->roleCollectionFactory->create();
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
        $searchResults = $this->roleSearchResults->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Magestore\Appadmin\Api\Data\Staff\RoleInterface $role)
    {
        return $this->deleteById($role->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($roleId)
    {
        /* @var \Magestore\Appadmin\Model\Staff\Role $role */
        $role = $this->getById($roleId);
        if ($role->getId()) {
            $this->roleResource->delete($role);
            return true;
        } else {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Role with id "%1" does not exist.', $roleId));
        }
    }

    /**
     * @inheritdoc
     */
    public function getAllRole()
    {
        /** @var \Magestore\Appadmin\Model\ResourceModel\Staff\Role\Collection $collection */
        $collection = $this->roleCollectionFactory->create();
        return $collection;
    }

}