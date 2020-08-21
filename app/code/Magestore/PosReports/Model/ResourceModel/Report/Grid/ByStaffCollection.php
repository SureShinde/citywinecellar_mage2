<?php
/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Model\ResourceModel\Report\Grid;

/**
 * Class ByStaffCollection
 *
 * Used to create By Staff Collection
 */
class ByStaffCollection extends AbstractCollection
{
    /**
     * Init resource
     *
     * @return $this
     */
    public function initResource()
    {
        $dateUsed = $this->filter->getFilter('date_used');
        if ($dateUsed == \Magestore\PosReports\Model\Source\DateUsed::UPDATED_AT) {
            $this->setMainTable('pos_order_aggregated_updated');
            $this->setResourceModel(\Magestore\PosReports\Model\ResourceModel\Report\PosOrder\Updatedat::class);
        }
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
        $totalTaxAmountActualExpr = new \Zend_Db_Expr("SUM(total_tax_amount_actual)");
        $totalDiscountAmountActualExpr = new \Zend_Db_Expr("SUM(total_discount_amount_actual)");
        $totalRefundedAmountExpr = new \Zend_Db_Expr("SUM(total_refunded_amount)");
        $totalRevenueAmountExpr = new \Zend_Db_Expr("SUM(total_revenue_amount)");
        $averageOrderValueExpr = new \Zend_Db_Expr(
            sprintf(
                '%s / %s',
                $this->getConnection()->getIfNullSql($totalRevenueAmountExpr, 0),
                $this->getConnection()->getIfNullSql($ordersCountExpr, 1)
            )
        );
        $aggregatedColumns = [
            'staff_id' => "staff_id",
            'orders_count' => $ordersCountExpr,
            'total_tax_amount_actual' => $totalTaxAmountActualExpr,
            'total_discount_amount_actual' => $totalDiscountAmountActualExpr,
            'total_refunded_amount' => $totalRefundedAmountExpr,
            'total_revenue_amount' => $totalRevenueAmountExpr,
            'average_order_value' => $averageOrderValueExpr
        ];
        $this->setGroupBy("staff_id");
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
        $totalTax = $this->getSumColumnValues($items, 'total_tax_amount_actual');
        $totalDiscount = $this->getSumColumnValues($items, 'total_discount_amount_actual');
        $totalRefund = $this->getSumColumnValues($items, 'total_refunded_amount');
        $totalRevenueAmount = $this->getSumColumnValues($items, 'total_revenue_amount');
        return [
            'staff_id' => 0,
            'orders_count' => $totalOrdersCount ? $totalOrdersCount : 0,
            'total_tax_amount_actual' => $totalTax ? $totalTax : 0,
            'total_discount_amount_actual' => $totalDiscount ? $totalDiscount : 0,
            'total_refunded_amount' => $totalRefund ? $totalRefund : 0,
            'total_revenue_amount' => $totalRevenueAmount ? $totalRevenueAmount : 0,
            'average_order_value' => $totalRevenueAmount / ($totalOrdersCount ? $totalOrdersCount : 1)
        ];
    }
}
