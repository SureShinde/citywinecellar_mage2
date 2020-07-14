<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{

    /**
     *
     * @var \Magestore\BarcodeSuccess\Api\InstallManagementInterface
     */
    protected $_installManagement;

    /**
     *
     * @param \Magestore\BarcodeSuccess\Api\InstallManagementInterface $installManagement
     */
    public function __construct(
        \Magestore\BarcodeSuccess\Api\InstallManagementInterface $installManagement
    )
    {
        $this->_installManagement = $installManagement;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        $setup->startSetup();

        /* create default barcode template */
        $this->_installManagement->createBarcodeTemplate();

        $setup->endSetup();
    }
}
