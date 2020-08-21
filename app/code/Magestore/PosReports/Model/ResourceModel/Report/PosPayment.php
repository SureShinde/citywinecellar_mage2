<?php
/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Model\ResourceModel\Report;

/**
 * POS Order Payment resource model
 *
 * Used to create Class PosPayment
 */
class PosPayment extends \Magento\Reports\Model\ResourceModel\Report\AbstractReport
{

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('pos_order_payment_aggregated', 'id');
    }

    /**
     * Aggregate POS Order Payment data
     *
     * @param string|int|\DateTime|array|null $from
     * @param string|int|\DateTime|array|null $to
     * @return $this
     * @throws \Exception
     */
    public function aggregate($from = null, $to = null)
    {
        $aggregationField = 'payment_date';
        $connection = $this->getConnection();

        $connection->beginTransaction();
        try {
            if ($from !== null || $to !== null) {
                $subSelect = $this->_getTableDateRangeSelect(
                    $this->getTable('webpos_order_payment'),
                    $aggregationField,
                    $aggregationField,
                    $from,
                    $to
                );
            } else {
                $subSelect = null;
            }
            $this->_clearTableByDateRange($this->getMainTable(), $from, $to, $subSelect);

            $periodExpr = $connection->getDatePartSql(
                $this->getStoreTZOffsetQuery(
                    ['wop' => $this->getTable('webpos_order_payment')],
                    'wop.' . $aggregationField,
                    $from,
                    $to
                )
            );

            $ordersCountExpr = new \Zend_Db_Expr('COUNT(o.entity_id)');
            $totalPaidExpr = new \Zend_Db_Expr(
                sprintf(
                    'SUM(%s)',
                    $connection->getCaseSql('type', [0 => 'wop.base_amount_paid'], 0)
                )
            );
            $totalRefundedExpr = new \Zend_Db_Expr(
                sprintf(
                    'SUM(%s)',
                    $connection->getCaseSql('type', [1 => 'wop.base_amount_paid'], 0)
                )
            );
            $netAmountExpr = new \Zend_Db_Expr(
                sprintf(
                    '%s - %s',
                    $totalPaidExpr,
                    $totalRefundedExpr
                )
            );

            // Columns list
            $columns = [
                'period' => $periodExpr,
                'location_id' => 'o.pos_location_id',
                'method' => 'wop.method',
                'method_title' => 'wop.title',
                'order_status' => 'o.status',
                'orders_count' => $ordersCountExpr,
                'total_paid' => $totalPaidExpr,
                'total_refunded' => $totalRefundedExpr,
                'net_amount' => $netAmountExpr
            ];

            $select = $connection->select();
            $select->from(
                ['wop' => $this->getTable('webpos_order_payment')],
                $columns
            )->join(
                ['o' => $this->getTable('sales_order')],
                'wop.order_id = o.entity_id',
                []
            )->where(
                'o.state NOT IN (?)',
                [\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT, \Magento\Sales\Model\Order::STATE_NEW]
            )->where(
                'o.pos_location_id IS NOT NULL AND o.pos_staff_id IS NOT NULL'
            );

            if ($subSelect !== null) {
                $select->having($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }

            $select->group([$periodExpr, 'o.pos_location_id', 'o.status', 'wop.method']);

            $connection->query($select->insertFromSelect($this->getMainTable(), array_keys($columns)));

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }

        $this->_setFlagData(\Magestore\PosReports\Model\Flag::REPORT_POS_SALES_BY_PAYMENT_FLAG_CODE);
        return $this;
    }
}
