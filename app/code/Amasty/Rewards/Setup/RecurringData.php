<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Setup;

use Amasty\Base\Setup\SerializedFieldDataConverter;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\App\ProductMetadataInterface;

/**
 * Recurring Data script
 */
class RecurringData implements InstallDataInterface
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var SerializedFieldDataConverter
     */
    private $fieldDataConverter;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * RecurringData constructor.
     * @param MetadataPool $metadataPool
     * @param ProductMetadataInterface $productMetadata
     * @param SerializedFieldDataConverter $fieldDataConverter
     */
    public function __construct(
        MetadataPool $metadataPool,
        ProductMetadataInterface $productMetadata,
        SerializedFieldDataConverter $fieldDataConverter
    ) {
        $this->productMetadata = $productMetadata;
        $this->metadataPool = $metadataPool;
        $this->fieldDataConverter = $fieldDataConverter;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($this->productMetadata->getVersion(), '2.2', '>=')) {
            $this->convertSerializedDataToJson($setup);
        }
    }
    /**
     * Convert metadata from serialized to JSON format:
     *
     * @param ModuleDataSetupInterface $setup
     *
     * @return void
     */
    public function convertSerializedDataToJson($setup)
    {
        $this->fieldDataConverter->convertSerializedDataToJson(
            $setup->getTable('amasty_rewards_rule'),
            'rule_id',
            'conditions_serialized'
        );
    }
}
