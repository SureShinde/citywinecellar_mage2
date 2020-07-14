<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Ui\DataProvider\Template;

use Magestore\BarcodeSuccess\Ui\DataProvider\AbstractProvider;

/**
 * Class DataProvider
 * @package Magestore\BarcodeSuccess\Ui\DataProvider
 */
class DataProvider extends AbstractProvider
{
    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var string
     */
    protected $type_provider;

    /**
     * @var string
     */
    protected $barcodeTemplate;

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Magento\Framework\Api\Search\ReportingInterface $reporting
     * @param \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magestore\BarcodeSuccess\Helper\Data $helper
     * @param \Magestore\BarcodeSuccess\Model\Locator\LocatorInterface $locator
     * @param \Magestore\BarcodeSuccess\Model\ResourceModel\Template\CollectionFactory $collectionFactory
     * @param \Magestore\BarcodeSuccess\Api\Data\BarcodeTemplateInterface $barcodeTemplate
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
        \Magestore\BarcodeSuccess\Model\ResourceModel\Template\CollectionFactory $collectionFactory,
        \Magestore\BarcodeSuccess\Api\Data\BarcodeTemplateInterface $barcodeTemplate,
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
            $meta,
            $data
        );
        $this->collection = $collectionFactory->create();
        if(isset($data['type_provider']) && $data['type_provider']) {
            $this->type_provider = $data['type_provider'];
        }
        $this->barcodeTemplate = $barcodeTemplate;
    }

    /**
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        if($this->type_provider == 'form') {
            $items = $this->collection->getItems();
            foreach ($items as $item) {
                $this->loadedData[$item->getId()] = $item->getData();
                $this->loadedData[$item->getId()]['preview'] = $item->getPreviewData();
            }
            if (!empty($data)) {
                $item = $this->collection->getNewEmptyItem();
                $item->setData($data);
                $this->loadedData[$item->getId()] = $item->getData();
                $this->loadedData[$item->getId()]['preview'] = $item->getPreviewData();
            }
            if(count($items) == 0){
                $item = $this->collection->getNewEmptyItem();
                $this->loadedData[$item->getId()] = $item->getData();
                $this->loadedData[$item->getId()]['preview'] = $item->getPreviewData();
            }
        }else{
            $this->loadedData = $this->getCollection()->toArray();
        }
        return $this->loadedData;
    }

    /**
     * @return \Magento\Framework\Api\Search\SearchResultInterface|\Magento\Framework\DataObject
     */
    public function getSearchResult()
    {
        if( ($this->request->getActionName() == 'gridToCsv') || ($this->request->getActionName() == 'gridToXml'))
        {
            $collection = $this->collection;//->getData();
            $count = $collection->getSize();
            $collection->setPageSize($collection->getSize()); // limit dung để query view dữ liệu - ko dùng cho export được
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
            /** @var \Magento\Framework\Search\EntityMetadata $entityMetadata */
            $entityMetadata = $objectManager->create('Magento\Framework\Search\EntityMetadata', ['entityId' => 'id']);
            /** @var \Magento\Framework\Search\Adapter\Mysql\DocumentFactory $documentFactory */
            $documentFactory = $objectManager->create(
                'Magento\Framework\Search\Adapter\Mysql\DocumentFactory',
                ['entityMetadata' => $entityMetadata]
            );
            /** @var \Magento\Framework\Api\Search\Document[] $documents */
            $documents = [];
            foreach($collection as $value){
                if($value->getStatus() == 1){
                    $value->setStatus('Active');
                }
                if($value->getStatus() == 2){
                    $value->setStatus('Inactive');
                }
                $documents[] = $documentFactory->create($value->getData());
            }
            $obj = new \Magento\Framework\DataObject();
            $obj->setItems($documents);
            $obj->setTotalCount($count);
            return $obj;
        }
        return parent::getSearchResult();
    }
}