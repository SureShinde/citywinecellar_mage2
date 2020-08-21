<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

namespace Magestore\Customercredit\Block\Adminhtml\Report;

/**
 * Class Graph
 *
 * Report graph block
 */
class Graph extends \Magento\Backend\Block\Dashboard\Graph
{
    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $_localeCurrency;
    /**
     * @var \Magestore\Customercredit\Helper\Report\Customercredit
     */
    protected $_dataHelper;
    /**
     * @var \Magestore\Customercredit\Model\TransactionFactory
     */
    protected $_transaction;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * Graph constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Reports\Model\ResourceModel\Order\CollectionFactory $collectionFactory
     * @param \Magento\Backend\Helper\Dashboard\Data $dashboardData
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param \Magestore\Customercredit\Helper\Report\Customercredit $dataHelper
     * @param \Magestore\Customercredit\Model\TransactionFactory $transaction
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Reports\Model\ResourceModel\Order\CollectionFactory $collectionFactory,
        \Magento\Backend\Helper\Dashboard\Data $dashboardData,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magestore\Customercredit\Helper\Report\Customercredit $dataHelper,
        \Magestore\Customercredit\Model\TransactionFactory $transaction,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        array $data = []
    ) {
        $this->_storeManager = $context->getStoreManager();
        $this->_localeCurrency = $localeCurrency;
        $this->_dataHelper = $dataHelper;
        $this->_transaction = $transaction;
        $this->serializer = $serializer;
        parent::__construct($context, $collectionFactory, $dashboardData, $data);
    }

    protected $_googleChartParams = [
        'cht' => 'lc',
        'chf' => 'bg,s,f4f4f4|c,lg,90,ffffff,0.1,ededed,0',
        'chm' => 'B,f4d4b2,0,0,0',
        'chco' => 'db4814',
    ];
    protected $_width = '587';
    protected $_height = '300';

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('customercredit/report/template.phtml');
    }

    /**
     * @inheritDoc
     */
    public function getWidth()
    {
        return $this->_width;
    }

    /**
     * @inheritDoc
     */
    public function getHeight()
    {
        return $this->_height;
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getChartUrl($directUrl = true) // phpcs:ignore Generic.Metrics.NestingLevel
    {
        $directUrl = true;
        $params = $this->_googleChartParams;
        $this->_allSeries = $this->getRowsData($this->_dataRows);
        foreach ($this->_axisMaps as $axis => $attr) {
            $this->setAxisLabels($axis, $this->getRowsData($attr, true));
        }

        $timezoneLocal = $this->_scopeConfig->getValue(
            $this->_localeDate->getDefaultTimezonePath(),
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        //difference
        list ($dateStart, $dateEnd) = $this->_transaction->create()->getCollection()
            ->getDateRange($this->getDataHelper()->getParam('period'), '', '', true);

        $tzDateStart = clone $dateStart;
        $tzDateStart->setTimezone(new \DateTimeZone($timezoneLocal));

        $dates = [];
        $datas = [];

        while ($dateStart <= $dateEnd) {
            switch ($this->getDataHelper()->getParam('period')) {
                case '24h':
                    $d = $dateStart->format('Y-m-d H:00');
                    $dLabel = $tzDateStart->format('Y-m-d H:00');
                    $dateStart->modify('+1 hour');
                    $tzDateStart->modify('+1 hour');
                    break;
                case '7d':
                case '1m':
                    $d = $dateStart->format('Y-m-d');
                    $dLabel = $tzDateStart->format('Y-m-d');
                    $dateStart->modify('+1 day');
                    $tzDateStart->modify('+1 day');
                    break;
                case '1y':
                case '2y':
                    $d = $dateStart->format('Y-m');
                    $dLabel = $dateStart->format('Y-m');
                    $dateStart->modify('+1 month');
                    break;
            }
            foreach ($this->getAllSeries() as $index => $serie) {

                if (in_array($d, $this->_axisLabels['x'])) {
                    $datas[$index][] = (float)array_shift($this->_allSeries[$index]);
                } else {
                    $datas[$index][] = 0;
                }
            }
            $dates[] = $dLabel;
        }

        /**
         * setting skip step
         */
        if (count($dates) > 8 && count($dates) < 15) {
            $c = 1;
        } elseif (count($dates) >= 15) {
            $c = 2;
        } else {
            $c = 0;
        }
        /**
         * skipping some x labels for good reading
         */
        $i = 0;
        foreach ($dates as $k => $d) {
            if ($i == $c) {
                $dates[$k] = $d;
                $i = 0;
            } else {
                $dates[$k] = '';
                $i++;
            }
        }

        $this->_axisLabels['x'] = $dates;
        $this->_allSeries = $datas;

        //Google encoding values
        switch ($this->_encoding) {
            case 's':
                // simple encoding
                $params['chd'] = "s:";
                $dataDelimiter = "";
                $dataSetdelimiter = ",";
                break;

            default:
                // extended encoding
                $params['chd'] = "e:";
                $dataDelimiter = "";
                $dataSetdelimiter = ",";
                break;
        }

        // process each string in the array, and find the max length
        foreach ($this->getAllSeries() as $index => $serie) {
            $localmaxvalue[$index] = max($serie);
            $localminvalue[$index] = min($serie);
        }

        $maxvalue = max($localmaxvalue);
        $minvalue = min($localminvalue);

        // default values
        $yrange = 0;
        $yLabels = [];
        $yorigin = 0;

        if ($minvalue >= 0 && $maxvalue >= 0) {
            $miny = 0;
            if ($maxvalue > 10) {
                $p = pow(10, $this->_getPow($maxvalue));
                $maxy = (ceil($maxvalue / $p)) * $p;
                $yLabels = range($miny, $maxy, $p);
            } else {
                $maxy = ceil($maxvalue + 1);
                $yLabels = range($miny, $maxy, 1);
            }
            $yrange = $maxy;
            $yorigin = 0;
        }

        $chartdata = $this->getChartData($yrange, $yorigin, $dataDelimiter, $dataSetdelimiter);

        $buffer = implode('', $chartdata);

        $buffer = rtrim($buffer, $dataSetdelimiter);
        $buffer = rtrim($buffer, $dataDelimiter);
        $buffer = str_replace(($dataDelimiter . $dataSetdelimiter), $dataSetdelimiter, $buffer);

        $params['chd'] .= $buffer;

        $valueBuffer = [];

        if (count($this->_axisLabels) > 0) {
            if (!isset($params['chxt'])) {
                $params['chxt'] = implode(',', array_keys($this->_axisLabels));
            }
            $indexid = 0;
            foreach (array_keys($this->_axisLabels) as $idx) {
                switch ($idx) {
                    case 'x':
                        foreach ($this->_axisLabels[$idx] as $_index => $_label) {
                            if ($_label != '') {
                                $period = new \DateTime($_label, new \DateTimeZone($timezoneLocal));
                                switch ($this->getDataHelper()->getParam('period')) {
                                    case '24h':
                                        $this->_axisLabels[$idx][$_index] = $this->_localeDate->formatDateTime(
                                            $period->setTime($period->format('H'), 0, 0),
                                            \IntlDateFormatter::NONE,
                                            \IntlDateFormatter::SHORT
                                        );
                                        break;
                                    case '7d':
                                    case '1m':
                                        $this->_axisLabels[$idx][$_index] = $this->_localeDate->formatDateTime(
                                            $period,
                                            \IntlDateFormatter::SHORT,
                                            \IntlDateFormatter::NONE
                                        );
                                        break;
                                    case '1y':
                                    case '2y':
                                        $this->_axisLabels[$idx][$_index] = date('m/Y', strtotime($_label));
                                        break;
                                }
                            } else {
                                $this->_axisLabels[$idx][$_index] = '';
                            }
                        }

                        $tmpstring = implode('|', $this->_axisLabels[$idx]);

                        $valueBuffer[] = $indexid . ":|" . $tmpstring;
                        if (count($this->_axisLabels[$idx]) > 1) {
                            $deltaX = 100 / (count($this->_axisLabels[$idx]) - 1);
                        } else {
                            $deltaX = 100;
                        }
                        break;
                    case 'y':
                        $valueBuffer[] = $indexid . ":|" . implode('|', $yLabels);
                        if (count($yLabels) - 1) {
                            $deltaY = 100 / (count($yLabels) - 1);
                        } else {
                            $deltaY = 100;
                        }
                        break;
                    case 'r':
                        $valueBuffer[] = "3:|" . implode('|', $yLabels);
                        if (count($yLabels) - 1) {
                            $deltaY = 100 / (count($yLabels) - 1);
                        } else {
                            $deltaY = 100;
                        }
                        break;

                }
                $indexid++;
            }
            $params['chxl'] = implode('|', $valueBuffer);
            if (isset($params['chxlexpend'])) {
                if ($params['chxlexpend'] == 'currency') {
                    $params['chxl'] .= '|2:|||('
                        . $this->_localeCurrency->getCurrency(
                            $this->_storeManager->getStore()->getCurrentCurrencyCode()
                        )->getSymbol()
                        . ')';
                } else {
                    $params['chxl'] .= $params['chxlexpend'];
                }
            }
        };

        // chart size
        $params['chs'] = $this->_width . 'x' . $this->_height;

        if (isset($deltaX) && isset($deltaY)) {
            $params['chg'] = $deltaX . ',' . $deltaY . ',1,0';
        }
        // return the encoded data
        if ($directUrl) {
            $p = [];
            foreach ($params as $name => $value) {
                $p[] = $name . '=' . urlencode($value);
            }
            return self::API_URL . '?' . implode('&', $p);
        } else {
            $gaData = urlencode(base64_encode($this->serializer->serialize($params)));
            $gaHash = $this->_dashboardData->getChartDataHash($gaData);
            $params = ['ga' => $gaData, 'h' => $gaHash];
            return $this->getUrl('*/*/tunnel', ['_query' => $params]);
        }
    }

    /**
     * Prepare chart data
     *
     * @return void
     */
    protected function _prepareData()
    {
        $availablePeriods = array_keys($this->_dashboardData->getDatePeriods());
        $period = $this->getRequest()->getParam('period');

        $this->getDataHelper()->setParam('period', ($period && in_array($period, $availablePeriods)) ? $period : '7d');
    }

    /**
     * Get Dashboard Data Helper
     *
     * @return \Magento\Backend\Helper\Dashboard\Data
     */
    public function getDashboardData()
    {
        return $this->_dashboardData;
    }

    /**
     * Get Chart Data
     *
     * @param float $yrange
     * @param float $yorigin
     * @param string $dataDelimiter
     * @param string $dataSetdelimiter
     * @return array
     */
    public function getChartData($yrange, $yorigin, $dataDelimiter, $dataSetdelimiter)
    {
        $chartdata = [];
        $dataMissing = "";
        foreach ($this->getAllSeries() as $serie) {
            $thisdataarray = $serie;
            $thisdataarrayLength = count($thisdataarray);
            if ($this->_encoding == "s") {
                // SIMPLE ENCODING
                for ($j = 0; $j < $thisdataarrayLength; $j++) {
                    $currentvalue = $thisdataarray[$j];
                    if (is_numeric($currentvalue)) {
                        $ylocation = round((strlen($this->_simpleEncoding) - 1) * ($yorigin + $currentvalue) / $yrange);
                        array_push($chartdata, substr($this->_simpleEncoding, $ylocation, 1) . $dataDelimiter);
                    } else {
                        array_push($chartdata, $dataMissing . $dataDelimiter);
                    }
                }
                // END SIMPLE ENCODING
            } else {
                // EXTENDED ENCODING
                for ($j = 0; $j < $thisdataarrayLength; $j++) {
                    $currentvalue = $thisdataarray[$j];
                    if (is_numeric($currentvalue)) {
                        if ($yrange) {
                            $ylocation = (4095 * ($yorigin + $currentvalue) / $yrange);
                        } else {
                            $ylocation = 0;
                        }
                        $firstchar = floor($ylocation / 64);
                        $secondchar = $ylocation % 64;
                        $mappedchar = substr($this->_extendedEncoding, $firstchar, 1)
                            . substr($this->_extendedEncoding, $secondchar, 1);
                        array_push($chartdata, $mappedchar . $dataDelimiter);
                    } else {
                        array_push($chartdata, $dataMissing . $dataDelimiter);
                    }
                }
                // ============= END EXTENDED ENCODING =============
            }
            array_push($chartdata, $dataSetdelimiter);
        }
        return $chartdata;
    }
}
