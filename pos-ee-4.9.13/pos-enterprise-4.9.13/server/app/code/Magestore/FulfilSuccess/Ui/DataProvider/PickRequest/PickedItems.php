<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Ui\DataProvider\PickRequest;

use Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\PickRequestItem\CollectionFactory;
use Magestore\FulfilSuccess\Ui\DataProvider\PickRequest\AbstractProvider;
use Magestore\FulfilSuccess\Service\PickRequest\PickService;
use Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface;

/**
 * Class PickedItems
 * @package Magestore\FulfilSuccess\Ui\DataProvider\PickRequest
 */
class PickedItems extends AbstractProvider
{
    /**
     * PickedItems constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Magento\Framework\Api\Search\ReportingInterface $reporting
     * @param \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param PickService $pickService
     * @param CollectionFactory $collectionFactory
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
        PickService $pickService,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $urlBuilder,
            $pickService,
            $meta,
            $data
        );
        $this->collection = $collectionFactory->create();
        $this->emptyCollection();
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $this->emptyCollection();
        $pickingItems = $this->pickService->getPickingItems();
        if(count($pickingItems) > 0){
            foreach ($pickingItems as $data){
                $item = $this->collection->getNewEmptyItem();
                $item->setData($data);
                $barcodes = $item->getData(PickRequestItemInterface::ITEM_BARCODE);
                if($barcodes){
                    $item->setData(PickRequestItemInterface::ITEM_BARCODE, $this->pickService->prepareBarcodesForView($barcodes));
                }
                $this->getCollection()->addItem($item);
            }
        }
        $this->loadedData = $this->getCollection()->toArray();
        $this->loadedData['totalRecords'] = count($this->getCollection()->getItems());
        return $this->loadedData;
    }
}