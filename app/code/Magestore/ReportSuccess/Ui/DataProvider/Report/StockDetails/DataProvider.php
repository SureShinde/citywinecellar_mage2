<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Ui\DataProvider\Report\StockDetails;

use Magestore\ReportSuccess\Model\ResourceModel\Report\StockDetails\CollectionFactory;

/**
 * Class DataProvider
 * @package Magestore\ReportSuccess\Ui\DataProvider\Report\StockDetails
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

    protected $decimalField = ['qty_on_hand','available_qty','qty_to_ship','incoming_qty'];

    /**
     * DataProvider constructor.
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

    public function getData()
    {
        $isExport = $this->request->getParam('is_export');
        // clone collection for get data provider without page size and current page
        /** @var \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection */
        $collection = clone $this->getCollection();
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }
        $items = $this->getCollection()->toArray();
        $itemsAll = $collection->setPageSize(null)->setCurPage(null)->load()->toArray();
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
     * {@inheritdoc}
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        $this->addedField[] = $filter->getField();
        if ($filter->getField() == "supplier" && $filter->getValue()) {
            $this->getCollection()->addSupplierToFilter($filter->getValue());
        } else if ($filter->getField() == "warehouse" && $filter->getValue()) {
            $this->getCollection()->addWarehouseToFilter($filter->getValue());
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
            'available_qty' => 0,
            'qty_to_ship' => 0,
            'incoming_qty' => 0
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