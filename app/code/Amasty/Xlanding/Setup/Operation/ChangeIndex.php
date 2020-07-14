<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Setup\Operation;

use Magento\Framework\Setup\SchemaSetupInterface;
use Amasty\Xlanding\Model\Indexer\IndexBuilder;

class ChangeIndex
{
    /**
     * @var string
     */
    private $productIdLink;

    public function __construct(\Magento\Framework\App\ProductMetadataInterface $productMetadata)
    {
        $this->productIdLink = $productMetadata->getEdition() != 'Community' ? 'row_id' : 'entity_id';
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        if ($this->productIdLink == 'row_id') {
            $table = $setup->getTable(IndexBuilder::TABLE_NAME);
            $connection = $setup->getConnection();
            $connection->dropForeignKey(
                $table,
                $setup->getFkName(
                    IndexBuilder::TABLE_NAME,
                    'product_id',
                    'catalog_product_entity',
                    'entity_id'
                )
            );

            $connection->addForeignKey(
                $setup->getFkName(
                    IndexBuilder::TABLE_NAME,
                    'product_id',
                    'catalog_product_entity',
                    $this->productIdLink
                ),
                $table,
                'product_id',
                $setup->getTable('catalog_product_entity'),
                $this->productIdLink,
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        }
    }
}
