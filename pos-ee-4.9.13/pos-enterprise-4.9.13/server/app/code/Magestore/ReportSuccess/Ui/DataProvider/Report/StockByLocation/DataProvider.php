<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Ui\DataProvider\Report\StockByLocation;

use Magestore\ReportSuccess\Model\ResourceModel\Report\StockByLocation\CollectionFactory;
use Magestore\ReportSuccess\Model\Source\Adminhtml\StockByLocation\Metric;

/**
 * Class DataProvider
 * @package Magestore\ReportSuccess\Ui\DataProvider\Report\StockByLocation
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * Product collection
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $collection;

    /**
     * @var \Magento\Ui\DataProvider\AddFieldToCollectionInterface[]
     */
    protected $addFieldStrategies;

    /**
     * @var \Magento\Ui\DataProvider\AddFilterToCollectionInterface[]
     */
    protected $addFilterStrategies;

    /**
     * @var array
     */
    protected $addedField;

    /**
     * @var \Magestore\ReportSuccess\Model\Bookmark
     */
    protected $bookmark;

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $currency;
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;
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
     * @var array
     */
    protected $fixField = ['sku', 'barcode', 'name'];

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Construct
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Ui\DataProvider\AddFieldToCollectionInterface[] $addFieldStrategies
     * @param \Magento\Ui\DataProvider\AddFilterToCollectionInterface[] $addFilterStrategies
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        \Magestore\ReportSuccess\Model\Bookmark $bookmark,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    )
    {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->bookmark = $bookmark;
        $this->addFieldStrategies = $addFieldStrategies;
        $this->addFilterStrategies = $addFilterStrategies;
        $this->currency = $currency;
        $this->request = $request;
        $this->priceCurrency = $priceCurrency;
        $this->storeManager = $storeManager;
        // Prepare collection
        $this->prepareCollection();
    }

    /**
     * Prepare collection by locations and metrics
     */
    protected function prepareCollection()
    {
        /** @var \Magestore\ReportSuccess\Model\ResourceModel\Report\StockByLocation\Collection $collection */
        $collection = $this->getCollection();
        $collection->joinLocationsData($this->bookmark->getLocations(), $this->bookmark->getMetric());
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getData()
    {
        $isExport = $this->request->getParam('is_export');
        if ($isExport) {
            $collection = clone $this->getCollection();
        }
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }
        $items = $this->getCollection()->toArray();
        $this->roundedDecimal($items);
        $this->formatPrice($items);

        if($isExport) {
            $itemsAll = $collection->setPageSize(null)->setCurPage(null)->load()->toArray();
            $this->roundedDecimal($itemsAll);
            $this->formatPrice($itemsAll);
            return [
                'totalRecords' => $this->getCollection()->getSize(),
                'items' => array_values($items),
                'allItems' => array_values($itemsAll),
                'location' => $this->bookmark->getLocations(),
                'metric' => $this->bookmark->getMetric(),
            ];
        } else {
            return [
                'totalRecords' => $this->getCollection()->getSize(),
                'items' => array_values($items),
                'location' => $this->bookmark->getLocations(),
                'metric' => $this->bookmark->getMetric(),
            ];
        }
    }
    /**
     * {@inheritdoc}
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        $this->addedField[] = $filter->getField();
        if ($filter->getField() == "barcode" && $filter->getValue()) {
            $this->getCollection()->addBarcodeToFilter($filter->getValue());
        } else {
            if (isset($this->addFilterStrategies[$filter->getField()])) {
                $this->addFilterStrategies[$filter->getField()]
                    ->addFilter(
                        $this->getCollection(),
                        $filter->getField(),
                        [$filter->getConditionType() => $filter->getValue()]
                    );
            } else {
                parent::addFilter($filter);
            }
        }
    }

    /**
     * @param $data
     * @return $this
     */
    public function roundedDecimal(&$data) {
        $metric = $this->bookmark->getMetric();
        if (!in_array($metric, $this->decimalField)){
            return $this;
        }
        foreach ($data as &$datum) {
            foreach ($datum as $key => $value){
                if (isset($datum[$key]) && !in_array($key, $this->fixField)){
                    $datum[$key] = $this->currency->format(round($datum[$key], 2), ['display'=>\Zend_Currency::NO_SYMBOL], false);
                }
            }
        }
    }

    /**
     * @param $data
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function formatPrice(&$data){
        $metric = $this->bookmark->getMetric();
        $baseCurrencyCode = $this->storeManager->getStore()->getBaseCurrencyCode();
        if (!in_array($metric, $this->priceField)){
            return $this;
        }
        foreach ($data as &$datum) {
            foreach ($datum as $key => $value){
                if (isset($datum[$key]) && !in_array($key, $this->fixField)){
                    $datum[$key] = $this->priceCurrency->format($datum[$key], false, 2, null, $baseCurrencyCode);
                }
            }
        }
    }
}
