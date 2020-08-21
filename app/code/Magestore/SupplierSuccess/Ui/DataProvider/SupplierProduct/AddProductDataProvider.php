<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Ui\DataProvider\SupplierProduct;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Api\AttributeValue;
use Magento\Framework\Api\CustomAttributesDataInterface;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product\Collection as SupplierProductCollection;

/**
 * Class AddProductDataProvider
 *
 * Supplier product data provider
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AddProductDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /** @var mixed */
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

    protected $searchCriteria;
    protected $searchCriteriaBuilder;
    protected $reporting;

    /**
     * AddProductDataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $requestInterface
     * @param \Magestore\SupplierSuccess\Service\Supplier\ProductService $supplierProductService
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CollectionFactory $collectionFactory,
        RequestInterface $requestInterface,
        \Magestore\SupplierSuccess\Service\Supplier\ProductService $supplierProductService,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->reporting = $reporting;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
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

    /**
     * Get modify collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getModifyCollection()
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addAttributeToSelect('name');
        $supplierId = $this->requestInterface->getParam('supplier_id', null);
        $supplierProductIds = 0;
        if ($supplierId) {
            /** @var SupplierProductCollection $supplierProductCollection */
            $supplierProductCollection = $this->supplierProductService->getProductsBySupplierId($supplierId);
            if ($supplierProductCollection->getSize()) {
                $supplierProductIds = $supplierProductCollection->getColumnValues('product_id');
            }
        }
        return $collection->addAttributeToFilter('entity_id', ['nin' => $supplierProductIds]);
    }

    /**
     * Get Search Criteria
     *
     * @return \Magento\Framework\Api\Search\SearchCriteria|null
     */
    public function getSearchCriteria()
    {
        if (($this->requestInterface->getActionName() == 'gridToCsv')
            || ($this->requestInterface->getActionName() == 'gridToXml')) {
            if (!$this->searchCriteria) {
                $this->searchCriteria = $this->searchCriteriaBuilder->create();
                $this->searchCriteria->setRequestName($this->name);
            }
            return $this->searchCriteria;
        }
        return parent::getSearchCriteria();
    }

    /**
     * Modify Collection To Export
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|mixed
     */
    public function modifiCollectionToExport()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $exportSession = $objectManager->get(\Magento\Newsletter\Model\Session::class);
        $export_page = $exportSession->getExportPage();
        $collection = clone($this->collection);
        $total_page = ceil($collection->getSize() / 200);
        if ((int)$export_page && (int)$export_page > 0) {
            $collection->setPageSize(200);
            $collection->setCurPage($export_page);
            $exportSession->setExportPage((int)$export_page + 1);
        } else {
            $collection->setPageSize(200);
            $collection->setCurPage(1);
            $exportSession->setExportPage(2);
        }
        if ((int)$exportSession->getExportPage() > (int)$total_page) {
            $exportSession->unsExportPage();
        }
        return $collection;
    }

    /**
     * Get Search Result
     *
     * @return \Magento\Framework\Api\Search\SearchResultInterface
     */
    public function getSearchResult()
    {
        if (($this->requestInterface->getActionName() == 'gridToCsv')
            || ($this->requestInterface->getActionName() == 'gridToXml')) {
            $collection = $this->modifiCollectionToExport();
            $count = $collection->getSize();
            /** @var \Magento\Framework\Api\Search\Document[] $documents */
            $documents = [];
            foreach ($collection as $value) {
                $data = [];
                $data['ids'] = $value->getEntityId();
                $data['entity_id'] = $value->getEntityId();
                $data['sku'] = $value->getSku();
                $data['name'] = $value->getName();
                $documents[] = $this->create($data);
            }
            $obj = new \Magento\Framework\DataObject();
            $obj->setItems($documents);
            $obj->setTotalCount($count);
            return $obj;
        }
        return parent::getSearchResult();
    }

    /**
     * Create Search Document instance
     *
     * @param mixed $rawDocument
     * @return \Magento\Framework\Api\Search\Document
     */
    public function create($rawDocument)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        /** @var \Magento\Framework\Search\EntityMetadata $entityMetadata */
        $entityMetadata = $objectManager->create(
            \Magento\Framework\Search\EntityMetadata::class,
            ['entityId' => 'id']
        );
        $documentId = null;
        $entityId = $entityMetadata->getEntityId();
        $attributes = [];
        foreach ($rawDocument as $fieldName => $value) {
            if ($fieldName === $entityId) {
                $documentId = $value;
            } else {
                $attributes[$fieldName] = new AttributeValue(
                    [
                        AttributeInterface::ATTRIBUTE_CODE => $fieldName,
                        AttributeInterface::VALUE => $value,
                    ]
                );
            }
        }

        return new Document(
            [
                DocumentInterface::ID => $documentId,
                CustomAttributesDataInterface::CUSTOM_ATTRIBUTES => $attributes,
            ]
        );
    }
}
