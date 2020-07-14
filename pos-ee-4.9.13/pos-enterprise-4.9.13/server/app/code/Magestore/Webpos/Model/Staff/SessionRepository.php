<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Staff;
class SessionRepository implements \Magestore\Webpos\Api\Staff\SessionRepositoryInterface
{
    /**
     * @var StaffFactory
     */
    protected $sessionFactory;
    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Staff\Session
     */
    protected $sessionResource;
    /**
     * @var \Magestore\Webpos\Api\Data\Staff\SessionSearchResultsInterface
     */
    protected $sessionSearchResults;
    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Staff\Session\CollectionFactory
     */
    protected $sessionCollectionFactory;
    /**
     * @var \Magestore\Appadmin\Model\ResourceModel\Staff\AuthorizationRule\CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface
     */
    protected $staffRepository;
    /**
     * StaffRepository constructor.
     * @param StaffFactory $sessionFactory
     */
    public function __construct(
        \Magestore\Webpos\Api\Data\Staff\SessionInterfaceFactory $sessionFactory,
        \Magestore\Webpos\Model\ResourceModel\Staff\Session $sessionResource,
        \Magestore\Webpos\Model\ResourceModel\Staff\Session\CollectionFactory $sessionCollectionFactory,
        \Magestore\Webpos\Api\Data\Staff\SessionSearchResultsInterfaceFactory $sessionSearchResultsInterfaceFactory,
        \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface $staffRepository,
        \Magestore\Appadmin\Model\ResourceModel\Staff\AuthorizationRule\CollectionFactory $collectionFactory
    ){
        $this->sessionFactory = $sessionFactory;
        $this->sessionResource = $sessionResource;
        $this->sessionSearchResults = $sessionSearchResultsInterfaceFactory;
        $this->sessionCollectionFactory = $sessionCollectionFactory;
        $this->collectionFactory = $collectionFactory;
        $this->staffRepository = $staffRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Magestore\Webpos\Api\Data\Staff\SessionInterface $session) {
        try {
            /* @var \Magestore\Webpos\Model\Staff\Session $session */
            /* @var \Magestore\Webpos\Model\Staff\Session $sessionModel */
            $sessionModel = $this->sessionResource->save($session);
            return $sessionModel;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__('Could not save session.'), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getById($id) {
        $session = $this->sessionFactory->create();
        $this->sessionResource->load($session, $id);
        if (!$session->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Session with id "%1" does not exist.', $id));
        } else {
            return $session;
        }
    }

    /**
     * Retrieve Session.
     *
     * @param int $sessionId
     * @return \Magestore\Webpos\Api\Data\Staff\SessionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBySessionId($sessionId) {
        $session = $this->sessionFactory->create();
        $this->sessionResource->load($session, $sessionId, \Magestore\Webpos\Api\Data\Staff\SessionInterface::SESSION_ID);
        if (!$session->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Session with id "%1" does not exist.', $sessionId));
        } else {
            return $session;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria) {
        $collection = $this->sessionCollectionFactory->create();
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
        $searchResults = $this->sessionSearchResults->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function getListByStaffId($staffId){
        /** @var \Magestore\Webpos\Model\ResourceModel\Staff\Session\Collection $collection */
        $collection = $this->sessionCollectionFactory->create();
        $collection->addFieldToFilter('staff_id', $staffId);

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Magestore\Webpos\Api\Data\Staff\SessionInterface $session)
    {
        return $this->deleteById($session->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($sessionId)
    {
        /* @var \Magestore\Webpos\Model\Staff\Session $session */
        $session = $this->getById($sessionId);
        if ($session->getId()) {
            $this->sessionResource->delete($session);
            return true;
        } else {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Session with id "%1" does not exist.', $sessionId));
        }
    }

    /**
     * @param int $staffId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAllCurrentPermission($staffId) {
        $staffModel = $this->staffRepository->getById($staffId);
        $resourceAccess = array();
        if ($staffModel->getId()) {
            $roleId = $staffModel->getRoleId();
            $authorizationCollection = $this->collectionFactory->create()->addFieldToFilter('role_id', $roleId);
            foreach ($authorizationCollection as $resource) {
                $resourceAccess[] = $resource->getResourceId();
            }
        }
        return $resourceAccess;
    }

    /**
     * @param int $posId
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function signOutPos($posId){
        $sessionCollection = $this->sessionCollectionFactory->create()->addFieldToFilter('pos_id', $posId);
        foreach ($sessionCollection as $session){
            $session->setData('pos_id', null);
            try {
                /* @var \Magestore\Webpos\Model\Staff\Session $session */
                /* @var \Magestore\Webpos\Model\Staff\Session $sessionModel */
                $session->getResource()->save($session);
                return $session;
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\CouldNotSaveException(__('Could not save session.'), $e);
            }
        }
    }
}