<?php
/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Model\ResourceModel\Report\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\Stdlib\DateTime\Filter\Date as DateFilter;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magestore\PosReports\Model\Source\TimeRange;
use Magestore\PosReports\Model\Source\PeriodType;
use Magestore\PosReports\Model\Filter;

/**
 * Class AbstractCollection
 *
 * Used to create Abstract Collection
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class AbstractCollection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var DateFilter
     */
    protected $dateFilter;

    /**
     * @var array
     */
    protected $aggregatedColumnsFilterMap = [];

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var string
     */
    protected $groupBy;

    /**
     * ByLocationCollection constructor.
     *
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param Filter $filter
     * @param DateFilter $dateFilter
     * @param ScopeConfigInterface $scopeConfig
     * @param string $mainTable
     * @param string $resourceModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        Filter $filter,
        DateFilter $dateFilter,
        ScopeConfigInterface $scopeConfig,
        $mainTable = 'pos_order_aggregated_created',
        $resourceModel = \Magestore\PosReports\Model\ResourceModel\Report\PosOrder\Createdat::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
        $this->filter = $filter;
        $this->dateFilter = $dateFilter;
        $this->scopeConfig = $scopeConfig;
        $this->initResource();
        $this->initCollection();
    }

    /**
     * Init select
     *
     * @return $this|\Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult|void
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $tableDescription = $this->getConnection()->describeTable($this->getMainTable());
        foreach ($tableDescription as $columnInfo) {
            $this->addFilterToMap($columnInfo['COLUMN_NAME'], 'main_table.' . $columnInfo['COLUMN_NAME']);
        }

        return $this;
    }

    /**
     * Init Resource
     *
     * @return $this
     */
    public function initResource()
    {

        return $this;
    }

    /**
     * Init collection
     *
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|mixed
     * @throws \Exception
     */
    public function initCollection()
    {
        $locationId = $this->filter->getFilter('location_id');
        $timeRange = $this->filter->getFilter('time_range');
        $from = $this->filter->getFilter('from');
        $to = $this->filter->getFilter('to');

        if ($from) {
            $from = $this->dateFilter->filter($from);
        }
        if ($to) {
            $to = $this->dateFilter->filter($to);
        }

        if ($timeRange == null) {
            $timeRange = TimeRange::CUSTOM_RANGE;
        }
        $this->addPeriodFilter($timeRange, $from, $to);
        $this->addLocationFilter($locationId);
        return $this;
    }

    /**
     * Retrieve total row data
     *
     * @param array $items
     * @return mixed
     */
    abstract public function getTotalItemData($items);

    /**
     * Retrieve array of columns that should be aggregated
     *
     * @return array
     */
    abstract public function getAggregatedColumns();

    /**
     * Set group by
     *
     * @param string $groupBy
     * @return $this
     */
    public function setGroupBy($groupBy)
    {
        $this->groupBy = $groupBy;
        return $this;
    }

    /**
     * Get group by
     *
     * @return string
     */
    public function getGroupBy()
    {
        return $this->groupBy;
    }

    /**
     * Add period filter
     *
     * @param string $timeRange
     * @param string $customStart
     * @param string $customEnd
     * @return $this
     * @throws \Exception
     */
    public function addPeriodFilter($timeRange, $customStart, $customEnd)
    {
        list($from, $to) = $this->getDateRange($timeRange, $customStart, $customEnd, true);
        $this->addFieldToFilter(
            'period',
            [
                'from' => $from->format(\Magento\Framework\Stdlib\DateTime::DATE_PHP_FORMAT),
                'to' => $to->format(\Magento\Framework\Stdlib\DateTime::DATE_PHP_FORMAT)
            ]
        );

        return $this;
    }

    /**
     * Add order status filter
     *
     * @param string $orderStatusSelector
     * @param string[] $orderStatus
     * @return $this
     */
    public function addOrderStatusFilter($orderStatusSelector, $orderStatus)
    {
        if (($orderStatusSelector == Boolean::VALUE_YES) && empty($orderStatus)) {
            $this->addFieldToFilter('main_table.location_id', ['null' => true]);
        }

        if (($orderStatusSelector == Boolean::VALUE_YES) && !empty($orderStatus)) {
            $this->addFieldToFilter('order_status', ['in' => $orderStatus]);
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
        if ((int) $locationId > 0) {
            $this->addFieldToFilter('main_table.location_id', $locationId);
        } else {
            $this->addFieldToFilter('main_table.location_id', ['null' => true]);
        }
        return $this;
    }

    /**
     * Calculate From and To dates (or times) by given period
     *
     * @param string $timeRange
     * @param string $customStart
     * @param string $customEnd
     * @param bool $returnObjects
     * @return array
     * @throws \Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getDateRange($timeRange, $customStart, $customEnd, $returnObjects = false)
    {
        $dateEnd = new \DateTime();
        $dateStart = new \DateTime();

        $startMonthDay = explode(
            ',',
            $this->scopeConfig->getValue(
                'reports/dashboard/ytd_start',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        );
        $startMonth = isset($startMonthDay[0]) ? (int)$startMonthDay[0] : 1;
        $startDay = isset($startMonthDay[1]) ? (int)$startMonthDay[1] : 1;

        switch ($timeRange) {
            case TimeRange::YESTERDAY:
                $dateStart->modify('-1 day');
                $dateEnd->modify('-1 day');
                break;
            case TimeRange::LAST_7_DAYS:
                // substract 6 days we need to include
                // only today and not hte last one from range
                $dateStart->modify('-6 days');
                break;
            case TimeRange::LAST_30_DAYS:
                $dateStart->modify('-30 days');
                break;
            case TimeRange::THIS_YEAR:
                $dateStart->setDate($dateStart->format('Y'), $startMonth, $startDay);
                $dateStart->setTime(0, 0, 0);
                break;
            case TimeRange::LAST_YEAR:
                $dateEnd->setDate($dateStart->format('Y'), $startMonth, $startDay);
                $dateEnd->modify('-1 day');
                $dateEnd->setTime(23, 59, 59);

                $dateStart = clone $dateEnd;
                $dateStart->setDate($dateStart->format('Y'), $startMonth, $startDay);
                $dateStart->setTime(0, 0, 0);
                break;
            case TimeRange::CUSTOM_RANGE:
                $dateStart = new \DateTime($customStart);
                $dateEnd = new \DateTime($customEnd);
                break;
            case TimeRange::TODAY:
            default:
                break;
        }

        if ($returnObjects) {
            return [$dateStart, $dateEnd];
        } else {
            return ['from' => $dateStart, 'to' => $dateEnd, 'datetime' => true];
        }
    }

    /**
     * Add field to filter
     *
     * @param string $field
     * @param array|null $condition
     * @return AbstractCollection|void
     */
    public function addFieldToFilter($field, $condition = null)
    {
        foreach ($this->aggregatedColumnsFilterMap as $key => $value) {
            if ($field == $key) {
                $field = $value;
                $conditionSql = $this->_getConditionSql($field, $condition);
                $this->getSelect()->having($conditionSql);
            }
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Get period type
     *
     * @return mixed|string
     */
    public function getPeriodType()
    {
        return $this->filter->getFilter('period_type');
    }

    /**
     * Get period format
     *
     * @param string $periodType
     * @return \Zend_Db_Expr
     */
    public function getPeriodFormat($periodType = "")
    {
        $periodType = $periodType ? $periodType : $this->getPeriodType();
        $connection = $this->getConnection();
        if (PeriodType::MONTH == $periodType) {
            $periodFormat = $connection->getDateFormatSql('period', '%Y-%m');
        } elseif (PeriodType::YEAR == $periodType) {
            $periodFormat = $connection->getDateExtractSql(
                'period',
                \Magento\Framework\DB\Adapter\AdapterInterface::INTERVAL_YEAR
            );
        } else {
            $periodFormat = $connection->getDateFormatSql('period', '%Y-%m-%d');
        }
        return $periodFormat;
    }

    /**
     * Get sum column values
     *
     * @param array $items
     * @param string $colName
     * @return float
     */
    public function getSumColumnValues($items, $colName)
    {
        $total = 0;
        foreach ($items as $item) {
            $total += $item->getData($colName);
        }
        return $total;
    }

    /**
     * Get items
     *
     * @return array|\Magento\Framework\Api\Search\DocumentInterface[]
     */
    public function getItems()
    {
        $items = parent::getItems();
        $totalItemData = $this->getTotalItemData($items);
        if (!empty($items) && !empty($totalItemData)) {
            $totalItem = $this->getNewEmptyItem();
            $totalItem->setData($totalItemData);
            $items[] = $totalItem;
        }
        return $items;
    }

    /**
     * Apply custom columns before load
     *
     * @return $this
     */
    protected function _beforeLoad()
    {
        $this->getSelect()->reset(\Magento\Framework\DB\Select::COLUMNS);
        $this->getSelect()->columns($this->getAggregatedColumns());
        $groupBy = $this->getGroupBy();
        if ($groupBy) {
            $this->getSelect()->group($groupBy);
        }
        return parent::_beforeLoad();
    }
}
