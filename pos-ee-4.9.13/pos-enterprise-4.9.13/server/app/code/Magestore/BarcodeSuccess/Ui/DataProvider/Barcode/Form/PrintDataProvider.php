<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Ui\DataProvider\Barcode\Form;

use Magestore\BarcodeSuccess\Ui\DataProvider\Barcode\DataProvider as ParentBarcodeDataProvider;

/* emlement export feature */
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\ReportingInterface;



/**
 * Class BarcodeDataProvider
 * @package Magestore\BarcodeSuccess\Ui\DataProvider\History
 */
class PrintDataProvider extends ParentBarcodeDataProvider
{

    /* emlement export feature */
    protected $searchCriteria;
    protected $searchCriteriaBuilder;
    protected $reporting;


    /**
     * PrintDataProvider constructor.
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

        /* emlement export feature */
        $this->reporting = $reporting;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;


        $printIds = $this->locator->get('current_barcode_ids_to_print');
        if(!empty($printIds)){
            $this->collection->addFieldToFilter('id',['in' => $printIds]);
        }
    }

    public function getData()
    {
        $data = parent::getData();
        $items = $this->locator->get('print_inline_edit_qty');
        if(!empty($items) && isset($data['items'])){
            foreach ($data['items'] as $key => $item){
                if(isset($items[$item['id']]['qty'])){
                    $data['items'][$key]['qty'] = $items[$item['id']]['qty'];
                }
            }
        }
        return $data;
    }

    /* emlement export feature */
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
        if($filter->getField() == 'qty') {
            $filter->setConditionType('eq');
            $filter->setValue(str_replace('%','',$filter->getValue()));
        }
        parent::addFilter($filter);
    }
    public function getSearchResult()
    {
        $collection = $this->collection;//->getData();
        $count = $collection->getSize();
        $collection->setPageSize($collection->getSize()); // limit dung để query view dữ liệu - ko dùng cho export được
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        /** @var \Magento\Framework\Search\EntityMetadata $entityMetadata */
        $entityMetadata = $objectManager->create('Magento\Framework\Search\EntityMetadata', ['entityId' => 'ids']);
        $idKey = $entityMetadata->getEntityId();
        /** @var \Magento\Framework\Search\Adapter\Mysql\DocumentFactory $documentFactory */
        $documentFactory = $objectManager->create(
            'Magento\Framework\Search\Adapter\Mysql\DocumentFactory',
            ['entityMetadata' => $entityMetadata]
        );
        /** @var \Magento\Framework\Api\Search\Document[] $documents */
        $documents = [];
        foreach($collection as $value){
            $data = array();
            $data['id'] = $value->getId();
            $data['ids'] = $value->getId();
            $data['barcode'] = $value->getBarcode();
            $data['qty'] = $value->getQty();
            $data['product_sku'] = $value->getProductSku();
            $data['supplier_code'] = $value->getSupplierCode();
            $data['purchased_time'] = $value->getPurchasedTime();
            $documents[] = $documentFactory->create($data);
        }
        $obj = new \Magento\Framework\DataObject();
        $obj->setItems($documents);
        $obj->setTotalCount($count);
        return $obj;
    }
}
