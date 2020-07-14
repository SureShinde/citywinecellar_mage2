<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Model\ResourceModel\Order\NeedShip;

/**
 * Class Collection
 * @package Magestore\OrderSuccess\Model\ResourceModel\Order\NeedShip
 */
class Collection extends \Magestore\OrderSuccess\Model\ResourceModel\Order\Collection
{
    protected $qtyOrdered = 'SUM(sales_order_item.qty_ordered
                - sales_order_item.qty_shipped
                - sales_order_item.qty_refunded
                - sales_order_item.qty_canceled
                - COALESCE(sales_order_item.qty_backordered, 0)
                - COALESCE(sales_order_item.qty_prepareship, 0))';

    /**
     * @return $this
     */
    protected function _initSelect()
    {
        $this->addFilterToMap(
            'qty_ordered',
            new \Zend_Db_Expr($this->qtyOrdered)
        );

        return parent::_initSelect();
    }

    /**
     * add condition.
     *
     * @param
     * @return $this
     */
    public function addCondition()
    {
        if ($this->helper->getOrderConfig('verify')) {
            $this->addFieldToFilter('is_verified', 1);
        }
        $this->addFieldToFilter('sales_order.is_virtual', 0);
        $this->addFieldToFilter('main_table.status', array(
            'nin' => array(
                'holded',
                'canceled',
                'closed',
                'payment_review',
                'complete'
            )
        ));
        $salesOrderItemJoinCondition = 'main_table.entity_id = sales_order_item.order_id 
             && sales_order_item.parent_item_id IS NULL
             && ( sales_order_item.is_virtual IS NULL || sales_order_item.is_virtual = 0)
             && sales_order_item.locked_do_ship IS NULL
            ';
        // Special case when order item is a bundle product with separately shipping type
        // Do not calculate prepare qty of bundle parent product with separately shipping type
        if($this->getListBundleSeparateNeedShip()) {
            $salesOrderItemJoinCondition = 'main_table.entity_id = sales_order_item.order_id 
             && (sales_order_item.parent_item_id IS NULL || sales_order_item.parent_item_id IN ' . '(' . implode(',', $this->getListBundleSeparateNeedShip()) . '))
             && ( sales_order_item.is_virtual IS NULL || sales_order_item.is_virtual = 0)
             && sales_order_item.locked_do_ship IS NULL
             && sales_order_item.item_id NOT IN ' . '(' . implode(',', $this->getListBundleSeparateNeedShip()) . ')'
            ;
        }
        $this->getSelect()->join(
            ['sales_order_item' => $this->getTable('sales_order_item')],
            $salesOrderItemJoinCondition,
            ['qty_ordered' => new \Zend_Db_Expr($this->qtyOrdered)]
        );

        $this->getSelect()->having(new \Zend_Db_Expr($this->qtyOrdered).' > ?', 0);

        $invalidOrderId = $this->getListOrderWithBundleDoNotNeedShip();

        if($invalidOrderId) {
            $this->getSelect()->where(new \Zend_Db_Expr('main_table.entity_id') . ' NOT IN (?)', $invalidOrderId);
        }
    }

    /**
     * Return list order item id of bundle products with separately shipping type
     *
     * @return array
     */
    public function getListBundleSeparateNeedShip() {
        $connection = $this->getConnection();
        $sql = clone $this->getSelect();
        $sql->reset();
        $sql->from($this->getTable('sales_order_item'));
        $sql->where("
            product_type = '" . \Magento\Bundle\Model\Product\Type::TYPE_CODE . "' 
            AND (product_options LIKE '%\"shipment_type\":\"1\"%' OR product_options LIKE '%\"shipment_type\":1%')
        ");
        $dataResult = $connection->fetchAll($sql);
        $bundleParentIdsValid = [];
        foreach ($dataResult as $data) {
            $bundleParentIdsValid[] = $data['item_id'];
        }
        return $bundleParentIdsValid;
    }

    public function getListOrderWithBundleDoNotNeedShip() {
        $connection = $this->getConnection();
        $sql = clone $this->getSelect();

        $sql->join(['sales_order_item_type' => $this->getTable('sales_order_item')],
            'main_table.entity_id = sales_order_item_type.order_id 
             && sales_order_item_type.product_type = "' . \Magento\Bundle\Model\Product\Type::TYPE_CODE . '"',
            []);

        $dataResult = $connection->fetchAll($sql);
        $orderIds = [];
        foreach ($dataResult as $data) {
            $orderIds[] = $data['entity_id'];
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria */
        $searchCriteria = $objectManager->get('Magento\Framework\Api\SearchCriteriaInterface');
        /** @var \Magento\Framework\Api\Search\FilterGroup $filterGroup */
        $filterGroup = $objectManager->get('Magento\Framework\Api\Search\FilterGroup');
        /** @var \Magento\Framework\Api\Filter $filter */
        $filter = $objectManager->get('Magento\Framework\Api\Filter');
        $filter->setField('entity_id');
        $filter->setValue($orderIds);
        $filter->setConditionType('in');

        $filterGroup->setFilters([$filter]);
        $searchCriteria->setFilterGroups([$filterGroup]);

        /** @var \Magento\Sales\Api\OrderRepositoryInterface $orderRepository */
        $orderRepository = $objectManager->get('Magento\Sales\Api\OrderRepositoryInterface');
        $listOrder = $orderRepository->getList($searchCriteria);

        $invalidOrdersId = [];
        foreach ($listOrder->getItems() as $item) {
            if($item->canShip() && !$item->getForcedShipmentWithInvoice()) {
                continue;
            }
            $invalidOrdersId[] = $item->getEntityId();
        }

        return $invalidOrdersId;
    }

    /**
     * rewrite add field to filters from collection
     *
     * @return array
     */
//    public function addFieldToFilter($field, $condition = null)
//    {
//        if ($field == 'qty_ordered') {
//            $field = new \Zend_Db_Expr('
//                sales_order_item.qty_ordered
//                - sales_order_item.qty_shipped
//                - sales_order_item.qty_refunded
//                - sales_order_item.qty_canceled
//                - COALESCE(sales_order_item.qty_backordered, 0)
//                - COALESCE(sales_order_item.qty_prepareship, 0)
//            ');
//        }
//        return parent::addFieldToFilter($field, $condition);
//    }

}
