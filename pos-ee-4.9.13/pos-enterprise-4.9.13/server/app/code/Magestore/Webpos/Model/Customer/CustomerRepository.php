<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Customer;

use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Model\Customer\NotificationStorage;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ImageProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;

/**
 * Class CustomerRepository
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class CustomerRepository implements \Magestore\Webpos\Api\Customer\CustomerRepositoryInterface
{
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Customer\Model\Data\CustomerSecureFactory
     */
    protected $customerSecureFactory;

    /**
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * @var \Magento\Customer\Model\ResourceModel\AddressRepository
     */
    protected $addressRepository;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer
     */
    protected $customerResourceModel;

    /**
     * @var \Magento\Customer\Api\CustomerMetadataInterface
     */
    protected $customerMetadata;

    /**
     * @var \Magento\Customer\Api\Data\CustomerSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var ImageProcessorInterface
     */
    protected $imageProcessor;

    /**
     * @var \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var NotificationStorage
     */
    private $notificationStorage;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Grid\CollectionFactory
     */
    protected $gridCollection;

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $subscriberFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $helper;

    /**
     * @var GridFactory
     */
    protected $customerGridFactory;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Customer\Model\Config\Share
     */
    protected $customerShareConfig;

    /**
     * CustomerRepository constructor.
     *
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Model\Data\CustomerSecureFactory $customerSecureFactory
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     * @param \Magento\Customer\Model\ResourceModel\AddressRepository $addressRepository
     * @param \Magento\Customer\Model\ResourceModel\Customer $customerResourceModel
     * @param CustomerMetadataInterface $customerMetadata
     * @param \Magestore\Webpos\Api\Data\Customer\CustomerSearchResultsInterfaceFactory $searchResultsFactory
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param DataObjectHelper $dataObjectHelper
     * @param ImageProcessorInterface $imageProcessor
     * @param \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param NotificationStorage $notificationStorage
     * @param \Magento\Customer\Model\ResourceModel\Grid\CollectionFactory $gridCollection
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param GridFactory $customerGridFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magestore\Webpos\Helper\Data $helper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Customer\Model\Config\Share $customerShareConfig
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Data\CustomerSecureFactory $customerSecureFactory,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Customer\Model\ResourceModel\AddressRepository $addressRepository,
        \Magento\Customer\Model\ResourceModel\Customer $customerResourceModel,
        \Magento\Customer\Api\CustomerMetadataInterface $customerMetadata,
        \Magestore\Webpos\Api\Data\Customer\CustomerSearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        DataObjectHelper $dataObjectHelper,
        ImageProcessorInterface $imageProcessor,
        \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor,
        NotificationStorage $notificationStorage,
        \Magento\Customer\Model\ResourceModel\Grid\CollectionFactory $gridCollection,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Magestore\Webpos\Model\Customer\GridFactory $customerGridFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magestore\Webpos\Helper\Data $helper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Model\Config\Share $customerShareConfig
    ) {
        $this->customerFactory = $customerFactory;
        $this->customerSecureFactory = $customerSecureFactory;
        $this->customerRegistry = $customerRegistry;
        $this->addressRepository = $addressRepository;
        $this->customerResourceModel = $customerResourceModel;
        $this->customerMetadata = $customerMetadata;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->eventManager = $eventManager;
        $this->storeManager = $storeManager;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->imageProcessor = $imageProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->notificationStorage = $notificationStorage;
        $this->gridCollection = $gridCollection;
        $this->subscriberFactory = $subscriberFactory;
        $this->logger = $logger;
        $this->customerGridFactory = $customerGridFactory;
        $this->helper = $helper;
        $this->objectManager = $objectManager;
        $this->customerShareConfig = $customerShareConfig;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function save(\Magestore\Webpos\Api\Data\Customer\CustomerInterface $customer, $passwordHash = null)
    {
        // phpcs:disable
        $delegatedClassName = "Magento\Customer\Model\Delegation\Storage";
        if (class_exists($delegatedClassName)) {
            $delegatedStorage = $this->objectManager->create($delegatedClassName);
        } else {
            $delegatedStorage = false;
        }
        if ($delegatedStorage) {
            $delegatedNewOperation = !$customer->getId()
                ? $delegatedStorage->consumeNewOperation() : null;
        }

        $prevCustomerData = null;
        $prevCustomerDataArr = null;
        $isExistingCustomer = false;

        if ($customer->getId()) {
            $isExistingCustomer = true;
            $prevCustomerData = $this->getById($customer->getId());
            $prevCustomerDataArr = $prevCustomerData->__toArray();
        }
        /** @var $customer \Magestore\Webpos\Model\Customer\Data\Customer */
        $customerArr = $customer->__toArray();

        $customer = $this->imageProcessor->save(
            $customer,
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            $prevCustomerData
        );

        $origAddresses = $customer->getAddresses();
        $customer->setAddresses([]);
        $customerData = $this->extensibleDataObjectConverter->toNestedArray(
            $customer,
            [],
            \Magestore\Webpos\Api\Data\Customer\CustomerInterface::class
        );
        $customer->setAddresses($origAddresses);
        $customerModel = $this->customerFactory->create(['data' => $customerData]);
        $customerModel->setData('customer_telephone', $customer->getTelephone());
        $storeId = $customerModel->getStoreId();
        if ($storeId === null) {
            $customerModel->setStoreId($this->helper->getCurrentStoreView()->getId());
        }
        $customerModel->setId($customer->getId());

        // Need to use attribute set or future updates can cause data loss
        if (!$customerModel->getAttributeSetId()) {
            $customerModel->setAttributeSetId(
                \Magento\Customer\Api\CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER
            );
        }
        $this->populateCustomerWithSecureData($customerModel, $passwordHash);

        // If customer email was changed, reset RpToken info
        if ($prevCustomerData
            && $prevCustomerData->getEmail() !== $customerModel->getEmail()
        ) {
            $customerModel->setRpToken(null);
            $customerModel->setRpTokenCreatedAt(null);
        }
        if (!array_key_exists('default_billing', $customerArr) &&
            null !== $prevCustomerDataArr &&
            array_key_exists('default_billing', $prevCustomerDataArr)
        ) {
            $customerModel->setDefaultBilling($prevCustomerDataArr['default_billing']);
        }

        if (!array_key_exists('default_shipping', $customerArr) &&
            null !== $prevCustomerDataArr &&
            array_key_exists('default_shipping', $prevCustomerDataArr)
        ) {
            $customerModel->setDefaultShipping($prevCustomerDataArr['default_shipping']);
        }

        try {
            if ($isExistingCustomer) {
                $this->saveAddress($customer->getId(), $customer);
                $customerModel->setDefaultShipping($customer->getDefaultShipping());
                $customerModel->setDefaultBilling($customer->getDefaultBilling());
            }
            $customerModel->save();
            $customerModel->getResource()->addCommitCallback([$customerModel, 'reindex']);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        $this->customerRegistry->push($customerModel);
        $customerId = $customerModel->getId();

        if (!$isExistingCustomer) {
            $this->saveAddress($customerId, $customer);
        }

        $this->customerRegistry->remove($customerId);

        $isSubscriber = $customer->getSubscriberStatus();
        $subscriberModel = $this->subscriberFactory->create()->loadByCustomerId($customerId);
        if ($isSubscriber) {
            $this->addSubscriber($customerId);
        } else {
            if ($subscriberModel->getId() && $subscriberModel->getData('subscriber_status')) {
                try {
                    $subscriberModel->unsubscribe();
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage());
                }
            }
        }

        $savedCustomer = $this->get($customer->getEmail(), $customer->getWebsiteId());

        $customerGridModel = $this->customerGridFactory->create()->load($savedCustomer->getId());
        $savedCustomer->setData('telephone', $customerGridModel->getData('billing_telephone'));
        $savedCustomer->setData('full_name', $customerGridModel->getData('name'));
        $eventData = ['customer_data_object' => $savedCustomer, 'orig_customer_data_object' => $prevCustomerData];
        if ($delegatedStorage) {
            $eventData['delegate_data'] = $delegatedNewOperation? $delegatedNewOperation->getAdditionalData() : [];
        }
        $this->eventManager->dispatch('customer_save_after_data_object', $eventData);

        return $savedCustomer;
    }

    /**
     * Save address
     *
     * @param int $customerId
     * @param \Magestore\Webpos\Api\Data\Customer\CustomerInterface $customer
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function saveAddress($customerId, \Magestore\Webpos\Api\Data\Customer\CustomerInterface $customer)
    {
        if ($customer->getAddresses() !== null) {
            if ($customer->getId()) {
                $existingAddresses = $this->getById($customer->getId())->getAddresses();
                $getIdFunc = function ($address) {
                    return $address->getId();
                };
                $existingAddressIds = array_map($getIdFunc, $existingAddresses);
            } else {
                $existingAddressIds = [];
            }

            $savedAddressIds = [];
            foreach ($customer->getAddresses() as $address) {
                $address->setCustomerId($customerId)
                    ->setRegion($address->getRegion());
                $this->addressRepository->save($address);
                if ($address->getId()) {
                    $savedAddressIds[] = $address->getId();
                    if ($address->isDefaultBilling()) {
                        $customer->setDefaultBilling($address->getId());
                    }
                    if ($address->isDefaultShipping()) {
                        $customer->setDefaultShipping($address->getId());
                    }
                }
            }

            $addressIdsToDelete = array_diff($existingAddressIds, $savedAddressIds);
            foreach ($addressIdsToDelete as $addressId) {
                $this->addressRepository->deleteById($addressId);
            }
        }
    }

    /**
     * Add customer subscriber
     *
     * @param int $customerId
     */
    public function addSubscriber($customerId)
    {
        if ($customerId) {
            $subscriberModel = $this->subscriberFactory->create()->loadByCustomerId($customerId);
            if ($subscriberModel->getId() === null) {
                try {
                    $this->subscriberFactory->create()->subscribeCustomerById($customerId);
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->logger->critical($e->getMessage());
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage());
                }

            } elseif ($subscriberModel->getData('subscriber_status') != 1) {
                $subscriberModel->setData('subscriber_status', 1);
                try {
                    $subscriberModel->save();
                } catch (\Exception $e) {
                    $this->logger->critical($e);
                }
            }
        }
    }

    /**
     * Set secure data to customer model
     *
     * @param \Magento\Customer\Model\Customer $customerModel
     * @param string|null $passwordHash
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @return void
     */
    private function populateCustomerWithSecureData($customerModel, $passwordHash = null)
    {
        if ($customerModel->getId()) {
            $customerSecure = $this->customerRegistry->retrieveSecureData($customerModel->getId());

            $customerModel->setRpToken($passwordHash ? null : $customerSecure->getRpToken());
            $customerModel->setRpTokenCreatedAt($passwordHash ? null : $customerSecure->getRpTokenCreatedAt());
            $customerModel->setPasswordHash($passwordHash ?: $customerSecure->getPasswordHash());

            $customerModel->setFailuresNum($customerSecure->getFailuresNum());
            $customerModel->setFirstFailure($customerSecure->getFirstFailure());
            $customerModel->setLockExpires($customerSecure->getLockExpires());
        } elseif ($passwordHash) {
            $customerModel->setPasswordHash($passwordHash);
        }

        if ($passwordHash && $customerModel->getId()) {
            $this->customerRegistry->remove($customerModel->getId());
        }
    }

    /**
     * @inheritdoc
     */
    public function get($email, $websiteId = null)
    {
        $customerModel = $this->customerRegistry->retrieveByEmail($email, $websiteId);
        $customerData = $customerModel->getDataModel();
        $checkSubscriber = $this->subscriberFactory->create()->loadByCustomerId($customerModel->getId());

        if ($checkSubscriber->isSubscribed()) {
            $customerData->setSubscriberStatus(1);
        } else {
            $customerData->setSubscriberStatus(0);
        }
        return $customerData;
    }

    /**
     * @inheritdoc
     */
    public function getById($customerId)
    {
        $customerModel = $this->customerRegistry->retrieve($customerId);
        return $customerModel->getDataModel();
    }

    /**
     * Prepare and get customer collection
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param bool $customerFilter
     * @return \Magento\Customer\Model\ResourceModel\Customer\Collection|mixed
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerCollection($searchCriteria, $customerFilter = false)
    {
        /** @var \Magento\Customer\Model\ResourceModel\Customer\Collection $collection */
        $collection = $this->customerFactory->create()->getCollection();

        if ($this->customerShareConfig->isWebsiteScope()) {
            $collection->addFieldToFilter('website_id', $this->helper->getCurrentStoreView()->getWebsiteId());
        }

        //Add filters from root filter group to the collection
        if (!$customerFilter) {
            foreach ($searchCriteria->getFilterGroups() as $group) {
                $this->addFilterGroupToCollection($group, $collection);
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Magestore\Webpos\Api\Data\Customer\CustomerInterface::class
        );
        // This is needed to make sure all the attributes are properly loaded
        foreach ($this->customerMetadata->getAllAttributesMetadata() as $metadata) {
            $collection->addAttributeToSelect($metadata->getAttributeCode());
        }
        // Needed to enable filtering on name as a whole
        $collection->addNameToSelect();
        // Needed to enable filtering based on billing address attributes
        $collection->getSelect()
            ->joinLeft(
                ['customer_grid' => $collection->getTable('customer_grid_flat')],
                'e.entity_id = customer_grid.entity_id',
                ['customer_grid.name', 'customer_grid.billing_telephone']
            )
            ->joinLeft(
                ['ns' => $collection->getTable('newsletter_subscriber')],
                'e.entity_id = ns.customer_id',
                ['ns.subscriber_status']
            )
            ->columns('IFNULL(customer_grid.billing_telephone,"") AS telephone')
            ->columns('customer_grid.name AS full_name')
            ->columns('IF(ns.subscriber_status = 1, "1", "0") AS subscriber_status');

        $sortOrders = $searchCriteria->getSortOrders();
        $collection = $this->setCustomerCollectionOrder($collection, $sortOrders);

        return $collection;
    }

    /**
     * Set sortorder to collection
     *
     * @param \Magento\Customer\Model\ResourceModel\Customer\Collection $collection
     * @param array $sortOrders
     * @return mixed
     */
    public function setCustomerCollectionOrder($collection, $sortOrders)
    {
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                if ($sortOrder->getField() == 'full_name') {
                    $collection->getSelect()->order('customer_grid.name');
                }
                if ($sortOrder->getField() == 'telephone') {
                    $collection->getSelect()->order('customer_grid.billing_telephone');
                }
                if ($sortOrder->getField() == 'subscriber_status') {
                    $collection->getSelect()->order('ns.subscriber_status');
                }
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        return $collection;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $objectManager = $this->objectManager;

        /** @var \Magento\Framework\App\Cache\StateInterface $cacheState */
        $cacheState = $objectManager->get(\Magento\Framework\App\Cache\StateInterface::class);
        if (!$cacheState->isEnabled(\Magestore\Webpos\Model\Cache\Type::TYPE_IDENTIFIER)
            || count($searchCriteria->getFilterGroups())
            || $searchCriteria->getCurrentPage() < 2 // Do not cache for first page
        ) {
            return $this->processGetList($searchCriteria);
        }

        /** @var \Magento\Framework\App\CacheInterface $cache */
        $cache = $objectManager->get(\Magento\Framework\App\CacheInterface::class);

        $key = 'syncCustomers-'
            . $searchCriteria->getPageSize()
            . '-' . $searchCriteria->getCurrentPage()
            . '-' . $this->helper->getCurrentStoreView()->getWebsiteId();

        if ($response = $cache->load($key)) {
            // Reponse from cache
            return json_decode($response, true);
        }

        $flag = \Magestore\Webpos\Api\SyncInterface::QUEUE_NAME;
        if ($cachedAt = $cache->load($flag)) {
            return [
                'async_stage'   => 2, // processing
                'cached_at'     => $cachedAt, // process started time
            ];
        }

        // Block metaphor
        $cachedAt = date("Y-m-d H:i:s");
        $cache->save($cachedAt, $flag, [\Magestore\Webpos\Model\Cache\Type::CACHE_TAG], 300);

        /** @var \Magento\Framework\Webapi\ServiceOutputProcessor $processor */
        $processor = $objectManager->get(\Magento\Framework\Webapi\ServiceOutputProcessor::class);
        $outputData = $processor->process(
            $this->processGetList($searchCriteria),
            \Magestore\Webpos\Api\Customer\CustomerRepositoryInterface::class,
            'getList'
        );
        $outputData['cached_at'] = $cachedAt;
        $cache->save(
            json_encode($outputData),
            $key,
            [
                \Magestore\Webpos\Model\Cache\Type::CACHE_TAG,
            ],
            86400   // Cache lifetime: 1 day
        );
        // Release metaphor
        $cache->remove($flag);
        return $outputData;
    }

    /**
     * Process collection
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return mixed
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function processGetList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        /** @var \Magento\Customer\Model\ResourceModel\Customer\Collection $collection */
        $collection = $this->getCustomerCollection($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $customers = [];
        /** @var \Magento\Customer\Model\Customer $customerModel */
        foreach ($collection as $customerModel) {
            $customers[] = $customerModel->getDataModel();
        }
        $searchResults->setItems($customers);
        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function search(\Magestore\Webpos\Api\SearchCriteriaInterface $searchCriteria)
    {
        $queryString = $searchCriteria->getQueryString();
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $collection = $this->gridCollection->create()->addFieldToFilter(
            ['name', 'email', 'billing_telephone'],
            [
                ['like' => '%' . $queryString . '%'],
                ['like' => '%' . $queryString . '%'],
                ['like' => '%' . $queryString . '%']
            ]
        );

        if ($this->customerShareConfig->isWebsiteScope()) {
            $collection->addFieldToFilter('website_id', $this->helper->getCurrentStoreView()->getWebsiteId());
        }

        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                if ($sortOrder->getField() == 'full_name') {
                    $sortOrder->setField('name');
                }
                if ($sortOrder->getField() == 'telephone') {
                    $sortOrder->setField('billing_telephone');
                }
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? SortOrder::SORT_ASC : SortOrder::SORT_DESC
                );
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $searchResults->setTotalCount($collection->getSize());

        $customers = [];
        foreach ($collection as $customerModel) {
            $customerInfoModel = $this->get($customerModel->getEmail(), $customerModel->getWebsiteId());
            $customerInfoModel->setData('telephone', $customerModel->getData('billing_telephone'));
            $customerInfoModel->setData('full_name', $customerModel->getData('name'));
            $customers[] = $customerInfoModel;
        }
        $searchResults->setItems($customers);
        return $searchResults;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Magento\Customer\Model\ResourceModel\Customer\Collection $collection
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    public function addFilterGroupToCollection(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Customer\Model\ResourceModel\Customer\Collection $collection
    ) {
        $fields = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[] = ['attribute' => $filter->getField(), $condition => $filter->getValue()];
        }
        if ($fields) {
            $collection->addFieldToFilter($fields);
        }
    }
}
