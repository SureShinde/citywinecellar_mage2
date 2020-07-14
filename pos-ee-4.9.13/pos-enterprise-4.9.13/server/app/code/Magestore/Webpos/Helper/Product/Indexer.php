<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Helper\Product;

class Indexer extends \Magento\Catalog\Helper\Product\Flat\Indexer
{
    /**
     * Get barcode attribute code
     *
     * @return string
     */
    public function barcodeAttr()
    {
        return $this->scopeConfig->getValue('webpos/product_search/barcode') ?: 'sku';
    }

    /**
     * {@inheritDoc}
     */
    public function getFlatTableName($storeId)
    {
        return sprintf('%s_%s', $this->getTable('webpos_search_product'), $storeId);
    }

    /**
     * {@inheritDoc}
     */
    public function getAttributeCodes()
    {
        if ($this->_attributeCodes === null) {
            $this->_attributeCodes = \Magestore\Webpos\Model\Catalog\ProductSearchRepository::SEARCH_ATTRIBUTES;
            $this->_attributeCodes[] = 'webpos_visible';
            $barcodeAttr = $this->barcodeAttr();
            if (!in_array($barcodeAttr, $this->_attributeCodes)) {
                $this->_attributeCodes[] = $barcodeAttr;
            }
        }
        return $this->_attributeCodes;
    }

    /**
     * {@inheritDoc}
     */
    public function getFlatColumnsDdlDefinition()
    {
        $columns = [];
        $columns['entity_id'] = [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length' => null,
            'unsigned' => true,
            'nullable' => false,
            'default' => false,
            'primary' => true,
            'comment' => 'Entity Id',
        ];
        $columns['attribute_set_id'] = [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            'length' => 5,
            'unsigned' => true,
            'nullable' => false,
            'default' => '0',
            'comment' => 'Attribute Set ID',
        ];
        $columns['type_id'] = [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length' => 32,
            'unsigned' => false,
            'nullable' => false,
            'default' => \Magento\Catalog\Model\Product\Type::DEFAULT_TYPE,
            'comment' => 'Type Id',
        ];
        return $columns;
    }

    /**
     * {@inheritDoc}
     */
    public function getFlatIndexes()
    {
        if ($this->_indexes === null) {
            $this->_indexes = [];
            $this->_indexes['PRIMARY'] = [
                'type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_PRIMARY,
                'fields' => ['entity_id'],
            ];
            $this->_indexes['IDX_BARCODE'] = [
                'type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX,
                'fields' => [$this->barcodeAttr()],
            ];
            $this->_indexes['IDX_NAME'] = [
                'type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX,
                'fields' => ['name'],
            ];
            $this->_indexes['IDX_TYPE_ID'] = [
                'type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX,
                'fields' => ['type_id'],
            ];
        }
        return $this->_indexes;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteAbandonedStoreFlatTables()
    {
        $connection = $this->_resource->getConnection();
        $existentTables = $connection->getTables($connection->getTableName('webpos_search_product_%'));
        $this->_changelog->setViewId('webpos_search_product');
        foreach ($existentTables as $key => $tableName) {
            if ($this->_changelog->getName() == $tableName) {
                unset($existentTables[$key]);
            }
        }
        $actualStoreTables = [];
        foreach ($this->_storeManager->getStores() as $store) {
            $actualStoreTables[] = $this->getFlatTableName($store->getId());
        }

        $tablesToDelete = array_diff($existentTables, $actualStoreTables);

        foreach ($tablesToDelete as $table) {
            if ($connection->isTableExists($table)) {
                $connection->dropTable($table);
            }
        }
    }
}
