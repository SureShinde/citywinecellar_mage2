<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SupplierCommand
 *
 * @package Magestore\SupplierSuccess\Console\Command
 */
class SupplierCommand extends Command
{
    /**
     * @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product
     */
    protected $productResource;

    /**
     * SupplierCommand constructor.
     * @param \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product $productResource
     * @param string|null $name
     */
    public function __construct(
        \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product $productResource,
        string $name = null
    ) {
        $this->productResource = $productResource;
        parent::__construct($name);
    }

    /**
     * Command configuration
     */
    protected function configure()
    {
        $this->setName('supplier:product');
        $this->setDescription('Standardize supplier product information');
    }

    /**
     * Command execute
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->productResource->correctProductInfo();
            $output->writeln(sprintf('Supplier products have been standardized successfully!'));
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>Error: %s</error>', $e->getMessage()));
        }
    }
}
