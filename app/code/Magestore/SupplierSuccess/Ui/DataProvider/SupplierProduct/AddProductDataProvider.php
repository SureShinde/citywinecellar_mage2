<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Ui\DataProvider\SupplierProduct;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
//use Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product\CollectionFactory;
use Magento\Framework\App\RequestInterface;


/* Add by Kai - fix bug export on Modal */
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
/* End by Kai - fix bug export on Modal */


/**
 * Class ProductDataProvider
 */
class AddProductDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /** @var mixed  */
    protected $collection;

    /**
     * @var RequestInterface
     */
    protected $requestInterface;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magestore\SupplierSuccess\Service\Supplier\ProductService
     */
    protected $supplierProductService;


    /* Add by Kai - fix bug export on Modal */
    protected $searchCriteria;
    protected $searchCriteriaBuilder;
    protected $reporting;
    /* End by Kai - fix bug export on Modal */

    /**
     * SupplierDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,

        /* Add by Kai - fix bug export on Modal */
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        /* End by Kai - fix bug export on Modal */

        CollectionFactory $collectionFactory,
        RequestInterface $requestInterface,
        \Magestore\SupplierSuccess\Service\Supplier\ProductService $supplierProductService,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        /* Add by Kai - fix bug export on Modal */
        $this->reporting = $reporting;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        /* End by Kai - fix bug export on Modal */

        $this->requestInterface = $requestInterface;
        $this->collectionFactory = $collectionFactory;
        $this->supplierProductService = $supplierProductService;
        $this->collection = $this->getModifyCollection();
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }
        $items = $this->getCollection()->toArray();
        return [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => array_values($items),
        ];
    }

    public function getModifyCollection()
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addAttributeToSelect('name');
        $supplierId = $this->requestInterface->getParam('supplier_id', null);
        $supplierProductIds = 0;
        if ($supplierId) {
            /** @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product\Collection $supplierProductCollection */
            $supplierProductCollection = $this->supplierProductService->getProductsBySupplierId($supplierId);
            if ($supplierProductCollection->getSize())
                $supplierProductIds = $supplierProductCollection->getColumnValues('product_id');
        }
        return $collection->addAttributeToFilter('entity_id', ['nin' => $supplierProductIds]);
    }

    /* Add by Kai - fix bug export on Modal */
    public function getSearchCriteria()
    {
        if( ($this->requestInterface->getActionName() == 'gridToCsv') || ($this->requestInterface->getActionName() == 'gridToXml')) {
            if (!$this->searchCriteria) {
                $this->searchCriteria = $this->searchCriteriaBuilder->create();
                $this->searchCriteria->setRequestName($this->name);
            }
            return $this->searchCriteria;
        }
        return parent::getSearchCriteria();
    }

    public function modifiCollectionToExport(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $exportSession = $objectManager->get('\Magento\Newsletter\Model\Session');
        //$exportSession->unsExportPage();
        $export_page = $exportSession->getExportPage();
        $collection = clone($this->collection);
        $total_page = ceil($collection->getSize()/200);
        if((int)$export_page && (int)$export_page > 0){
            $collection->setPageSize(200);
            $collection->setCurPage($export_page);
            $exportSession->setExportPage((int)$export_page + 1);
        }else{
            $collection->setPageSize(200);
            $collection->setCurPage(1);
            $exportSession->setExportPage(2);
        }
        if( (int)$exportSession->getExportPage() > (int)$total_page ){
            $exportSession->unsExportPage();
        }
        return $collection;
    }
    public function getSearchResult()
    {
        if( ($this->requestInterface->getActionName() == 'gridToCsv') || ($this->requestInterface->getActionName() == 'gridToXml'))
        {
            //$collection = $this->collection;//->getData();
            $collection = $this->modifiCollectionToExport();
            $count = $collection->getSize();
            //$collection->setPageSize($collection->getSize()); // limit dung để query view dữ liệu - ko dùng cho export được
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
            /** @var \Magento\Framework\Search\EntityMetadata $entityMetadata */
            $entityMetadata = $objectManager->create('Magento\Framework\Search\EntityMetadata', ['entityId' => 'id']);
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
                $data['ids'] = $value->getEntityId();
                $data['entity_id'] = $value->getEntityId();
                $data['sku'] = $value->getSku();
                $data['name'] = $value->getName();
                $documents[] = $documentFactory->create($data);
            }
            $obj = new \Magento\Framework\DataObject();
            $obj->setItems($documents);
            $obj->setTotalCount($count);
            return $obj;
        }
        return parent::getSearchResult();
    }
}
