<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosSampleData\Model\FixtureGenerator\Order;

use Magento\Catalog\Model\Product\Type;

/**
 * Class OrdersFixture
 *
 * @package Magestore\PosSampleData\Model\FixtureGenerator\Order
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class OrdersFixture
{
    /**
     * Batch size for order generation.
     *
     * @var string
     */
    const BATCH_SIZE = 1000;

    /**
     * INSERT query templates.
     *
     * @var array
     */
    protected $queryTemplates;

    /**
     * Array of resource connections ordered by tables.
     *
     * @var \Magento\Framework\DB\Adapter\AdapterInterface[]
     */
    protected $resourceConnections;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\ConfigurableProduct\Api\OptionRepositoryInterface
     */
    protected $optionRepository;

    /**
     * @var \Magento\ConfigurableProduct\Api\LinkManagementInterface
     */
    protected $linkManagement;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * Flag specifies if inactive quotes should be generated for orders.
     *
     * @var bool
     */
    protected $orderQuotesEnable = true;
    /**
     * number_requested_order
     * product_sku
     * payment_reference_number
     * searching_prefix
     * customer_email
     *
     * @var array
     */
    protected $additionalData = [];

    /**
     * OrdersFixture constructor.
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\ConfigurableProduct\Api\OptionRepositoryInterface $optionRepository
     * @param \Magento\ConfigurableProduct\Api\LinkManagementInterface $linkManagement
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param array $additionalData
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\ConfigurableProduct\Api\OptionRepositoryInterface $optionRepository,
        \Magento\ConfigurableProduct\Api\LinkManagementInterface $linkManagement,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        $additionalData = []
    ) {
        $this->storeManager = $storeManager;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productRepository = $productRepository;
        $this->optionRepository = $optionRepository;
        $this->linkManagement = $linkManagement;
        $this->serializer = $serializer;
        $this->objectManager = $objectManager;
        $this->customerRepository = $customerRepository;
        $this->additionalData = $additionalData;
    }

    /**
     * Generate orders
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD)
     */
    public function execute()
    {
        if (isset($this->additionalData['number_requested_order']) && $this->additionalData['number_requested_order']) {
            $requestedOrders = $this->additionalData['number_requested_order'];
        } else {
            return false;
        }
        $orderSimpleCountTo = 1;
        try {
            $customer = $this->customerRepository->get($this->additionalData['customer_email']);
        } catch (\Exception $e) {
            return false;
        }

        $entityId = $this->getMaxEntityId(
            'sales_order',
            \Magento\Sales\Model\ResourceModel\Order::class,
            'entity_id'
        );

        $maxItemId = $this->getMaxEntityId(
            'sales_order_item',
            \Magento\Sales\Model\ResourceModel\Order\Item::class,
            'item_id'
        );

        $ruleId = $this->getMaxEntityId(
            'salesrule',
            \Magento\SalesRule\Model\ResourceModel\Rule::class,
            'rule_id'
        );
        $maxItemsPerOrder = $orderSimpleCountTo;

        /** @var \Generator $itemIdSequence */
        $itemIdSequence = $this->getItemIdSequence($maxItemId, $requestedOrders, $maxItemsPerOrder);

        $this->prepareQueryTemplates();

        $result = [];
        $store = $this->storeManager->getDefaultStoreView();
        $productsResult = [];
        $this->storeManager->setCurrentStore($store->getId());

        if ($orderSimpleCountTo > 0) {
            $productsResult[Type::TYPE_SIMPLE] = $this->prepareSimpleProducts(
                $this->getProductIds($store, Type::TYPE_SIMPLE, $orderSimpleCountTo)
            );
        }

        $result[] = [
            $store->getId(),
            implode(
                PHP_EOL,
                [
                    $this->storeManager->getWebsite($store->getWebsiteId())->getName(),
                    $this->storeManager->getGroup($store->getStoreGroupId())->getName(),
                    $store->getName()
                ]
            ),
            $productsResult
        ];

        $productStoreId = function ($index) use ($result) {
            return $result[$index % count($result)][0];
        };
        $productStoreName = function ($index) use ($result) {
            return $result[$index % count($result)][1];
        };
        $productId = function ($entityId, $index, $type) use ($result) {
            return $result[$entityId % count($result)][2][$type][$index]['id'];
        };
        $productSku = function ($entityId, $index, $type) use ($result) {
            return $result[$entityId % count($result)][2][$type][$index]['sku'];
        };
        $productName = function ($entityId, $index, $type) use ($result) {
            return $result[$entityId % count($result)][2][$type][$index]['name'];
        };
        $productBuyRequest = function ($entityId, $index, $type) use ($result) {
            return $result[$entityId % count($result)][2][$type][$index]['buyRequest'];
        };

        $customerAddress = '';
        if (count($customer->getAddresses())) {
            $customerAddress = $customer->getAddresses()[0];
        }
        if (isset($this->additionalData['searching_prefix'])) {
            $customerSearchingPrefix = $this->additionalData['searching_prefix'];
        } else {
            $customerSearchingPrefix = '';
        }
        $address = [
            '%firstName%' => ($customerSearchingPrefix ? 'firstname_' . $customerSearchingPrefix : '')
                . $customer->getFirstname(),
            '%lastName%' => ($customerSearchingPrefix ? 'lastname_' . $customerSearchingPrefix : '')
                . $customer->getLastname(),
            '%company%' => $customerAddress ? $customerAddress->getCompany() : 'Company',
            '%address%' => $customerAddress ? $customerAddress->getStreet()[0] : 'Address',
            '%city%' => $customerAddress ? $customerAddress->getCity() : 'city',
            '%state%' => $customerAddress ? $customerAddress->getRegion()->getRegion() : 'Alabama',
            '%country%' => $customerAddress ? $customerAddress->getCountryId() : 'US',
            '%zip%' => $customerAddress ? $customerAddress->getPostcode() : '11111',
            '%phone%' => ($customerSearchingPrefix ? 'telephone_' . $customerSearchingPrefix : '')
                . ($customerAddress ? $customerAddress->getTelephone() : '911')
        ];

        $batchNumber = 0;
        $entityId++;
        $ordersCount = 1;
        $paymentReferenceNumber = '';
        if (isset($this->additionalData['payment_reference_number'])) {
            $paymentReferenceNumber = $this->additionalData['payment_reference_number'];
        }
        while ($ordersCount <= $requestedOrders) {
            $batchNumber++;
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            $productCount = [
                Type::TYPE_SIMPLE => 1
            ];
            $order = [
                '%itemsPerOrder%' => array_sum($productCount),
                '%orderNumber%' => ($customerSearchingPrefix ? 'increment_id_' . $customerSearchingPrefix : '')
                    . (100000000 * $productStoreId($entityId) + $entityId),
                '%email%' => ($customerSearchingPrefix ? 'email_' . $customerSearchingPrefix : '')
                    . $customer->getEmail(),
                '%time%' => date(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT),
                '%productStoreId%' => $productStoreId($entityId),
                '%productStoreName%' => $productStoreName($entityId),
                '%entityId%' => $entityId,
                '%paymentReferenceNumber%' => $paymentReferenceNumber,
                '%ruleId%' => $ruleId,
            ];
            $shippingAddress = ['%orderAddressId%' => $entityId * 2 - 1, '%addressType%' => 'shipping'];
            $billingAddress = ['%orderAddressId%' => $entityId * 2, '%addressType%' => 'billing'];

            try {
                $this->query('eav_entity_store', $order);
                $this->query('sales_order', $order);
                $this->query('sales_order_address', $order, $address, $shippingAddress);
                $this->query('sales_order_address', $order, $address, $billingAddress);
                $this->query('sales_order_grid', $order);
                $this->query('sales_order_payment', $order);
                $this->query('sales_order_status_history', $order);

                for ($i = 0; $i < $productCount[Type::TYPE_SIMPLE]; $i++) {
                    $itemData = [
                        '%productId%' => $productId($entityId, $i, Type::TYPE_SIMPLE),
                        '%sku%' => $productSku($entityId, $i, Type::TYPE_SIMPLE),
                        '%name%' => $productName($entityId, $i, Type::TYPE_SIMPLE),
                        '%itemId%' => $itemIdSequence->current(),
                        '%productType%' => Type::TYPE_SIMPLE,
                        '%productOptions%' => $productBuyRequest($entityId, $i, Type::TYPE_SIMPLE),
                        '%parentItemId%' => 'null',
                    ];
                    $this->query('sales_order_item', $order, $itemData);
                    $itemIdSequence->next();
                }
            } catch (\Exception $lastException) {
                foreach ($this->resourceConnections as $connection) {
                    if ($connection->getTransactionLevel() > 0) {
                        $connection->rollBack();
                    }
                }
                throw $lastException;
            }

            if ($batchNumber >= self::BATCH_SIZE) {
                $this->commitBatch();
                $batchNumber = 0;
            }
            $entityId++;
            $ordersCount++;
        }

        foreach ($this->resourceConnections as $connection) {
            if ($connection->getTransactionLevel() > 0) {
                $connection->commit();
            }
        }
    }

    /**
     * Load and prepare INSERT query templates data from external file.
     *
     * Queries are prepared using external json file, where keys are DB column names and values represent data,
     * to be inserted to the table. Data may contain a default value or a placeholder which is replaced later during
     * flow (in the query method of this class).
     * Additionally, in case if multiple DB connections are set up, transaction is started for each connection.
     *
     * @return void
     */
    protected function prepareQueryTemplates()
    {
        $fileName = __DIR__ . DIRECTORY_SEPARATOR . "_files" . DIRECTORY_SEPARATOR . "orders_fixture_data.json";
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        $templateData = json_decode(file_get_contents(realpath($fileName)), true);
        foreach ($templateData as $table => $template) {
            if (isset($template['_table'])) {
                $table = $template['_table'];
                unset($template['_table']);
            }
            if (isset($template['_resource'])) {
                $resource = $template['_resource'];
                unset($template['_resource']);
            } else {
                $resource = explode("_", $table);
                foreach ($resource as &$item) {
                    $item = ucfirst($item);
                }
                $resource = "Magento\\"
                    . array_shift($resource)
                    . "\\Model\\ResourceModel\\"
                    . implode("\\", $resource);
            }

            $tableName = $this->getTableName($table, $resource);

            $querySuffix = "";
            if (isset($template['_query_suffix'])) {
                $querySuffix = $template['_query_suffix'];
                unset($template['_query_suffix']);
            }

            $fields = implode(', ', array_keys($template));
            $values = implode(', ', array_values($template));

            /** @var \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resourceModel */
            $resourceModel = $this->objectManager->get($resource);
            $connection = $resourceModel->getConnection();
            if ($connection->getTransactionLevel() == 0) {
                $connection->beginTransaction();
            }

            // phpcs:ignore Magento2.SQL.RawQuery
            $this->queryTemplates[$table] = "INSERT INTO `{$tableName}` ({$fields}) VALUES ({$values}){$querySuffix};";
            $this->resourceConnections[$table] = $connection;
        }
    }

    /**
     * Build and execute query.
     *
     * Builds a database query by replacing placeholder values in the cached queries and executes query in appropriate
     * DB connection (if setup). Additionally filters out quote-related queries, if appropriate flag is set.
     *
     * @param string $table
     * @param array $replacements
     * @return void
     */
    protected function query($table, ... $replacements)
    {
        if (!$this->orderQuotesEnable && strpos($table, "quote") !== false) {
            return;
        }
        $query = $this->queryTemplates[$table];
        foreach ($replacements as $data) {
            $query = str_replace(array_keys($data), array_values($data), $query);
        }

        $this->resourceConnections[$table]->query($query);
    }

    /**
     * Get maximum order id currently existing in the database.
     *
     * To support incremental generation of the orders it is necessary to get the maximum order entity_id currently
     * existing in the database.
     *
     * @param string $tableName
     * @param string $resourceName
     * @param string $column
     * @return int
     */
    protected function getMaxEntityId($tableName, $resourceName, $column = 'entity_id')
    {
        $tableName = $this->getTableName(
            $tableName,
            $resourceName
        );

        /** @var \Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb $resource */
        $resource = $this->objectManager->get($resourceName);
        $connection = $resource->getConnection();
        // phpcs:ignore Magento2.SQL.RawQuery
        return (int)$connection->query("SELECT MAX(`{$column}`) FROM `{$tableName}`;")->fetchColumn(0);
    }

    /**
     * Get a limited amount of product id's from a collection filtered by store and specific product type.
     *
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @param string $typeId
     * @param int $limit
     * @return array
     * @throws \RuntimeException
     */
    protected function getProductIds(\Magento\Store\Api\Data\StoreInterface $store, $typeId, $limit = null)
    {
        $specificSku = '';
        if (isset($this->additionalData['product_sku']) && $this->additionalData['product_sku']) {
            $specificSku = $this->additionalData['product_sku'];
        }

        /** @var $productCollection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $productCollection = $this->productCollectionFactory->create();

        $productCollection->addStoreFilter($store->getId());
        $productCollection->addWebsiteFilter($store->getWebsiteId());

        $productCollection->getSelect()->where(" type_id = '$typeId' ");
        if ($specificSku) {
            $productCollection->getSelect()->where(" sku LIKE '%$specificSku%' ");
        }

        $ids = $productCollection->getAllIds($limit);

        if ($limit && count($ids) < $limit) {
            throw new \RuntimeException('Not enough products of type: ' . $typeId);
        }
        return $ids;
    }

    /**
     * Prepare data for the simple products to be used as order items.
     *
     * Based on the Product Id's load data, which is required to replace placeholders in queries.
     *
     * @param array $productIds
     * @return array
     */
    protected function prepareSimpleProducts(array $productIds = [])
    {
        $productsResult = [];
        if (isset($this->additionalData['searching_prefix'])) {
            $customerSearchingPrefix = $this->additionalData['searching_prefix'];
        } else {
            $customerSearchingPrefix = '';
        }
        foreach ($productIds as $key => $simpleId) {
            $simpleProduct = $this->productRepository->getById($simpleId);
            $productsResult[$key]['id'] = $simpleId;
            $productsResult[$key]['sku'] = $simpleProduct->getSku();
            $productsResult[$key]['name'] = ($customerSearchingPrefix ? 'product_name_' . $customerSearchingPrefix : '')
                . $simpleProduct->getName();
            $productsResult[$key]['buyRequest'] = $this->serializer->serialize(
                [
                    "info_buyRequest" => [
                        "uenc" => "aHR0cDovL21hZ2VudG8uZGV2L2NvbmZpZ3VyYWJsZS1wcm9kdWN0LTEuaHRtbA,,",
                        "product" => $simpleId,
                        "qty" => "1"
                    ]
                ]
            );
        }
        return $productsResult;
    }

    /**
     * Commit all active transactions at the end of the batch.
     *
     * Many transactions may exist, since generation process creates a transaction per each available DB connection.
     *
     * @return void
     */
    protected function commitBatch()
    {
        foreach ($this->resourceConnections as $connection) {
            if ($connection->getTransactionLevel() > 0) {
                $connection->commit();
                $connection->beginTransaction();
            }
        }
    }

    /**
     * Get real table name for db table, validated by db adapter.
     *
     * In case prefix or other features mutating default table names are used.
     *
     * @param string $tableName
     * @param string $resourceName
     * @return string
     */
    public function getTableName($tableName, $resourceName)
    {
        /** @var \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource */
        $resource = $this->objectManager->get($resourceName);
        return $resource->getConnection()->getTableName($resource->getTable($tableName));
    }

    /**
     * Get sequence for order items
     *
     * @param int $maxItemId
     * @param int $requestedOrders
     * @param int $maxItemsPerOrder
     * @return \Generator
     */
    protected function getItemIdSequence($maxItemId, $requestedOrders, $maxItemsPerOrder)
    {
        $requestedItems = $requestedOrders * $maxItemsPerOrder;
        for ($i = $maxItemId + 1; $i <= $requestedItems; $i++) {
            yield $i;
        }
    }
}
