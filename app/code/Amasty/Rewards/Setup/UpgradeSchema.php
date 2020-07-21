<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var Operation\UpgradeTo110
     */
    private $upgradeTo110;

    /**
     * @var Operation\UpgradeTo160
     */
    private $upgradeTo160;

    /**
     * @var Operation\UpgradeTo161
     */
    private $upgradeTo161;

    /**
     * @var Operation\UpgradeTo170
     */
    private $upgradeTo170;

    /**
     * @var Operation\UpgradeTo181
     */
    private $upgradeTo181;

    /**
     * @var Operation\UpgradeTo193
     */
    private $upgradeTo193;

    public function __construct(
        Operation\UpgradeTo110 $upgradeTo110,
        Operation\UpgradeTo160 $upgradeTo160,
        Operation\UpgradeTo161 $upgradeTo161,
        Operation\UpgradeTo170 $upgradeTo170,
        Operation\UpgradeTo181 $upgradeTo181,
        Operation\UpgradeTo193 $upgradeTo193
    ) {
        $this->upgradeTo110 = $upgradeTo110;
        $this->upgradeTo160 = $upgradeTo160;
        $this->upgradeTo161 = $upgradeTo161;
        $this->upgradeTo170 = $upgradeTo170;
        $this->upgradeTo181 = $upgradeTo181;
        $this->upgradeTo193 = $upgradeTo193;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1', '<')) {
            $this->upgradeTo110->execute($setup);
        }

        if (version_compare($context->getVersion(), '1.6.0', '<')) {
            $this->upgradeTo160->execute($setup);
        }

        if (version_compare($context->getVersion(), '1.6.1', '<')) {
            $this->upgradeTo161->execute($setup);
        }

        if (version_compare($context->getVersion(), '1.7.0', '<')) {
            $this->upgradeTo170->execute($setup);
        }

        if (version_compare($context->getVersion(), '1.8.1', '<')) {
            $this->upgradeTo181->execute($setup);
        }

        if (version_compare($context->getVersion(), '1.9.3', '<')) {
            $this->upgradeTo193->execute($setup);
        }

        if (version_compare($context->getVersion(), '1.10.1', '<')) {
            $table = $setup->getTable(\Amasty\Rewards\Model\ResourceModel\Rewards::TABLE_NAME);
            $setup->getConnection()->addIndex(
                $table,
                $setup->getIdxName(
                    $table,
                    \Amasty\Rewards\Api\Data\RewardsInterface::CUSTOMER_ID
                ),
                \Amasty\Rewards\Api\Data\RewardsInterface::CUSTOMER_ID
            );
        }

        $setup->endSetup();
    }
}
