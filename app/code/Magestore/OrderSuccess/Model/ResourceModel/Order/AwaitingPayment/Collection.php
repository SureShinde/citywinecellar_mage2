<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Model\ResourceModel\Order\AwaitingPayment;

/**
 * Awaiting Payment Order Collection
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
        $this->getSelect()->columns(
            [
                'real_total_due' => new \Zend_Db_Expr(
                    'COALESCE(sales_order.total_due, 0) - COALESCE(sales_order.total_canceled, 0)'
                )
            ]
        );
        $this->getSelect()->where(
            'COALESCE(sales_order.total_due, 0) - COALESCE(sales_order.total_canceled, 0) > 0'
        );
    }

    /**
     * Add Field To Filter
     *
     * @param array|string $field
     * @param array|string $condition
     * @return \Magento\Sales\Model\ResourceModel\Order\Grid\Collection
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'real_total_due') {
            $field = new \Zend_Db_Expr(
                'COALESCE(sales_order.total_due, 0) - COALESCE(sales_order.total_canceled, 0)'
            );
        }
        return parent::addFieldToFilter($field, $condition);
    }
}
