<?php

namespace Magestore\PosSampleData\Model\FixtureGenerator\Order;

/**
 * Class OrderDataGenerator
 *
 * @package Magestore\PosSampleData\Model\FixtureGenerator\Order
 */
class OrderDataGenerator
{
    /**
     * @var OrdersFixtureFactory
     */
    protected $ordersFixtureFactory;
    /**
     * @var SearchingFixture
     */
    protected $searchingFixture;

    /**
     * OrderDataGenerator constructor.
     *
     * @param OrdersFixtureFactory $ordersFixtureFactory
     * @param SearchingFixture $searchingFixture
     */
    public function __construct(
        OrdersFixtureFactory $ordersFixtureFactory,
        SearchingFixture $searchingFixture
    ) {
        $this->ordersFixtureFactory = $ordersFixtureFactory;
        $this->searchingFixture = $searchingFixture;
    }

    /**
     * Generate orders
     */
    public function execute()
    {
        $this->generateSampleOrder();
        $this->generateSearchingOrder();
    }

    /**
     * Generate sample order
     */
    public function generateSampleOrder()
    {
        $additionalData = [
            "number_requested_order" => 100,
            "customer_email" => 'roni_cost@example.com'
        ];
        /** @var OrdersFixture $orderFixture */
        $orderFixture = $this->ordersFixtureFactory->create(
            [
                'additionalData' => $additionalData
            ]
        );
        $orderFixture->execute();
    }

    /**
     * Generate searching order
     */
    public function generateSearchingOrder()
    {
        $searchingFixtures = $this->searchingFixture->getSearchFixtures();

        foreach ($searchingFixtures as $additionalData) {
            $orderFixture = $this->ordersFixtureFactory->create(
                [
                    'additionalData' => $additionalData
                ]
            );
            $orderFixture->execute();
        }
    }
}
