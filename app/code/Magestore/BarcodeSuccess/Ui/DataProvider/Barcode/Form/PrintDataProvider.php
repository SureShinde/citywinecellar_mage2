<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Ui\DataProvider\Barcode\Form;

use Magestore\BarcodeSuccess\Ui\DataProvider\Barcode\DataProvider as ParentBarcodeDataProvider;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\ReportingInterface;

/**
 * Class PrintDataProvider
 *
 * Used to create print data provider
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PrintDataProvider extends ParentBarcodeDataProvider
{
    /**
     * @var
     */
    protected $searchCriteria;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var ReportingInterface
     */
    protected $reporting;

    /**
     * PrintDataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magestore\BarcodeSuccess\Helper\Data $helper
     * @param \Magestore\BarcodeSuccess\Model\Locator\LocatorInterface $locator
     * @param \Magestore\BarcodeSuccess\Model\ResourceModel\Barcode\CollectionFactory $collectionFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\Api\Search\ReportingInterface $reporting,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magestore\BarcodeSuccess\Helper\Data $helper,
        \Magestore\BarcodeSuccess\Model\Locator\LocatorInterface $locator,
        \Magestore\BarcodeSuccess\Model\ResourceModel\Barcode\CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $urlBuilder,
            $helper,
            $locator,
            $collectionFactory,
            $productFactory,
            $imageHelper,
            $stockRegistry,
            $meta,
            $data
        );

        $this->reporting = $reporting;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;

        $printIds = $this->locator->get('current_barcode_ids_to_print');
        if (!empty($printIds)) {
            $this->collection->addFieldToFilter('id', ['in' => $printIds]);
        }
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $data = parent::getData();
        $items = $this->locator->get('print_inline_edit_qty');
        if (!empty($items) && isset($data['items'])) {
            foreach ($data['items'] as $key => $item) {
                if (isset($items[$item['id']]['qty'])) {
                    $data['items'][$key]['qty'] = $items[$item['id']]['qty'];
                }
            }
        }
        return $data;
    }

    /**
     * Get search criteria
     *
     * @return \Magento\Framework\Api\Search\SearchCriteria
     */
    public function getSearchCriteria()
    {
        if (!$this->searchCriteria) {
            $this->searchCriteria = $this->searchCriteriaBuilder->create();
            $this->searchCriteria->setRequestName($this->name);
        }
        return $this->searchCriteria;
    }

    /**
     * @inheritdoc
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if ($filter->getField() == 'qty') {
            $filter->setConditionType('eq');
            $filter->setValue(str_replace('%', '', $filter->getValue()));
        }
        return parent::addFilter($filter);
    }
}
