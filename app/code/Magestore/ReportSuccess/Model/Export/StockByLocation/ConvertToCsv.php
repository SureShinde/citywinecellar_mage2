<?php

/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Model\Export\StockByLocation;

use Magento\Framework\Filesystem;
use Magento\Framework\Json\DecoderInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Ui\Api\BookmarkManagementInterface;
use Magestore\ReportSuccess\Model\Source\Adminhtml\StockByLocation\Metric;

/**
 * Class ConvertToCsv
 * @package Magestore\ReportSuccess\Model\Export\StockByLocation
 */
class ConvertToCsv extends \Magestore\ReportSuccess\Model\Export\AbstractConvertToCsv
{
    /**
     * @var string
     */
    protected $reportTitle = 'STOCK BY WAREHOUSE REPORT';
    /**
     * @var array
     */
    protected $totalData = [];
    /**
     * @var array
     */
    protected $needExplodeItem = [];
    /**
     * @var \Magestore\ReportSuccess\Model\Source\Adminhtml\StockByLocation\Metric
     */
    protected $stockByLocationSource;
    /**
     * @var \Magestore\ReportSuccess\Model\Bookmark
     */
    protected $bookmark;

    /**
     * @var array
     */
    protected $fixField = ['sku', 'barcode', 'name'];

    /**
     * @var array
     */
    protected $decimalField = [
        Metric::QTY_ON_HAND,
        Metric::AVAILABLE_QTY,
        Metric::QTY_TO_SHIP
    ];
    /**
     * @var array
     */
    protected $priceField = [
        Metric::INVENTORY_VALUE,
        Metric::POTENTIAL_REVENUE,
        Metric::PROFIT_VALUE,
    ];

    /**
     * ConvertToCsv constructor.
     * @param Filesystem $filesystem
     * @param Filter $filter
     * @param \Magestore\ReportSuccess\Model\Export\MetadataProvider $metadataProvider
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param \Magestore\ReportSuccess\Model\Source\Adminhtml\Supplier $supplier
     * @param \Magestore\ReportSuccess\Model\Source\Adminhtml\PurchaseOrder $purchaseOrder
     * @param BookmarkManagementInterface $bookmarkManagement
     * @param \Magento\Framework\App\RequestInterface $request
     * @param DecoderInterface $jsonDecoder
     * @param \Magento\Ui\Model\ResourceModel\Bookmark\Collection $bookmarkCollection
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magestore\ReportSuccess\Model\Source\Adminhtml\Warehouse $warehouse
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magestore\ReportSuccess\Model\Bookmark $bookmark
     * @param \Magestore\ReportSuccess\Model\Source\Adminhtml\StockByLocation\Metric $stockByLocationSource
     * @param int $pageSize
     */
    public function __construct(
        Filesystem $filesystem,
        Filter $filter,
        \Magestore\ReportSuccess\Model\Export\MetadataProvider $metadataProvider,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magestore\ReportSuccess\Model\Source\Adminhtml\Supplier $supplier,
        \Magestore\ReportSuccess\Model\Source\Adminhtml\PurchaseOrder $purchaseOrder,
        BookmarkManagementInterface $bookmarkManagement,
        \Magento\Framework\App\RequestInterface $request,
        DecoderInterface $jsonDecoder,
        \Magento\Ui\Model\ResourceModel\Bookmark\Collection $bookmarkCollection,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\ReportSuccess\Model\Source\Adminhtml\Warehouse $warehouse,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magestore\ReportSuccess\Model\Bookmark $bookmark,
        \Magestore\ReportSuccess\Model\Source\Adminhtml\StockByLocation\Metric $stockByLocationSource,
        $pageSize = 200
    )
    {
        $this->stockByLocationSource = $stockByLocationSource;
        $stockOption = $this->stockByLocationSource->toOptionListArray();
        $metricTitle = $stockOption[$bookmark->getMetric()];
        $this->bookmark = $bookmark;
        $this->reportTitle = strtoupper($metricTitle) . ' BY WAREHOUSE REPORT';
        parent::__construct($filesystem, $filter, $metadataProvider, $categoryCollectionFactory, $pricingHelper, $supplier, $purchaseOrder, $bookmarkManagement, $request, $jsonDecoder, $bookmarkCollection, $priceCurrency, $currency, $storeManager, $scopeConfig, $localeDate, $objectManager, $warehouse, $moduleManager, $pageSize);
    }

    /**
     * Abstract function
     * Initialize options
     */
    public function setOptions()
    {
        $options = $this->metadataProvider->getOptions();
        $this->options = $options;
    }

    /**
     * @param $component
     * @return array
     */
    public function getFields($component)
    {
        $fields = $this->metadataProvider->getFields($component);
        $currentFields = $this->getFilterColumn();
        $isEnableBarcode = $this->scopeConfig->getValue('reportsuccess/general/enable_barcode_in_report', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $barcode = $this->scopeConfig->getValue('reportsuccess/general/barcode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!$isEnableBarcode) {
            foreach ($currentFields as $key => $value) {
                if ($value == 'barcode') {
                    unset($currentFields[$key]);
                }
            }
        } else {
            // remove barcode column if barcode attribute is SKU
            if ($barcode == 'sku') {
                $keyBarcode = -1;
                foreach ($currentFields as $key => $value) {
                    if ($value == 'barcode') {
                        $keyBarcode = $key;
                    }
                }
                if ($keyBarcode != -1) {
                    unset($currentFields[$keyBarcode]);
                }
            }
        }
        return count($currentFields) ? $currentFields : $fields;
    }


    /**
     * @return array
     */
    public function getFilterColumn()
    {
        $config = $this->getCurrentBookmark()->getConfig();
        $columns = $config['current']['columns'];

        if (isset($config['current']['positions'])) {
            $positions = $config['current']['positions'];
            foreach ($columns as $key => $value) {
                if (!$value['visible']) {
                    unset($positions[$key]);
                }
            }
            return array_keys($positions);
        } else {
            $array = [];
            foreach ($columns as $key => $value) {
                if ($value['visible'] && in_array($key, array('sku', 'name', 'barcode'))) {
                    $array[] = $key;
                }
            }
            return $array;
        }

    }

    /**
     * @param $data
     * @return $this|void
     */
    public function roundedDecimal(&$data)
    {
        $metric = $this->bookmark->getMetric();
        if (!in_array($metric, $this->decimalField)) {
            return $this;
        }
        foreach ($data as $key => $value) {
            if (isset($data[$key]) && !in_array($key, $this->fixField)) {
                $data[$key] = $this->currency->format(round($data[$key], 2), ['display' => \Zend_Currency::NO_SYMBOL], false);
            }
        }
    }

    /**
     * @param $data
     * @return $this|void
     */
    public function formatPrice(&$data)
    {
        $metric = $this->bookmark->getMetric();
        $baseCurrencyCode = $this->_storeManager->getStore()->getBaseCurrencyCode();
        if (!in_array($metric, $this->priceField)) {
            return $this;
        }
        foreach ($data as $key => $value) {
            if (isset($data[$key]) && !in_array($key, $this->fixField)) {
                $data[$key] = $this->priceCurrency->format($data[$key], false, 2, null, $baseCurrencyCode);
            }
        }
    }
}
