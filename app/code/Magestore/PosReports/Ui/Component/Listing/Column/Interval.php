<?php

/**
 * Copyright © Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\Locale\Bundle\DataBundle;
use Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magestore\PosReports\Model\Source\PeriodType;
use Magestore\PosReports\Model\Source\FirstDayOfWeek;

/**
 * Class Interval
 *
 * Used to create Interval
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Interval extends Column
{
    /**
     * @var DateTimeFormatterInterface
     */
    protected $dateTimeFormatter;

    /**
     * @var ResolverInterface
     */
    protected $localeResolver;

    /**
     * @var TimezoneInterface
     */
    protected $localeDate;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Interval constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param DateTimeFormatterInterface $dateTimeFormatter
     * @param ResolverInterface $localeResolver
     * @param TimezoneInterface $localeDate
     * @param ScopeConfigInterface $scopeConfig
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        DateTimeFormatterInterface $dateTimeFormatter,
        ResolverInterface $localeResolver,
        TimezoneInterface $localeDate,
        ScopeConfigInterface $scopeConfig,
        array $components = [],
        array $data = []
    ) {
        $this->dateTimeFormatter = $dateTimeFormatter;
        $this->localeResolver = $localeResolver;
        $this->localeDate = $localeDate;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items']) && !empty($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $periodType = isset($item['period_type']) ? $item['period_type'] : null;
                $dateString = (isset($item[$this->getData('name')])) ? $item[$this->getData('name')] : "";
                $yearweek = (isset($item['yearweek'])) ? $item['yearweek'] : "";
                if ((!empty($dateString) || !empty($yearweek)) && $periodType) {
                    try {
                        $item[$this->getData('name')] = $this->format($periodType, $dateString, $yearweek);
                    } catch (\Exception $e) {
                        return $dataSource;
                    }
                }
            }
        }

        return $dataSource;
    }

    /**
     * Retrieve date format
     *
     * @param string $periodType
     * @return mixed
     */
    public function getFormat($periodType)
    {
        $dataBundle = new DataBundle();
        $resourceBundle = $dataBundle->get($this->localeResolver->getLocale());
        $formats = $resourceBundle['calendar']['gregorian']['availableFormats'];
        switch ($periodType) {
            case PeriodType::MONTH:
                $format = $formats['yMMM'];
                break;
            case PeriodType::YEAR:
                $format = $formats['y'];
                break;
            default:
                $format = $this->localeDate->getDateFormat(\IntlDateFormatter::MEDIUM);
                break;
        }
        return $format;
    }

    /**
     * Format
     *
     * @param string $periodType
     * @param string $dateString
     * @param string $yearweek
     * @return string
     */
    public function format($periodType, $dateString = "", $yearweek = "")
    {
        if (($dateString || $yearweek) && $periodType) {
            if ($periodType == PeriodType::WEEK) {
                if ($yearweek) {
                    $yearWeekData = str_split($yearweek, 4);
                    if (count($yearWeekData) > 1) {
                        $year = $yearWeekData[0];
                        $week = $yearWeekData[1];
                        return $this->getWeekInterval($week, $year);
                    }
                }
            } else {
                switch ($periodType) {
                    case PeriodType::MONTH:
                        $dateString = $dateString . '-01';
                        break;
                    case PeriodType::YEAR:
                        $dateString = $dateString . '-01-01';
                        break;
                }
                $format = $this->getFormat($periodType);
                $date = $this->localeDate->date(new \DateTime($dateString), null, false);
                return $this->dateTimeFormatter->formatObject($date, $format, $this->localeResolver->getLocale());
            }

        }
        return $dateString;
    }

    /**
     * Get week interval
     *
     * @param string $week
     * @param int $year
     * @return string
     */
    public function getWeekInterval($week, $year)
    {
        $yearWeekMode = $this->scopeConfig->getValue(
            'reportsuccess/general/first_day_of_week',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($yearWeekMode == FirstDayOfWeek::SUNDAY) {
            ++$week;
        }
        $format = $this->getFormat(PeriodType::WEEK);
        $dateTime = new \DateTime();
        $dateTime->setISODate($year, $week, $yearWeekMode);
        $startDate = $this->localeDate->date($dateTime, null, false);
        $startDateString = $this->dateTimeFormatter->formatObject(
            $startDate,
            $format,
            $this->localeResolver->getLocale()
        );

        $dateTime->modify('+6 days');
        $endDate = $this->localeDate->date($dateTime, null, false);
        $endDateString = $this->dateTimeFormatter->formatObject($endDate, $format, $this->localeResolver->getLocale());
        return $startDateString . " - " . $endDateString;
    }
}
