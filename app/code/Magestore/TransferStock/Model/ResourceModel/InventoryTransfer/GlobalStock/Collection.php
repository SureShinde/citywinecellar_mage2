<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\GlobalStock;

/**
 * Global stock collection
 */
class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    const MAPPING_FIELD = [
        'total_qty' => 'IFNULL(current_inventory_source_item.quantity, 0)',
        'barcode' => 'GROUP_CONCAT(DISTINCT barcode.barcode)'
    ];

    /**
     * Init Select
     *
     * @return $this|\Magento\Catalog\Model\ResourceModel\Product\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _initSelect()
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Framework\App\RequestInterface $request */
        $request = $om->get(\Magento\Framework\App\RequestInterface::class);
        /** @var \Magento\Framework\Module\Manager $moduleManager */
        $moduleManager = $om->get(\Magento\Framework\Module\Manager::class);

        $this->getSelect()->from(['e' => $this->getEntity()->getEntityTable()]);
        $entity = $this->getEntity();
        if ($entity->getTypeId() && $entity->getEntityTable() == \Magento\Eav\Model\Entity::DEFAULT_ENTITY_TABLE) {
            $this->addAttributeToFilter('entity_type_id', $this->getEntity()->getTypeId());
        }
        $this->addAttributeToFilter(
            'type_id',
            ['in' => ['simple', 'virtual', 'downloadable', 'giftvoucher']]
        );
        $this->addAttributeToSelect([
            "name",
            "sku",
            "price",
            "status",
            "thumbnail"
        ]);

        $resource = $this->getResource();
        $sourceItemTable = $resource->getTable('inventory_source_item');
        $this->getSelect()->joinLeft(
            ['inventory_source_item' => $sourceItemTable],
            "e.sku = inventory_source_item.sku",
            ['source_code']
        );

        // Add current quantity
        $currentFromSource = $request->getParam('source_warehouse_code');
        if (!$currentFromSource) {
            $currentTransferId = $request->getParam('inventorytransfer_id');
            if ($currentTransferId) {
                $transferModel = $om->create(\Magestore\TransferStock\Model\InventoryTransfer::class)
                    ->load($currentTransferId);
                if ($transferModel->getId()) {
                    $currentFromSource = $transferModel->getData('source_warehouse_code');
                }
            }
        }

        if ($currentFromSource) {
            $this->getSelect()->columns([
                'qty_to_send' => new \Zend_Db_Expr('0'),
                'total_qty' => new \Zend_Db_Expr(self::MAPPING_FIELD['total_qty'])
            ]);
            $this->getSelect()->joinInner(
                ['current_inventory_source_item' => $sourceItemTable],
                "e.sku = current_inventory_source_item.sku AND
                current_inventory_source_item.source_code = '$currentFromSource'",
                ['quantity']
            );
        }

        // add barcode
        $barcodeTable = $resource->getTable('os_barcode');
        if ($moduleManager->isEnabled('Magestore_BarcodeSuccess')) {
            $this->getSelect()->columns([
                'barcode' => new \Zend_Db_Expr(self::MAPPING_FIELD['barcode']),
                'barcode_original_data' => new \Zend_Db_Expr(self::MAPPING_FIELD['barcode'])
            ]);
            $this->getSelect()->joinLeft(
                ['barcode' => $barcodeTable],
                "e.sku = barcode.product_sku",
                []
            );
        }

        $this->getSelect()->group('e.sku');

        return $this;
    }

    /**
     * Get Select Count Sql
     *
     * @return \Magento\Framework\DB\Select
     * @throws \Zend_Db_Select_Exception
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        $countSelect->reset(\Magento\Framework\DB\Select::ORDER);
        $countSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $countSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $countSelect->reset(\Magento\Framework\DB\Select::COLUMNS);

        if (!count($this->getSelect()->getPart(\Magento\Framework\DB\Select::GROUP))) {
            $countSelect->columns(new \Zend_Db_Expr('COUNT(*)'));
            return $countSelect;
        }
        $countSelect->reset(\Magento\Framework\DB\Select::HAVING);
        $countSelect->reset(\Magento\Framework\DB\Select::GROUP);
        $group = $this->getSelect()->getPart(\Magento\Framework\DB\Select::GROUP);
        $countSelect->columns(new \Zend_Db_Expr(("COUNT(DISTINCT ".implode(", ", $group).")")));
        return $countSelect;
    }

    /**
     * @inheritDoc
     */
    public function getSize()
    {
        if ($this->_totalRecords === null) {
            $sql = $this->getSelect()
                ->reset(\Magento\Framework\DB\Select::LIMIT_COUNT)
                ->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET)
                ->__toString();
            $records = $this->getConnection()->query($sql);
            $result = $records->fetchAll();
            $this->_totalRecords = count($result);
        }
        return (int)$this->_totalRecords;
    }

    /**
     * @inheritDoc
     */
    public function addFieldToFilter($field, $condition = null)
    {
        foreach (self::MAPPING_FIELD as $key => $value) {
            if ($field == $key) {
                $field = $value;
                return $this->addFieldToFilterCallBack($field, $condition);
            }
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add Field To Filter Call Back
     *
     * @param string $field
     * @param integer|string|array $condition
     */
    public function addFieldToFilterCallBack($field, $condition)
    {
        $count = count($condition);
        for ($i = 0; $i < $count; $i++) {
            $conditionSql = $this->_getConditionSql($field, $condition);
            $this->getSelect()->having($conditionSql);
        }
    }

    /**
     * Add Source Code To Filter
     *
     * @param string $sourceCode
     * @return $this
     */
    public function addSourceCodeToFilter($sourceCode)
    {
        $this->getSelect()->where(
            $this->getConnection()->prepareSqlCondition(
                'inventory_source_item.source_code',
                ['eq' => $sourceCode]
            )
        );
        return $this;
    }

    /**
     * Add Barcode To Filter
     *
     * @param string $barcode
     * @return $this
     */
    public function addBarcodeToFilter($barcode)
    {
        $this->getSelect()->where(
            $this->getConnection()->prepareSqlCondition('barcode.barcode', ['like' => $barcode])
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        foreach (self::MAPPING_FIELD as $key => $value) {
            if ($field == $key) {
                $field = $value;
                $this->getSelect()->order(new \Zend_Db_Expr($field .' '. $direction));
                return $this;
            }
        }
        return parent::addOrder($field, $direction);
    }

    /**
     * Add select order
     *
     * @param   string $field
     * @param   string $direction
     * @return  $this
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        foreach (self::MAPPING_FIELD as $key => $value) {
            if ($field == $key) {
                $field = $value;
            }
        }
        return parent::setOrder($field, $direction);
    }
}
