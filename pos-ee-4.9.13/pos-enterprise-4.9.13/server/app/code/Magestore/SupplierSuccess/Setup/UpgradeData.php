<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.1.0.2', '<')) {
            /*
             * Clear wrong data from previous version
             *
             * https://github.com/Magestore/SupplierSuccess/issues/12
             */
            $setup->getConnection()->update(
                $setup->getTable('os_supplier'),
                ['region_id' => NULL],
                'country_id IS NULL'
            );
        }
        $setup->endSetup();
    }
}
