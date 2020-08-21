<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\ResourceModel\BackOrderProduct;

use Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;

/**
 * Backorder Product Collection
 */
class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    /**
     * @var array
     */
    protected $mappingField = [
        'product_id' => 'entity_id',
        'product_sku' => 'sku',
        'product_name' => 'name',
        'qty' => 'qty',
        'product_supplier_sku' => 'supplier_product.product_supplier_sku',
        'cost' => 'supplier_product.cost',
    ];

    /**
     * Init select
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _initSelect()
    {
        if ($this->isEnabledFlat()) {
            $this->getSelect()->from(
                [self::MAIN_TABLE_ALIAS => $this->getEntity()->getFlatTableName()],
                null
            )->columns(
                ['status' => new \Zend_Db_Expr(ProductStatus::STATUS_ENABLED)]
            );
            $this->addAttributeToSelect($this->getResource()->getDefaultAttributes());
            if ($this->_catalogProductFlatState->getFlatIndexerHelper()->isAddChildData()) {
                $this->getSelect()->where('e.is_child=?', 0);
                $this->addAttributeToSelect(['child_id', 'is_child']);
            }
        } else {
            $this->getSelect()->from([self::MAIN_TABLE_ALIAS => $this->getEntity()->getEntityTable()]);
        }

        $this->addAttributeToFilter('type_id', \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
        $this->addExpressionAttributeToSelect('product_name', '', 'name');

        $this->getSelect()
            ->columns(['product_id' => 'entity_id'])
            ->columns(['product_sku' => 'sku']);

        $reservation = clone $this->getSelect();

        $reservation->columns(
            [
                'reservation_qty' => new \Zend_Db_Expr('SUM(IFNULL(inventory_reservation.quantity,0))')
            ]
        )
            ->joinInner(
                ['inventory_reservation' => $this->getTable('inventory_reservation')],
                'e.sku = inventory_reservation.sku',
                null
            )->group('e.entity_id')->distinct(true);

        $this->getSelect()->columns(
            [
                'qty' => new \Zend_Db_Expr(
                    'SUM(IFNULL(inventory_source_item.quantity, 0)) 
                    + IFNULL(catalog_reservation.reservation_qty,0)'
                )
            ]
        )
            ->joinInner(
                ['inventory_source_item' => $this->getTable('inventory_source_item')],
                'e.sku = inventory_source_item.sku',
                null
            )
            ->joinLeft(
                ['catalog_reservation' => $reservation],
                'e.entity_id = catalog_reservation.entity_id',
                null
            )
            ->group('e.entity_id')->distinct(true)
            ->having('qty < 0');

        if ($this->checkProductSource()) {
            $this->getSelect()->joinInner(
                ['supplier_product' => $this->getTable('os_supplier_product')],
                'e.entity_id = supplier_product.product_id',
                ['product_id', 'product_sku', 'product_supplier_sku', 'product_name', 'cost']
            );
        }

        return $this;
    }

    /**
     * Add Filter Supplier And Purchase
     *
     * @param int $supplierId
     * @param int $purchaseId
     * @return $this
     */
    public function addFilterSupplierAndPurchase($supplierId, $purchaseId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $purchaseItemService = $objectManager
            ->get(\Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item\ItemService::class);

        if ($this->checkProductSource()) {
            $this->getSelect()->where('supplier_product.supplier_id = ?', $supplierId);
        }

        if ($purchaseId) {
            $productIds = $purchaseItemService->getProductsByPurchaseOrderId($purchaseId)
                ->getColumnValues(PurchaseOrderItemInterface::PRODUCT_ID);
            if (!empty($productIds)) {
                $this->getSelect()
                    ->where("e.entity_id NOT IN ('" . implode("','", $productIds) . "')");
            }
        }

        return $this;
    }

    /**
     * Get Mapping Field
     *
     * @return array|mixed
     */
    public function getMappingField()
    {
        return $this->mappingField;
    }

    /**
     * Get collection size
     *
     * @return int
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
     * Add Field To Filter
     *
     * @param string $field
     * @param string|array $condition
     * @return $this|\Magento\Framework\Data\Collection\AbstractDb
     */
    public function addFieldToFilter($field, $condition = null)
    {
        foreach ($this->mappingField as $key => $value) {
            if ($field == $key) {
                $field = $value;
                return $this->addFieldToFilterCallBack($field, $condition);
            }
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * AddOrder
     *
     * @param string $field
     * @param string $direction
     * @return $this
     */
    public function addOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        foreach ($this->getMappingField() as $key => $value) {
            if ($field == $key) {
                $field = $value;
                $this->getSelect()->order(new \Zend_Db_Expr($field . ' ' . $direction));
                return $this;
            }
        }
        return parent::addOrder($field, $direction);
    }

    /**
     * Add Field To Filter Call Back
     *
     * @param string $field
     * @param string|array $condition
     */
    public function addFieldToFilterCallBack($field, $condition)
    {
        $conditionSql = $this->_getConditionSql($field, $condition);
        $this->getSelect()->having($conditionSql);
    }

    /**
     * Check Product Source
     *
     * @return bool
     */
    public function checkProductSource()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productConfig = $objectManager->get(\Magestore\PurchaseOrderSuccess\Service\Config\ProductConfig::class);
        return (boolean)($productConfig->getProductSource() ==
            \Magestore\PurchaseOrderSuccess\Model\System\Config\ProductSource::TYPE_SUPPLIER);
    }
}
