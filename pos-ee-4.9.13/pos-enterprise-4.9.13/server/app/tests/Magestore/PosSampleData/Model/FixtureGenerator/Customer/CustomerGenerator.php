<?php
/**
 * Copyright © 2019 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace Magestore\PosSampleData\Model\FixtureGenerator\Customer;

use Magento\Customer\Api\Data\CustomerInterface;
use Magestore\PosSampleData\Model\FixtureGenerator\Customer\CustomerDataGenerator;
use Magestore\PosSampleData\Model\FixtureGenerator\Customer\CustomerDataGeneratorFactory;

/**
 * Class CustomerGenerator
 *
 * @package Magestore\PosSampleData\Model\FixtureGenerator\Customer
 */
class CustomerGenerator
{
    /**
     * @var EntityGeneratorFactory
     */
    private $entityGeneratorFactory;

    /**
     * @var CustomerTemplateGenerator
     */
    private $customerTemplateGenerator;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    /**
     * @var CustomerDataGeneratorFactory
     */
    private $customerDataGeneratorFactory;

    /**
     * CustomerGenerator constructor.
     *
     * @param EntityGeneratorFactory $entityGeneratorFactory
     * @param CustomerTemplateGenerator $customerTemplateGenerator
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param CustomerDataGeneratorFactory $customerDataGeneratorFactory
     */
    public function __construct(
        \Magento\Setup\Model\FixtureGenerator\EntityGeneratorFactory $entityGeneratorFactory,
        \Magento\Setup\Model\FixtureGenerator\CustomerTemplateGenerator $customerTemplateGenerator,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        CustomerDataGeneratorFactory $customerDataGeneratorFactory
    ) {
        $this->entityGeneratorFactory = $entityGeneratorFactory;
        $this->customerTemplateGenerator = $customerTemplateGenerator;
        $this->resourceConnection = $resourceConnection;
        $this->customerDataGeneratorFactory = $customerDataGeneratorFactory;
    }

    /**
     * Generate
     *
     * @param int $numberCustomer
     * @param int $numberAddress
     */
    public function generate($numberCustomer, $numberAddress)
    {
        /** @var CustomerDataGenerator $customerDataGenerator */
        $customerDataGenerator = $this->customerDataGeneratorFactory->create(
            [
                'addresses-count' => $numberAddress
            ]
        );

        $fixtureMap = [
            'customer_data' => function ($customerId) use ($customerDataGenerator) {
                return $customerDataGenerator->generate($customerId);
            },
        ];

        $this->entityGeneratorFactory
            ->create(
                [
                    'entityType' => CustomerInterface::class,
                    'customTableMap' => [
                        'customer_entity' => [
                            'handler' => $this->getCustomerEntityHandler()
                        ],

                        'customer_address_entity' => [
                            'handler' => $this->getCustomerAddressEntityHandler()
                        ]
                    ],
                ]
            )->generate(
                $this->customerTemplateGenerator,
                $numberCustomer,
                function ($customerId) use ($fixtureMap) {
                    // phpcs:ignore Magento2.Functions.DiscouragedFunction
                    $fixtureMap['customer_data'] = call_user_func($fixtureMap['customer_data'], $customerId);
                    return $fixtureMap;
                }
            );

        $this->addDefaultAddresses();
    }

    /**
     * Creates closure that is used
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @return \Closure
     */
    private function getCustomerEntityHandler()
    {
        return function ($entityId, $entityNumber, $fixtureMap, $binds) {
            return array_map(
                'array_merge',
                $binds,
                array_fill(0, count($binds), $fixtureMap['customer_data']['customer'])
            );
        };
    }

    /**
     * Creates closure that is used
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @return \Closure
     */
    private function getCustomerAddressEntityHandler()
    {
        return function ($entityId, $entityNumber, $fixtureMap, $binds) {
            return array_map(
                'array_merge',
                array_fill(0, count($fixtureMap['customer_data']['addresses']), reset($binds)),
                $fixtureMap['customer_data']['addresses']
            );
        };
    }

    /**
     * Set default billing and shipping addresses for customer
     *
     * @return void
     */
    private function addDefaultAddresses()
    {
        $this->getConnection()->query(
            sprintf(
                '
                    update `%s` customer
                    join (
                        select 
                            parent_id, min(entity_id) as min, max(entity_id) as max
                        from `%s`
                        group by parent_id
                    ) customer_address on customer_address.parent_id = customer.entity_id
                    set
                      customer.default_billing = customer_address.min,
                      customer.default_shipping = customer_address.max
                ',
                $this->resourceConnection->getTableName('customer_entity'),
                $this->resourceConnection->getTableName('customer_address_entity')
            )
        );
    }

    /**
     * Get connection
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private function getConnection()
    {
        if (null === $this->connection) {
            $this->connection = $this->resourceConnection->getConnection();
        }

        return $this->connection;
    }
}
