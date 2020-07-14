<?php
/**
 * Copyright © 2019 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\PosSampleData\Model\FixtureGenerator\Customer;

/**
 * Generate address data for customer
 */
class AddressDataGenerator
{
    /**
     * Generate address data
     *
     * @return array
     */
    public function generateAddress()
    {
        return [
            'postcode' => 100000
        ];
    }
}
