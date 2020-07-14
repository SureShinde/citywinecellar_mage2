<?php
/**
 * Copyright © 2019 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\PosSampleData\Model\FixtureGenerator\Customer;

/**
 * Create new instance of CustomerDataGenerator
 */
class CustomerDataGeneratorFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create CustomerGenerator instance with specified configuration
     *
     * @param array $config
     * @return CustomerDataGenerator
     */
    public function create(array $config)
    {
        return $this->objectManager->create(
            CustomerDataGenerator::class,
            [
                'addressGenerator' => $this->objectManager->create(
                    \Magestore\PosSampleData\Model\FixtureGenerator\Customer\AddressDataGenerator::class
                ),
                'config' => $config
            ]
        );
    }
}
