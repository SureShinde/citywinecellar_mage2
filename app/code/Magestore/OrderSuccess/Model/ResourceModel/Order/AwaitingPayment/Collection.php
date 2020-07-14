<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Model\ResourceModel\Order\AwaitingPayment;

/**
 * Class Collection
 * @package Magestore\OrderSuccess\Model\ResourceModel\Sales\AwaitingPayment
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
        $this->getSelect()->columns([
                                    'real_total_due' => new \Zend_Db_Expr('
                                          COALESCE(sales_order.total_due, 0) - COALESCE(sales_order.total_canceled, 0)
                                        ')
                                    ]);
        $this->getSelect()->where('COALESCE(sales_order.total_due, 0) - COALESCE(sales_order.total_canceled, 0) > 0');
        //$this->addFieldToFilter('real_total_due', array('gt'=>0));
    }

    /**
     * rewrite add field to filters from collection
     *
     * @return array
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'real_total_due') {
            $field = new \Zend_Db_Expr('
                                COALESCE(sales_order.total_due, 0) - COALESCE(sales_order.total_canceled, 0)
                              ');
        }
        return parent::addFieldToFilter($field, $condition);
    }
}
