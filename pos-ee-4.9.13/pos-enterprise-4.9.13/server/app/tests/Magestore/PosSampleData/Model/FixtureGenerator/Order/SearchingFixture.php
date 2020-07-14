<?php

namespace Magestore\PosSampleData\Model\FixtureGenerator\Order;

/**
 * Class SearchingFixture
 */
class SearchingFixture
{
    protected $numberOfSearchingResult = [
        [
            'number' => 1,
            'searching_prefix' => '1order_',
            'sku' => '1product_pos_simple_product',
            'payment_reference_number' => '1order_payment_reference_number'
        ],
        [
            'number' => 4,
            'searching_prefix' => '4orders_',
            'sku' => '4products_pos_simple_product',
            'payment_reference_number' => '4orders_payment_reference_number'
        ],
        [
            'number' => 11,
            'searching_prefix' => '11orders_',
            'sku' => '21products_pos_simple_product',
            'payment_reference_number' => '11orders_payment_reference_number'
        ]
    ];

    /**
     * Get search fixtures
     *
     * @return array
     */
    public function getSearchFixtures()
    {
        $searchFixtures = [];

        foreach ($this->numberOfSearchingResult as $value) {
            $searchFixtures[] = [
                "number_requested_order" => $value['number'],
                "product_sku" => $value['sku'],
                "payment_reference_number" => $value['payment_reference_number'],
                "searching_prefix" => $value['searching_prefix'],
                "customer_email" => 'roni_cost@example.com'
            ];
        }

        return $searchFixtures;
    }
}
