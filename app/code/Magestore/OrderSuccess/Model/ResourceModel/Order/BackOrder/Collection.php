<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Model\ResourceModel\Order\BackOrder;

/**
 * Backorder Collection
 */
class Collection extends \Magestore\OrderSuccess\Model\ResourceModel\Order\Collection
{
    /**
     * Add Condition
     *
     * @return Collection|void
     */
    public function addCondition()
    {
        if ($this->helper->getOrderConfig('verify')) {
            $this->addFieldToFilter('is_verified', 1);
        }
        $this->addFieldToFilter(
            'main_table.status',
            [
                'nin' => [
                    'holded',
                    'canceled',
                    'closed',
                    'complete'
                ]
            ]
        );
        $this->getSelect()->join(
            ['sales_order_item' => $this->getTable('sales_order_item')],
            'main_table.entity_id = sales_order_item.order_id',
            [
                'qty_backordered' => new \Zend_Db_Expr(
                    'SUM(sales_order_item.qty_backordered)
                    * COUNT(DISTINCT sales_order_item.item_id)/COUNT(sales_order_item.item_id)'
                )
            ]
        );
        $this->addFieldToFilter('qty_backordered', ['gt' => 0]);
    }
}
