<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @module      RewardPoints
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */


namespace Magestore\Rewardpoints\Model\Customer;

use Magestore\Rewardpoints\Api\CustomerRepositoryInterface;
use Magestore\Rewardpoints\Api\Data\Customer\CustomerInterface;
use Magestore\Rewardpoints\Api\Data\Customer\CustomerSearchResultsInterfaceFactory;

/**
 * Customer repository model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CustomerRepository implements CustomerRepositoryInterface
{
    /**
     * @var CustomerSearchResultsInterfaceFactory
     */
    protected $_rewardCustomerSearchResults;

    /**
     * @var \Magestore\Rewardpoints\Helper\Customer
     */
    protected $_customerHelper;
    /**
     * @var \Magestore\Rewardpoints\Model\CustomerFactory
     */
    protected $_rewardCustomerFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\Framework\Webapi\Request
     */
    protected $_request;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * CustomerRepository constructor.
     *
     * @param \Magestore\Rewardpoints\Model\CustomerFactory $rewardCustomerFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magestore\Rewardpoints\Helper\Customer $customerHelper
     * @param CustomerSearchResultsInterfaceFactory $rewardCustomerSearchResults
     * @param \Magento\Framework\Webapi\Request $request
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magestore\Rewardpoints\Model\CustomerFactory $rewardCustomerFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magestore\Rewardpoints\Helper\Customer $customerHelper,
        CustomerSearchResultsInterfaceFactory $rewardCustomerSearchResults,
        \Magento\Framework\Webapi\Request $request,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_rewardCustomerFactory       = $rewardCustomerFactory;
        $this->_customerFactory             = $customerFactory;
        $this->_customerHelper              = $customerHelper;
        $this->_rewardCustomerSearchResults = $rewardCustomerSearchResults;
        $this->_request                     = $request;
        $this->_logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function get($param)
    {
        $rewardCustomers = [];
        /** many emails or ids */
        if (strpos($param, ',') !== false) {
            $params = explode(',', $param);
            foreach ($params as $item) {
                $rewardCustomers[] = $this->getByParam($item);
            }

        } else {
            $rewardCustomers[] = $this->getByParam($param);
        }
        return $rewardCustomers;
    }

    /**
     * Get By Param
     *
     * @param mixed $param
     * @return CustomerInterface
     * @throws \Magento\Framework\Webapi\Exception
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByParam($param)
    {
        if (is_numeric($param)) {
            return $this->getByCustomerId($param);
        } elseif (filter_var($param, FILTER_VALIDATE_EMAIL)) {
            $websiteId = $this->_request->getParam('websiteId');
            if ($websiteId) {
                return $this->getByEmail($param, $websiteId);
            }
            return $this->getByEmail($param);
        } else {
            throw new \Magento\Framework\Webapi\Exception(__('Customer ID or Email is required.'));
        }
    }

    /**
     * Get By Email
     *
     * @param string $email
     * @param string $websiteId
     * @return \Magestore\Rewardpoints\Api\Data\Customer\CustomerInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByEmail($email, $websiteId = 1)
    {
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $this->_customerFactory->create()->setWebsiteId($websiteId)->loadByEmail($email);
        if (!$customer->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __('Customer with email '.$email.' in website '.$websiteId.' does not exist')
            );
        }
        /** @var \Magestore\Rewardpoints\Model\Customer $rewardCustomer */
        $rewardCustomer = $this->_rewardCustomerFactory->create();
        $rewardCustomer->getResource()->load($rewardCustomer, $customer->getId(), 'customer_id');
        if (!$rewardCustomer->getId()) {
            $rewardCustomer->setCustomerId($customer->getId())
                ->setData('point_balance', 0)
                ->setData('holding_balance', 0)
                ->setData('spent_balance', 0)
                ->setData('is_notification', 1)
                ->setData('expire_notification', 1)
                ->save();
        }
        $rewardCustomer->setEmail($email);
        return $rewardCustomer;
    }

    /**
     * Get By Customer Id
     *
     * @param string $customerId
     * @return \Magestore\Rewardpoints\Api\Data\Customer\CustomerInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByCustomerId($customerId)
    {
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $this->_customerFactory->create()->load($customerId);
        if (!$customer->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __(
                    'Customer with ID %1 does not exist',
                    $customerId
                )
            );
        }
        /** @var \Magestore\Rewardpoints\Model\Customer $rewardCustomer */
        $rewardCustomer = $this->_customerHelper->getAccountByCustomer($customer);
        if (!$rewardCustomer->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __(
                    'Reward Customer with customer_id "%1" does not exist',
                    $customerId
                )
            );
        }
        return $rewardCustomer;
    }

    /**
     * @inheritDoc
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Magestore\Rewardpoints\Api\Data\Customer\CustomerSearchResultsInterface $searchResult */
        $searchResult = $this->_rewardCustomerSearchResults->create();
        $searchResult->setSearchCriteria($searchCriteria);
        /** @var \Magestore\Rewardpoints\Model\Customer $rewardCustomer */
        $rewardCustomer           = $this->_rewardCustomerFactory->create();
        $rewardCustomerCollection = $rewardCustomer->getCollection();
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $this->addFilterGroupToCollection($filterGroup, $rewardCustomerCollection);
        }
        $searchResult->setTotalCount($rewardCustomerCollection->getSize());
        /** @var \Magento\Framework\Api\SortOrder[] $sortOrders */
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            foreach ($sortOrders as $sortOrder) {
                $rewardCustomerCollection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == \Magento\Framework\Api\SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $rewardCustomerCollection->setCurPage($searchCriteria->getCurrentPage());
        $rewardCustomerCollection->setPageSize($searchCriteria->getPageSize());
        $items = [];
        foreach ($rewardCustomerCollection as $rewardCustomer) {
            $items[] = $rewardCustomer;
        }
        $searchResult->setItems($items);
        return $searchResult;
    }

    /**
     * Add a FilterGroup to the collection.
     *
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param mixed $collection
     * @return $this
     * @throws \Magento\Framework\Exception\InputException
     */
    public function addFilterGroupToCollection(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        $collection
    ) {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function save(\Magestore\Rewardpoints\Api\Data\Customer\CustomerInterface $rewardCustomer)
    {
        /** @var \Magestore\Rewardpoints\Model\Customer $rewardCustomer */
        $newRewardCustomer = $this->_rewardCustomerFactory->create();
        $this->checkRewardCustomerExist($rewardCustomer)
            ->checkCustomerId($rewardCustomer)
            ->checkCustomerHasAccount($rewardCustomer);
        try {
            $newRewardCustomer
                ->setData(CustomerInterface::REWARD_ID, $rewardCustomer->getRewardId())
                ->setData(CustomerInterface::CUSTOMER_ID, $rewardCustomer->getCustomerId())
                ->setData(CustomerInterface::POINT_BALANCE, $rewardCustomer->getPointBalance())
                ->setData(CustomerInterface::HOLDING_BALANCE, $rewardCustomer->getHoldingBalance())
                ->setData(CustomerInterface::SPENT_BALANCE, $rewardCustomer->getSpentBalance())
                ->setData(CustomerInterface::EXPIRE_NOTIFICATION, $rewardCustomer->getExpireNotification())
                ->setData(CustomerInterface::IS_NOTIFICATION, $rewardCustomer->getIsNotification());
            $newRewardCustomer->getResource()->save($newRewardCustomer);
        } catch (\Exception $e) {
            $this->_logger->critical($e);
        }
        return $newRewardCustomer;
    }

    /**
     * @inheritDoc
     */
    public function update($param, \Magestore\Rewardpoints\Api\Data\Customer\CustomerInterface $rewardCustomer)
    {
        $oldRewardCustomer = $this->getByParam($param);
        try {
            $oldRewardCustomer
                ->setData(CustomerInterface::CUSTOMER_ID, $rewardCustomer->getCustomerId())
                ->setData(CustomerInterface::POINT_BALANCE, $rewardCustomer->getPointBalance())
                ->setData(CustomerInterface::HOLDING_BALANCE, $rewardCustomer->getHoldingBalance())
                ->setData(CustomerInterface::SPENT_BALANCE, $rewardCustomer->getSpentBalance())
                ->setData(CustomerInterface::EXPIRE_NOTIFICATION, $rewardCustomer->getExpireNotification())
                ->setData(CustomerInterface::IS_NOTIFICATION, $rewardCustomer->getIsNotification());
            $oldRewardCustomer->getResource()->save($oldRewardCustomer);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\AlreadyExistsException(
                __($e->getMessage())
            );
        }
        return $this->getByParam($param);
    }

    /**
     * Check Reward Customer Exist
     *
     * @param CustomerInterface $rewardCustomer
     * @return $this
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function checkRewardCustomerExist(
        \Magestore\Rewardpoints\Api\Data\Customer\CustomerInterface $rewardCustomer
    ) {
        /** @var \Magestore\Rewardpoints\Model\Customer $newRewardCustomer */
        $newRewardCustomer = $this->_rewardCustomerFactory->create();
        if ($rewardCustomer->getRewardId()) {
            $newRewardCustomer->getResource()->load(
                $newRewardCustomer,
                $rewardCustomer->getRewardId(),
                \Magestore\Rewardpoints\Api\Data\Customer\CustomerInterface::REWARD_ID
            );
            if ($newRewardCustomer->getId()) {
                throw new \Magento\Framework\Exception\AlreadyExistsException(
                    __(
                        'The reward customer already exists.'
                    )
                );
            }
        }
        return $this;
    }

    /**
     * Check Customer Id
     *
     * @param CustomerInterface $rewardCustomer
     * @return $this
     * @throws \Magento\Framework\Exception\NotFoundException
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function checkCustomerId(\Magestore\Rewardpoints\Api\Data\Customer\CustomerInterface $rewardCustomer)
    {
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $this->_customerFactory->create();
        if ($rewardCustomer->getCustomerId()) {
            $customer->getResource()->load($customer, $rewardCustomer->getCustomerId());
            if (!$customer->getId()) {
                throw new \Magento\Framework\Exception\NotFoundException(
                    __('Cannot find customer with id ' . $rewardCustomer->getCustomerId())
                );
            }
        } else {
            throw new \Magento\Framework\Webapi\Exception(__('"customer_id" is required.'));
        }
        return $this;
    }

    /**
     * Check Customer Has Account
     *
     * @param CustomerInterface $rewardCustomer
     * @return $this
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function checkCustomerHasAccount(\Magestore\Rewardpoints\Api\Data\Customer\CustomerInterface $rewardCustomer)
    {
        /** @var \Magestore\Rewardpoints\Model\Customer $newRewardCustomer */
        $newRewardCustomer = $this->_rewardCustomerFactory->create();
        if ($rewardCustomer->getCustomerId()) {
            $newRewardCustomer->getResource()->load(
                $newRewardCustomer,
                $rewardCustomer->getCustomerId(),
                \Magestore\Rewardpoints\Api\Data\Customer\CustomerInterface::CUSTOMER_ID
            );
            if ($newRewardCustomer->getId()) {
                throw new \Magento\Framework\Exception\AlreadyExistsException(
                    __(
                        'Customer has reward account already.'
                    )
                );
            }
        }
        return $this;
    }
}
