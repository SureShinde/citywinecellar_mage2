<?php
/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Model\ResourceModel\Report\PosOrder;

/**
 * POS Order entity resource model with aggregation by created at
 *
 * Class Createdat
 */
class Createdat extends \Magento\Reports\Model\ResourceModel\Report\AbstractReport
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Createdat constructor.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Reports\Model\FlagFactory $reportsFlagFactory
     * @param \Magento\Framework\Stdlib\DateTime\Timezone\Validator $timezoneValidator
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param string|null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Reports\Model\FlagFactory $reportsFlagFactory,
        \Magento\Framework\Stdlib\DateTime\Timezone\Validator $timezoneValidator,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        $connectionName = null
    ) {
        parent::__construct(
            $context,
            $logger,
            $localeDate,
            $reportsFlagFactory,
            $timezoneValidator,
            $dateTime,
            $connectionName
        );
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('pos_order_aggregated_created', 'id');
    }

    /**
     * Aggregate POS Orders data by order created at
     *
     * @param string|int|\DateTime|array|null $from
     * @param string|int|\DateTime|array|null $to
     * @return Createdat
     * @throws \Exception
     */
    public function aggregate($from = null, $to = null)
    {
        return $this->_aggregateByField('created_at', $from, $to);
    }

    /**
     * Aggregate POS Orders data by custom field
     *
     * @param string $aggregationField
     * @param string|int|\DateTime|array|null $from
     * @param string|int|\DateTime|array|null $to
     * @return $this
     * @throws \Exception
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _aggregateByField($aggregationField, $from, $to)
    {
        $connection = $this->getConnection();

        $connection->beginTransaction();
        try {
            if ($from !== null || $to !== null) {
                $subSelect = $this->_getTableDateRangeSelect(
                    $this->getTable('sales_order'),
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
                    ['o' => $this->getTable('sales_order')],
                    'o.' . $aggregationField,
                    $from,
                    $to
                )
            );

            $yearWeekMode = $this->scopeConfig->getValue(
                'reportsuccess/general/first_day_of_week',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $yearWeekExpr = new \Zend_Db_Expr("YEARWEEK($periodExpr, $yearWeekMode)");

            // Columns list
            $columns = [
                'period' => $periodExpr,
                'yearweek' => $yearWeekExpr,
                'location_id' => 'o.pos_location_id',
                'staff_id' => 'o.pos_staff_id',
                'order_status' => 'o.status',
                'orders_count' => new \Zend_Db_Expr('COUNT(o.entity_id)'),
                'total_qty_ordered' => new \Zend_Db_Expr('SUM(oi.total_qty_ordered)'),
                'total_qty_invoiced' => new \Zend_Db_Expr('SUM(oi.total_qty_invoiced)'),
                'total_income_amount' => new \Zend_Db_Expr(
                    sprintf(
                        'SUM((%s - %s) * %s)',
                        $connection->getIfNullSql('o.base_grand_total', 0),
                        $connection->getIfNullSql('o.base_total_canceled', 0),
                        $connection->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_revenue_amount' => new \Zend_Db_Expr(
                    sprintf(
                        'SUM((%s - %s - %s - (%s - %s - %s)) * %s)',
                        $connection->getIfNullSql('o.base_total_invoiced', 0),
                        $connection->getIfNullSql('o.base_tax_invoiced', 0),
                        $connection->getIfNullSql('o.base_shipping_invoiced', 0),
                        $connection->getIfNullSql('o.base_total_refunded', 0),
                        $connection->getIfNullSql('o.base_tax_refunded', 0),
                        $connection->getIfNullSql('o.base_shipping_refunded', 0),
                        $connection->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_profit_amount' => new \Zend_Db_Expr(
                    sprintf(
                        'SUM((%s - %s - %s - %s - %s) * %s)',
                        $connection->getIfNullSql('o.base_total_paid', 0),
                        $connection->getIfNullSql('o.base_total_refunded', 0),
                        $connection->getIfNullSql('o.base_tax_invoiced', 0),
                        $connection->getIfNullSql('o.base_shipping_invoiced', 0),
                        $connection->getIfNullSql('o.base_total_invoiced_cost', 0),
                        $connection->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_invoiced_cost_amount' => new \Zend_Db_Expr(
                    sprintf(
                        'SUM(%s * %s)',
                        $connection->getIfNullSql('o.base_total_invoiced_cost', 0),
                        $connection->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_invoiced_amount' => new \Zend_Db_Expr(
                    sprintf(
                        'SUM(%s * %s)',
                        $connection->getIfNullSql('o.base_total_invoiced', 0),
                        $connection->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_canceled_amount' => new \Zend_Db_Expr(
                    sprintf(
                        'SUM(%s * %s)',
                        $connection->getIfNullSql('o.base_total_canceled', 0),
                        $connection->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_paid_amount' => new \Zend_Db_Expr(
                    sprintf(
                        'SUM(%s * %s)',
                        $connection->getIfNullSql('o.base_total_paid', 0),
                        $connection->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_refunded_amount' => new \Zend_Db_Expr(
                    sprintf(
                        'SUM(%s * %s)',
                        $connection->getIfNullSql('o.base_total_refunded', 0),
                        $connection->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_tax_amount' => new \Zend_Db_Expr(
                    sprintf(
                        'SUM((%s - %s) * %s)',
                        $connection->getIfNullSql('o.base_tax_amount', 0),
                        $connection->getIfNullSql('o.base_tax_canceled', 0),
                        $connection->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_tax_amount_actual' => new \Zend_Db_Expr(
                    sprintf(
                        'SUM((%s -%s) * %s)',
                        $connection->getIfNullSql('o.base_tax_invoiced', 0),
                        $connection->getIfNullSql('o.base_tax_refunded', 0),
                        $connection->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_shipping_amount' => new \Zend_Db_Expr(
                    sprintf(
                        'SUM((%s - %s) * %s)',
                        $connection->getIfNullSql('o.base_shipping_amount', 0),
                        $connection->getIfNullSql('o.base_shipping_canceled', 0),
                        $connection->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_shipping_amount_actual' => new \Zend_Db_Expr(
                    sprintf(
                        'SUM((%s - %s) * %s)',
                        $connection->getIfNullSql('o.base_shipping_invoiced', 0),
                        $connection->getIfNullSql('o.base_shipping_refunded', 0),
                        $connection->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_discount_amount' => new \Zend_Db_Expr(
                    sprintf(
                        'SUM((ABS(%s) - %s) * %s)',
                        $connection->getIfNullSql('o.base_discount_amount', 0),
                        $connection->getIfNullSql('o.base_discount_canceled', 0),
                        $connection->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_discount_amount_actual' => new \Zend_Db_Expr(
                    sprintf(
                        'SUM((ABS(%s) - ABS(%s)) * %s)',
                        $connection->getIfNullSql('o.base_discount_invoiced', 0),
                        $connection->getIfNullSql('o.base_discount_refunded', 0),
                        $connection->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),

                'total_profit_margin_amount' => new \Zend_Db_Expr(
                    sprintf(
                        'SUM((%s - %s - %s - %s - %s) * %s) / SUM((%s - %s - %s - (%s - %s - %s)) * %s) * 100',
                        $connection->getIfNullSql('o.base_total_paid', 0),
                        $connection->getIfNullSql('o.base_total_refunded', 0),
                        $connection->getIfNullSql('o.base_tax_invoiced', 0),
                        $connection->getIfNullSql('o.base_shipping_invoiced', 0),
                        $connection->getIfNullSql('o.base_total_invoiced_cost', 0),
                        $connection->getIfNullSql('o.base_to_global_rate', 0),
                        $connection->getIfNullSql('o.base_total_invoiced', 0),
                        $connection->getIfNullSql('o.base_tax_invoiced', 0),
                        $connection->getIfNullSql('o.base_shipping_invoiced', 0),
                        $connection->getIfNullSql('o.base_total_refunded', 0),
                        $connection->getIfNullSql('o.base_tax_refunded', 0),
                        $connection->getIfNullSql('o.base_shipping_refunded', 0),
                        $connection->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),

                'average_order_value' => new \Zend_Db_Expr(
                    sprintf(
                        'SUM((%s - %s - %s - (%s - %s - %s)) * %s) / %s',
                        $connection->getIfNullSql('o.base_total_invoiced', 0),
                        $connection->getIfNullSql('o.base_tax_invoiced', 0),
                        $connection->getIfNullSql('o.base_shipping_invoiced', 0),
                        $connection->getIfNullSql('o.base_total_refunded', 0),
                        $connection->getIfNullSql('o.base_tax_refunded', 0),
                        $connection->getIfNullSql('o.base_shipping_refunded', 0),
                        $connection->getIfNullSql('o.base_to_global_rate', 0),
                        $connection->getIfNullSql('COUNT(o.entity_id)', 1)
                    )
                ),
            ];

            $select = $connection->select();
            $selectOrderItem = $connection->select();

            $qtyCanceledExpr = $connection->getIfNullSql('qty_canceled', 0);
            $cols = [
                'order_id' => 'order_id',
                'total_qty_ordered' => new \Zend_Db_Expr("SUM(qty_ordered - {$qtyCanceledExpr})"),
                'total_qty_invoiced' => new \Zend_Db_Expr('SUM(qty_invoiced)'),
            ];
            $selectOrderItem->from(
                $this->getTable('sales_order_item'),
                $cols
            )->where(
                'parent_item_id IS NULL'
            )->group(
                'order_id'
            );

            $select->from(
                ['o' => $this->getTable('sales_order')],
                $columns
            )->join(
                ['oi' => $selectOrderItem],
                'oi.order_id = o.entity_id',
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

            $select->group([$periodExpr, 'o.pos_location_id', 'o.pos_staff_id', 'o.status']);

            $connection->query($select->insertFromSelect($this->getMainTable(), array_keys($columns)));

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }

        return $this;
    }
}
