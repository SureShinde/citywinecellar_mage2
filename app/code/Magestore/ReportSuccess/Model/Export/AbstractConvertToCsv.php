<?php

/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Model\Export;

use Magento\Framework\Filesystem;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Ui\Api\BookmarkManagementInterface;
use Magento\Framework\Json\DecoderInterface;

/**
 * Class \Magestore\ReportSuccess\Model\Export\AbstractConvertToCsv
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AbstractConvertToCsv extends \Magento\Ui\Model\Export\ConvertToCsv
{
    /**
     *
     */
    protected $addCategoryInCollection = false;

    const PAGE_SIZE = 200;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricingHelper;

    /**
     * @var \Magestore\ReportSuccess\Model\Source\Adminhtml\Supplier
     */
    protected $supplier;

    /**
     * @var \Magestore\ReportSuccess\Model\Source\Adminhtml\PurchaseOrder
     */
    protected $purchaseOrder;

    /**
     * @var BookmarkManagementInterface
     */
    protected $bookmarkManagement;

    /**
     * Request instance
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var DecoderInterface
     */
    protected $jsonDecoder;

    /**
     * @var \Magento\Ui\Api\Data\BookmarkInterface
     */
    protected $_currentBookmark;

    /**
     * @var \Magento\Ui\Model\ResourceModel\Bookmark\Collection
     */
    protected $bookmarkCollection;

    /**
     * @var \Magestore\ReportSuccess\Model\Export\MetadataProvider
     */
    protected $metadataProvider;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magestore\ReportSuccess\Model\Source\Adminhtml\Warehouse
     */
    protected $warehouse;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * List options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Title of file
     *
     * @var string
     */
    protected $reportTitle = '';

    /**
     * Array total data
     *
     * @var array
     */
    protected $totalData = [];

    /**
     * Array price total data
     *
     * @var array
     */
    protected $priceTotal = [];

    /**
     * Field need explode before write to csv
     */
    protected $needExplodeItem = [];

    /**
     * @var array
     */
    protected $decimalField = [];

    /**
     * @var array
     */
    protected $priceField = [];

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $currency;

    /**
     * AbstractConvertToCsv constructor.
     *
     * @param Filesystem $filesystem
     * @param Filter $filter
     * @param MetadataProvider $metadataProvider
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param \Magestore\ReportSuccess\Model\Source\Adminhtml\Supplier $supplier
     * @param \Magestore\ReportSuccess\Model\Source\Adminhtml\PurchaseOrder $purchaseOrder
     * @param BookmarkManagementInterface $bookmarkManagement
     * @param \Magento\Framework\App\RequestInterface $request
     * @param DecoderInterface $jsonDecoder
     * @param \Magento\Ui\Model\ResourceModel\Bookmark\Collection $bookmarkCollection
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Directory\Model\Currency $currency
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magestore\ReportSuccess\Model\Source\Adminhtml\Warehouse $warehouse
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param int $pageSize
     * @throws \Magento\Framework\Exception\FileSystemException
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Filesystem $filesystem,
        Filter $filter,
        \Magestore\ReportSuccess\Model\Export\MetadataProvider $metadataProvider,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magestore\ReportSuccess\Model\Source\Adminhtml\Supplier $supplier,
        \Magestore\ReportSuccess\Model\Source\Adminhtml\PurchaseOrder $purchaseOrder,
        \Magento\Ui\Api\BookmarkManagementInterface $bookmarkManagement,
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
        $pageSize = 200
    ) {
        parent::__construct($filesystem, $filter, $metadataProvider, $pageSize);
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->pricingHelper = $pricingHelper;
        $this->supplier = $supplier;
        $this->purchaseOrder = $purchaseOrder;
        $this->bookmarkManagement = $bookmarkManagement;
        $this->_request = $request;
        $this->jsonDecoder = $jsonDecoder;
        $this->bookmarkCollection = $bookmarkCollection;
        $this->priceCurrency = $priceCurrency;
        $this->_storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->localeDate = $localeDate;
        $this->objectManager = $objectManager;
        $this->warehouse = $warehouse;
        $this->moduleManager = $moduleManager;
        $this->currency = $currency;
        // initialize options
        $this->setOptions();
    }

    /**
     * Get header of component
     *
     * @param UiComponentInterface $component
     * @return array
     */
    public function getHeaders($component)
    {
        $fields = $this->getFields($component);
        $columnsData = $this->metadataProvider->getColumnsData($component);
        $array = [];
        foreach ($fields as $field) {
            if (isset($columnsData[$field])) {
                $array[] = $columnsData[$field];
            }
        }
        return $array;
    }

    /**
     * Get fields
     *
     * @param UiComponentInterface $component
     * @return array
     */
    public function getFields($component)
    {
        $fields = $this->metadataProvider->getFields($component);
        $currentFields = $this->getFilterColumn();
        $isEnableBarcode = $this->scopeConfig->getValue(
            'reportsuccess/general/enable_barcode_in_report',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $barcode = $this->scopeConfig->getValue(
            'reportsuccess/general/barcode',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
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
     * Get filter column
     *
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
                if ($value['visible']) {
                    $array[] = $key;
                }
            }
            return $array;
        }
    }

    /**
     * Get csv file
     *
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCsvFile()
    {
        $component = $this->filter->getComponent();

        $name = sha1(microtime());
        $file = 'export/' . $component->getName() . $name . '.csv';

        $this->filter->prepareComponent($component);
        $this->filter->applySelectionOnTargetProvider();
        /** @var \Magento\Ui\DataProvider\AbstractDataProvider $dataProvider */
        $dataProvider = $component->getContext()->getDataProvider();
        $fields = $this->getFields($component);

        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();
        $stream->writeCsv([__($this->reportTitle)]);
        $stream->writeCsv(
            [
                $this->localeDate->formatDate(null, \IntlDateFormatter::SHORT, true)
            ]
        );
        $stream->writeCsv([]);
        $stream->writeCsv($this->getHeaders($component));
        $pageSize = self::PAGE_SIZE;
        $page = 1;
        $collection = $dataProvider->getCollection();
        $totalCount = (int)$collection->getSize();
        $this->addSortOrderToCollection($collection);
        while ($totalCount > 0) {
            $collectionPageSize = clone $collection;
            $collectionPageSize->setPageSize($pageSize);
            $collectionPageSize->setCurPage($page);
            $collectionPageSize->load();
            if ($this->addCategoryInCollection) {
                $collectionPageSize->addCategoryIds();
            }
            $items = $collectionPageSize->toArray();
            foreach ($items as $item) {
                foreach ($this->needExplodeItem as $f_item) {
                    if (isset($item[$f_item])) {
                        $item[$f_item] = explode(',', $item[$f_item]);
                    }
                }
                $this->increaseTotals($item);

                $this->roundedDecimal($item);
                $this->formatPrice($item);
                $this->metadataProvider->convertDate($item, $component->getName());
                $stream->writeCsv($this->getRowData($item, $fields, $this->options));
            }
            $page++;
            $totalCount = $totalCount - $pageSize;
        }
        $stream->writeCsv($this->getTotals($fields, $this->totalData));

        $stream->unlock();
        $stream->close();

        return [
            'type' => 'filename',
            'value' => $file,
            'rm' => true  // can delete file after use
        ];
    }

    /**
     * Returns row data
     *
     * @param array $document
     * @param array $fields
     * @param array $options
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * phpcs:disable Generic.Metrics.NestingLevel
     */
    public function getRowData($document, $fields, $options)
    {
        $row = [];
        $baseCurrencyCode = $this->_storeManager->getStore()->getBaseCurrencyCode();
        foreach ($fields as $column) {
            $isPrice = in_array($column, $this->priceTotal);
            if (isset($options[$column])) {
                if (isset($document[$column])) {
                    $key = $document[$column];
                    if (is_array($key)) {
                        $value = [];
                        foreach ($key as $childKey) {
                            if (isset($options[$column][$childKey])) {
                                $value[] = $options[$column][$childKey];
                            }
                        }
                        $row[] = implode(', ', $value);
                    } elseif (isset($options[$column][$key])) {
                        $row[] = $options[$column][$key];
                    } else {
                        $row[] = '';
                    }
                } else {
                    $row[] = '';
                }
            } else {
                if (isset($document[$column])) {
                    if ($isPrice) {
                        $row[] = $this->priceCurrency->format(
                            $document[$column],
                            false,
                            2,
                            null,
                            $baseCurrencyCode
                        );
                    } else {
                        $row[] = $document[$column];
                    }
                } else {
                    $row[] = '';
                }
            }
        }
        return $row;
    }

    /**
     * Get total of fields
     *
     * @param array $fields
     * @param array $data
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getTotals($fields, $data)
    {
        if (!count(array_intersect(array_values($this->getFilterColumn()), array_keys($this->totalData)))) {
            return [];
        }
        $totals = [];
        $baseCurrencyCode = $this->_storeManager->getStore()->getBaseCurrencyCode();
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                if (in_array($field, $this->priceTotal)) {
                    $totals[] = $this->priceCurrency->format(
                        $data[$field],
                        false,
                        2,
                        null,
                        $baseCurrencyCode
                    );
                } else {
                    $totals[] = $data[$field];
                }
            } else {
                $totals[] = '';
            }
        }
        if ($totals[0] == '') {
            $totals[0] = __('Totals');
        }

        return $totals;
    }

    /**
     * Add sorted order to array
     *
     * @param array $array
     * @return mixed
     */
    public function addSortOrderToArray($array)
    {
        $config = $this->getCurrentBookmark()->getConfig();
        foreach ($config['current']['columns'] as $key => $value) {
            if ($value['sorting']) {
                if ($key == 'barcode') {
                    $barcode = $this->scopeConfig->getValue(
                        'reportsuccess/general/barcode',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    );
                    if ($barcode == 'sku') {
                        return $array;
                    }
                }
                if ($key == 'ids') {
                    return $array;
                }
                usort($array, $this->buildSorter($key));
                return $array;
            }
        }
        return $array;
    }

    /**
     * Add sort order to collection
     *
     * @param array $collection
     * @return mixed
     */
    public function addSortOrderToCollection($collection)
    {
        $config = $this->getCurrentBookmark()->getConfig();
        foreach ($config['current']['columns'] as $key => $value) {
            if ($value['sorting']) {
                if ($key == 'barcode') {
                    $barcode = $this->scopeConfig->getValue(
                        'reportsuccess/general/barcode',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    );
                    if ($barcode == 'sku') {
                        return $collection;
                    }
                }
                if ($key == 'ids') {
                    return $collection;
                }

                $collection->setOrder($key, $value['sorting']);
                return $collection;
            }
        }
        return $collection;
    }

    /**
     * Sort array
     *
     * @param int $key
     * @return \Closure
     */
    public function buildSorter($key)
    {
        return function ($a, $b) use ($key) {
            if (isset($a[$key]) && isset($b[$key])) {
                return strnatcmp($a[$key], $b[$key]);
            }
            return null;
        };
    }

    /**
     * Get current bookmark
     *
     * @return \Magento\Framework\DataObject|\Magento\Ui\Api\Data\BookmarkInterface
     */
    public function getCurrentBookmark()
    {
        if (!$this->_currentBookmark) {
            $this->_currentBookmark = $this->bookmarkManagement->getByIdentifierNamespace(
                'current',
                $this->_request->getParam('namespace')
            );
        }
        return $this->_currentBookmark;
    }

    /**
     * Increase total
     *
     * @param array $item
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function increaseTotals($item)
    {
        foreach ($this->totalData as $key => $value) {
            if (isset($item[$key]) && $item[$key]) {
                $this->totalData[$key] += $item[$key];
            }
        }
    }

    /**
     * Abstract function
     *
     * Initialize options
     */
    public function setOptions()
    {
        // Implement in children class
        // phpcs:ignore
        return;
    }

    /**
     * Round decimal
     *
     * @param array $data
     */
    public function roundedDecimal(&$data)
    {
        foreach ($this->decimalField as $field) {
            if (isset($data[$field])) {
                $data[$field] = $this->currency->format(
                    round($data[$field], 2),
                    ['display' => \Zend_Currency::NO_SYMBOL],
                    false
                );
            }
        }
    }

    /**
     * Format Price for data
     *
     * @param array $data
     */
    public function formatPrice(&$data)
    {
        $baseCurrencyCode = $this->_storeManager->getStore()->getBaseCurrencyCode();
        foreach ($this->priceField as $field) {
            if (isset($data[$field])) {
                $data[$field] = $this->priceCurrency->format(
                    $data,
                    false,
                    2,
                    null,
                    $baseCurrencyCode
                );
            }
        }
    }
}
