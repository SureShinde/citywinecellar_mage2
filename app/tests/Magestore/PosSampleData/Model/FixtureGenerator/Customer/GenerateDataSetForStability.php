<?php
/**
 * Copyright © 2019 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace Magestore\PosSampleData\Model\FixtureGenerator\Customer;

/**
 * Class GenerateDataSetForStability
 *
 * @package Magestore\PosSampleData\Model\FixtureGenerator\Customer
 */
class GenerateDataSetForStability
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var CustomerGenerator
     */
    protected $customerGenerator;

    /**
     * GenerateDataSetForStability constructor.
     *
     * @param \Magento\Framework\Registry $registry
     * @param CustomerGenerator $customerGenerator
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magestore\PosSampleData\Model\FixtureGenerator\Customer\CustomerGenerator $customerGenerator
    ) {
        $this->registry = $registry;
        $this->customerGenerator = $customerGenerator;
    }

    /**
     * Execute
     */
    public function execute()
    {
        $this->registry->unregister('isSecureArea');
        $this->registry->register('isSecureArea', true);
        // create sample customer
        $this->customerGenerator->generate(700, 1);
        $this->customerGenerator->generate(200, 0);
        $this->customerGenerator->generate(100, 2);
    }
}
