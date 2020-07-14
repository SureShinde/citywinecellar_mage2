<?php
/**
 * Copyright © 2019 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace Magestore\PosSampleData\Model\FixtureGenerator\Customer;

/**
 * Generate customer data for customer fixture
 */
class CustomerDataGenerator
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var \Magento\Setup\Model\Address\AddressDataGenerator
     */
    private $addressDataGenerator;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\CollectionFactory
     */
    private $groupCollectionFactory;

    /**
     * @var array
     */
    private $customerGroupIds;

    /**
     * @param \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory
     * @param \Magento\Setup\Model\Address\AddressDataGenerator $addressDataGenerator
     * @param array $config
     */
    public function __construct(
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory,
        \Magento\Setup\Model\Address\AddressDataGenerator $addressDataGenerator,
        array $config
    ) {
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->addressDataGenerator = $addressDataGenerator;
        $this->config = $config;
    }

    /**
     * Generate customer data by index
     *
     * @param int $customerId
     * @return array
     */
    public function generate($customerId)
    {
        if ($customerId % 160 == 0 && $customerId < 700) {
            $incrementId = str_pad($customerId, 8, "0", STR_PAD_LEFT);
            $email = sprintf('%s_four_result@trueplus.vn', $incrementId);
            $firstName = sprintf('First_%s_four_result', $incrementId);
            $lastName = sprintf('four_result_Last_%s', $incrementId);
        } elseif ($customerId % 63 == 0 && $customerId < 700) {
            $incrementId = str_pad($customerId, 8, "0", STR_PAD_LEFT);
            $email = sprintf('%s_eleven_result@trueplus.vn', $incrementId);
            $firstName = sprintf('First_%s_eleven_result', $incrementId);
            $lastName = sprintf('eleven_result_Last_%s', $incrementId);
        } else {
            $incrementId = str_pad($customerId, 8, "0", STR_PAD_LEFT);
            $email = sprintf('%s@trueplus.vn', $incrementId);
            $firstName = sprintf('First_%s', $incrementId);
            $lastName = sprintf('Last_%s', $incrementId);
        }
        return [
            'customer' => [
                'email' => $email,
                'firstname' => $firstName,
                'lastname' => $lastName,
                'group_id' => $this->getGroupIdForCustomer($customerId)
            ],

            'addresses' => $this->generateAddresses($customerId),
        ];
    }

    /**
     * Get customer group id for customer
     *
     * @param int $customerId
     * @return int
     */
    private function getGroupIdForCustomer($customerId)
    {
        if (!$this->customerGroupIds) {
            $this->customerGroupIds = $this->groupCollectionFactory->create()->getAllIds();
        }

        return $this->customerGroupIds[$customerId % count($this->customerGroupIds)];
    }

    /**
     * Generate customer addresses with distribution
     * 50% as shipping address
     * 50% as billing address
     *
     * @param int $customerId
     * @return array
     */
    private function generateAddresses($customerId)
    {
        $addresses = [];
        $addressesCount = $this->config['addresses-count'];

        if ($customerId % 160 == 0 && $customerId < 700) {
            $phone = 'Phone_four_result_' . str_pad($customerId, 8, "0", STR_PAD_LEFT);
        } elseif ($customerId % 63 == 0 && $customerId < 700) {
            $phone = 'Phone_eleven_result_' . str_pad($customerId, 8, "0", STR_PAD_LEFT);
        } else {
            $phone = 'Phone_' . str_pad($customerId, 8, "0", STR_PAD_LEFT);
        }

        while ($addressesCount) {
            $addresses[] = [
                'telephone' => $phone,
                'postcode' => 100000,
                'country_id' => 'VN',
                'region' => 'Hanoi',
                'city' => 'Hanoi'
            ];
            $addressesCount--;
        }

        return $addresses;
    }
}
