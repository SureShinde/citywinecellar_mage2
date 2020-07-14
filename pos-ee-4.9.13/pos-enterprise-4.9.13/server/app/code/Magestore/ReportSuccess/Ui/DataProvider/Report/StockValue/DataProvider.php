<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Ui\DataProvider\Report\StockValue;

use Magestore\ReportSuccess\Model\ResourceModel\Product\CollectionFactory;

/**
 * Class DataProvider
 * @package Magestore\ReportSuccess\Ui\DataProvider\Report\StockValue
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
     * @var \Magento\Directory\Model\Currency
     */
    protected $currency;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var array
     */
    protected $addedField;

    protected $decimalField = ['qty_on_hand'];

    /**
     * ProductDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Directory\Model\Currency $currency
     * @param \Magento\Framework\App\RequestInterface $request
     * @param array $addFieldStrategies
     * @param array $addFilterStrategies
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Framework\App\RequestInterface $request,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    )
    {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->currency = $currency;
        $this->request = $request;
        $this->addFieldStrategies = $addFieldStrategies;
        $this->addFilterStrategies = $addFilterStrategies;
    }


    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $isExport = $this->request->getParam('is_export');
        // clone collection for get data provider without page size and current page
        /** @var \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection */
        $collection = clone $this->getCollection();
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
            $this->getCollection()->addCategoryIds();
        }
        $items = $this->getCollection()->toArray();
        $itemsAll = $collection->setPageSize(null)->setCurPage(null)->load()->addCategoryIds()->toArray();
        $this->roundedDecimal($items);

        if($isExport) {
            return [
                'totalRecords' => $this->getCollection()->getSize(),
                'items' => array_values($items),
                'allItems' => array_values($itemsAll),
                'totals' => $this->getDataTotal($itemsAll)
            ];
        } else {
            return [
                'totalRecords' => $this->getCollection()->getSize(),
                'items' => array_values($items),
                'totals' => $this->getDataTotal($itemsAll)
            ];
        }
    }

    /**
     * Add field to select
     *
     * @param string|array $field
     * @param string|null $alias
     * @return void
     */
    public function addField($field, $alias = null)
    {
        if (isset($this->addFieldStrategies[$field])) {
            $this->addFieldStrategies[$field]->addField($this->getCollection(), $field, $alias);
        } else {
            parent::addField($field, $alias);
        }
    }



    /**
     * {@inheritdoc}
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        $this->addedField[] = $filter->getField();
        if ($filter->getField() == "category_ids") {
            $this->getCollection()->addCategoriesFilter(array("in" => $filter->getValue()));
        } else if ($filter->getField() == "warehouse") {
            $this->getCollection()->addWarehouseToFilter($filter->getValue());
        } else if ($filter->getField() == "supplier" && $filter->getValue()) {
            $this->getCollection()->addSupplierToFilter($filter->getValue());
        } else if ($filter->getField() == "barcode" && $filter->getValue()) {
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

    public function roundedDecimal(&$data) {
        foreach ($data as &$datum) {
            foreach ($this->decimalField as $field) {
                if(isset($datum[$field])) {
                    $datum[$field] = $this->currency->format(round($datum[$field], 2), ['display'=>\Zend_Currency::NO_SYMBOL], false);
                }
            }
        }
    }
    public function getDataTotal($items) {
        $total = [
            'qty_on_hand' => 0,
            'stock_value' => 0,
            'potential_revenue' => 0,
            'potential_profit' => 0
        ];
        foreach ($items as $item) {
            foreach ($total as $key => &$value) {
                if(isset($item[$key])) {
                    $value += (float)$item[$key];
                }
            }
        }

        return $total;
    }
}
