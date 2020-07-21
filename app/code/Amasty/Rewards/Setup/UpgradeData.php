<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Setup;

class UpgradeData implements \Magento\Framework\Setup\UpgradeDataInterface
{
    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetaData;

    /**
     * @var \Amasty\Base\Setup\SerializedFieldDataConverter
     */
    private $fieldDataConverter;

    /**
     * @var Operation\UpgradeDataTo170
     */
    protected $upgradeDataTo170;

    /**
     * @var Operation\UpgradeDataTo180
     */
    private $upgradeDataTo180;

    /**
     * @var Operation\UpgradeDataTo181
     */
    private $upgradeDataTo181;

    /**
     * @var Operation\UpgradeDataTo1140
     */
    private $upgradeDataTo1140;

    public function __construct(
        \Magento\Framework\App\ProductMetadataInterface $productMetaData,
        \Amasty\Base\Setup\SerializedFieldDataConverter $fieldDataConverter,
        Operation\UpgradeDataTo170 $upgradeDataTo170,
        Operation\UpgradeDataTo180 $upgradeDataTo180,
        Operation\UpgradeDataTo181 $upgradeDataTo181,
        Operation\UpgradeDataTo1140 $upgradeDataTo1140
    ) {
        $this->productMetaData = $productMetaData;
        $this->fieldDataConverter = $fieldDataConverter;
        $this->upgradeDataTo170 = $upgradeDataTo170;
        $this->upgradeDataTo180 = $upgradeDataTo180;
        $this->upgradeDataTo181 = $upgradeDataTo181;
        $this->upgradeDataTo1140 = $upgradeDataTo1140;
    }

    /**
     * @inheritdoc
     */
    public function upgrade(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.3', '<')
            && $this->productMetaData->getVersion() >= "2.2.0"
        ) {
            $table = $setup->getTable('amasty_rewards_rule');
            $this->fieldDataConverter->convertSerializedDataToJson($table, 'rule_id', 'conditions_serialized');
        }

        if (version_compare($context->getVersion(), '1.7.0', '<')) {
            $this->upgradeDataTo170->execute($setup);
        }

        if (version_compare($context->getVersion(), '1.8.0', '<')) {
            $this->upgradeDataTo180->execute($setup);
        }

        if (version_compare($context->getVersion(), '1.8.1', '<')) {
            $this->upgradeDataTo181->execute($setup);
        }

        if (version_compare($context->getVersion(), '1.14.0', '<')) {
            $this->upgradeDataTo1140->execute($setup);
        }

        $setup->endSetup();
    }
}
