<?php
/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Model\ResourceModel\Report\Grid;

/**
 * Class ByPaymentMethodCollection
 *
 * Used to create By Payment Method Collection
 */
class ByPaymentMethodCollection extends AbstractCollection
{
    /**
     * Init resource
     *
     * @return $this
     */
    public function initResource()
    {
        $this->setMainTable('pos_order_payment_aggregated');
        $this->setResourceModel(\Magestore\PosReports\Model\ResourceModel\Report\PosPayment::class);
        return $this;
    }

    /**
     * Add location filter
     *
     * @param string $locationId
     * @return $this
     */
    public function addLocationFilter($locationId)
    {
        if ((int)$locationId > 0) {
            $this->addFieldToFilter('main_table.location_id', $locationId);
        }
        return $this;
    }

    /**
     * Get aggregated columns
     *
     * @return array
     */
    public function getAggregatedColumns()
    {
        $ordersCountExpr = new \Zend_Db_Expr("SUM(orders_count)");
        $totalPaidExpr = new \Zend_Db_Expr("SUM(total_paid)");
        $totalRefundedExpr = new \Zend_Db_Expr("SUM(total_refunded)");
        $netAmountExpr = new \Zend_Db_Expr("SUM(net_amount)");
        $aggregatedColumns = [
            'method' => "method",
            'method_title' => "method_title",
            'orders_count' => $ordersCountExpr,
            'total_paid' => $totalPaidExpr,
            'total_refunded' => $totalRefundedExpr,
            'net_amount' => $netAmountExpr
        ];
        $this->setGroupBy("method");
        return $aggregatedColumns;
    }

    /**
     * Get total item data
     *
     * @param array $items
     * @return array|mixed
     */
    public function getTotalItemData($items)
    {
        $totalOrdersCount = $this->getSumColumnValues($items, 'orders_count');
        $totalPaid = $this->getSumColumnValues($items, 'total_paid');
        $totalRefund = $this->getSumColumnValues($items, 'total_refunded');
        $netAmount = $this->getSumColumnValues($items, 'net_amount');
        return [
            'method' => __("Total"),
            'method_title' => __("Total"),
            'orders_count' => $totalOrdersCount ? $totalOrdersCount : 0,
            'total_paid' => $totalPaid ? $totalPaid : 0,
            'total_refunded' => $totalRefund ? $totalRefund : 0,
            'net_amount' => $netAmount ? $netAmount : 0,
        ];
    }
}
