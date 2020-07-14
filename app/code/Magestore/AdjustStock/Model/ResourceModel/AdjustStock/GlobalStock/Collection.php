<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Model\ResourceModel\AdjustStock\GlobalStock;

/**
 * Class Collection
 * @package Magestore\AdjustStock\Model\ResourceModel\TransferStock\GlobalStock
 */
class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    const MAPPING_FIELD = [
        'source_code' => 'GROUP_CONCAT(DISTINCT inventory_source_item.source_code)',
        'total_qty' => 'IFNULL(current_inventory_source_item.quantity, 0)',
        'barcode' => 'GROUP_CONCAT(DISTINCT barcode.barcode)'
    ];

    /**
     * @return $this|\Magento\Catalog\Model\ResourceModel\Product\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _initSelect()
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Framework\App\RequestInterface $request */
        $request = $om->get('Magento\Framework\App\RequestInterface');
        /** @var \Magento\Framework\Module\Manager $moduleManager */
        $moduleManager = $om->get('Magento\Framework\Module\Manager');

        $this->getSelect()->from(['e' => $this->getEntity()->getEntityTable()]);
        $entity = $this->getEntity();
        if ($entity->getTypeId() && $entity->getEntityTable() == \Magento\Eav\Model\Entity::DEFAULT_ENTITY_TABLE) {
            $this->addAttributeToFilter('entity_type_id', $this->getEntity()->getTypeId());
        }
        $this->addAttributeToSelect([
            "name",
            "sku",
            "price",
            "status",
            "image"
        ]);

        $resource = $this->getResource();
        $sourceItemTable = $resource->getTable('inventory_source_item');
        $this->getSelect()->joinLeft(
            ['inventory_source_item' => $sourceItemTable],
            "e.sku = inventory_source_item.sku",
            ['source_code']
        );

        // Add current quantity
        $currentAdjustmentSource = $request->getParam('source_code');
        if (!$currentAdjustmentSource) {
            $currentAdjustId = $request->getParam('adjuststock_id');
            if ($currentAdjustId) {
                $adjustModel = $om->create('Magestore\AdjustStock\Model\AdjustStock')->load($currentAdjustId);
                if ($adjustModel->getId()) {
                    $currentAdjustmentSource = $adjustModel->getData('source_code');
                }
            }
        }

        if($currentAdjustmentSource) {
            $this->getSelect()->joinLeft(
                ['current_inventory_source_item' => $sourceItemTable],
                "e.sku = current_inventory_source_item.sku AND
                current_inventory_source_item.source_code = '$currentAdjustmentSource'",
                ['quantity']
            );
        }

        // add barcode
        $barcodeTable = $resource->getTable('os_barcode');
        if($moduleManager->isEnabled('Magestore_BarcodeSuccess')) {
            $this->getSelect()->joinLeft(
                ['barcode' => $barcodeTable],
                "e.sku = barcode.product_sku",
                ['barcode']
            );
        }

        $this->getSelect()->columns([
            'source_code' => new \Zend_Db_Expr(self::MAPPING_FIELD['source_code']),
            'change_qty' => new \Zend_Db_Expr('0'),
            'new_qty' => new \Zend_Db_Expr('0')
        ]);

        if($currentAdjustmentSource) {
            $this->getSelect()->columns([
                'source_code' => new \Zend_Db_Expr(self::MAPPING_FIELD['source_code']),
                'change_qty' => new \Zend_Db_Expr('0'),
                'new_qty' => new \Zend_Db_Expr('current_inventory_source_item.quantity')
            ]);
        } else {
            $this->getSelect()->columns([
                'source_code' => new \Zend_Db_Expr(self::MAPPING_FIELD['source_code']),
                'change_qty' => new \Zend_Db_Expr('0'),
                'new_qty' => new \Zend_Db_Expr('0')
            ]);
        }

        if($currentAdjustmentSource) {
            $this->getSelect()->columns([
                'total_qty' => new \Zend_Db_Expr(self::MAPPING_FIELD['total_qty'])
            ]);
        } else {
            $this->getSelect()->columns([
                'total_qty' => new \Zend_Db_Expr('0')
            ]);
        }

        if($moduleManager->isEnabled('Magestore_BarcodeSuccess')) {
            $this->getSelect()->columns([
                'barcode' => new \Zend_Db_Expr(self::MAPPING_FIELD['barcode']),
                'barcode_original_data' => new \Zend_Db_Expr(self::MAPPING_FIELD['barcode'])
            ]);
        }

        $this->getSelect()->group('e.sku');

        return $this;
    }

    /**
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
     * @return int
     * @throws \Zend_Db_Statement_Exception
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
        return intval($this->_totalRecords);
    }

    /**
     * @param mixed $field
     * @param null $condition
     * @return $this|\Magento\Framework\Data\Collection\AbstractDb
     */
    public function addFieldToFilter($field, $condition = null)
    {
        foreach (self::MAPPING_FIELD as $key => $value) {
            if($field == $key){
                $field = $value;
                return $this->addFieldToFilterCallBack($field, $condition);
            }
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * @param mixed $field
     * @param null $condition
     */
    public function addFieldToFilterCallBack($field ,$condition ){
        foreach ($condition as $con => $value){
            $conditionSql = $this->_getConditionSql($field, $condition);
            $this->getSelect()->having($conditionSql);
        }
    }

    /**
     * @param $sourceCode
     * @return $this
     */
    public function addSourceCodeToFilter($sourceCode) {
        $this->getSelect()->where($this->getConnection()->prepareSqlCondition('inventory_source_item.source_code' , ['eq' => $sourceCode]));
        return $this;
    }

    /**
     * @param $barcode
     * @return $this
     */
    public function addBarcodeToFilter($barcode) {
        $this->getSelect()->where($this->getConnection()->prepareSqlCondition('barcode.barcode' , ['like' => $barcode]));
        return $this;
    }

    /**
     * @param string $field
     * @param string $direction
     * @return $this
     */
    public function addOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        foreach (self::MAPPING_FIELD as $key => $value) {
            if($field == $key){
                $field = $value;
                $this->getSelect()->order( new \Zend_Db_Expr($field .' '. $direction));
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
            if($field == $key){
                $field = $value;
            }
        }
        return parent::setOrder($field, $direction);
    }
}
