<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Model\ResourceModel\Order\BackOrder;

/**
 * Class Collection
 * @package Magestore\OrderSuccess\Model\ResourceModel\Sales\BackOrder
 */
class Collection extends \Magestore\OrderSuccess\Model\ResourceModel\Order\Collection
{
    /**
     * add condition.
     *
     * @param
     * @return $this
     */
    public function addCondition(){
        if($this->helper->getOrderConfig('verify')){
            $this->addFieldToFilter('is_verified', 1);
        }
        $this->addFieldToFilter('main_table.status', array(
            'nin'=> array(
                'holded',
                'canceled',
                'closed',
                'complete'
            )
        ));
        $this->getSelect()->join(
            ['sales_order_item' => $this->getTable('sales_order_item')],
            'main_table.entity_id = sales_order_item.order_id',
            [
                'qty_backordered' =>  new \Zend_Db_Expr(
                    'SUM(sales_order_item.qty_backordered)
                    *COUNT(DISTINCT sales_order_item.item_id)/COUNT(sales_order_item.item_id)
                    ')
            ]
        );
        $this->addFieldToFilter('qty_backordered', array('gt'=>0));
    }

}
