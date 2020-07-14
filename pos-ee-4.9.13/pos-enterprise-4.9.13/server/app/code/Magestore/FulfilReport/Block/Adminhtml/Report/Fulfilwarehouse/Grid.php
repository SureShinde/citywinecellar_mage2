<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilReport\Block\Adminhtml\Report\Fulfilwarehouse;

use Magento\Framework\DataObject;

/**
 * Report grid container.
 * @category Magestore
 * @package  Magestore_Webpos
 * @module   Webpos
 * @author   Magestore Developer
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Data\CollectionFactory
     */
    protected $dataCollection;

    /**
     * @var \Magestore\FulfilSuccess\Api\FulfilManagementInterface
     */
    protected $fulfilManagement;

    /**
     * @var \Magestore\FulfilReport\Model\ResourceModel\PickRequest\FulfilWarehouse\CollectionFactory
     */
    protected $pickRequestCollectionFactory;

    /**
     * @var \Magestore\FulfilReport\Model\ResourceModel\PackRequest\FulfilWarehouse\CollectionFactory
     */
    protected $packRequestCollectionFactory;

    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param FulfilManagementInterface $fulfilManagement
     * @param \Magestore\FulfilReport\Model\ResourceModel\PickRequest\FulfilWarehouse\CollectionFactory $pickRequestCollectionFactory
     * @param \Magestore\FulfilReport\Model\ResourceModel\PackRequest\FulfilWarehouse\CollectionFactory $packRequestCollectionFactory
     * @param \Magento\Framework\Data\CollectionFactory $dataCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magestore\FulfilSuccess\Api\FulfilManagementInterface $fulfilManagement,
        \Magestore\FulfilReport\Model\ResourceModel\PickRequest\FulfilWarehouse\CollectionFactory $pickRequestCollectionFactory,
        \Magestore\FulfilReport\Model\ResourceModel\PackRequest\FulfilWarehouse\CollectionFactory $packRequestCollectionFactory,
        \Magento\Framework\Data\CollectionFactory $dataCollection,
        array $data = [])
    {
        parent::__construct($context, $backendHelper, $data);
        $this->fulfilManagement = $fulfilManagement;
        $this->pickRequestCollectionFactory = $pickRequestCollectionFactory;
        $this->packRequestCollectionFactory = $packRequestCollectionFactory;
        $this->dataCollection = $dataCollection;
        $this->_filterVisibility = false;
    }

    protected function _prepareCollection()
    {
        $resultArray = [];
        $pickRequestsCollection = $this->getPickRequestsByWarehouse();
        $packRequestsCollection = $this->getPackRequestsByWarehouse();
        /** @var \Magento\Framework\Data\Collection $collection */
        $collection = $this->dataCollection->create();

        /** @var \Magestore\FulfilSuccess\Api\Data\PickRequestInterface $pickRequest */
        foreach ($pickRequestsCollection as $pickRequest) {
            $resultArray[$pickRequest->getResourceFieldId()]['warehouse'] = $pickRequest->getResourceFieldName();
            $resultArray[$pickRequest->getResourceFieldId()]['pick'] = $pickRequest->getData('total_picked_requests');
        }

        /** @var \Magestore\FulfilSuccess\Api\Data\PackRequestInterface $packRequest */
        foreach ($packRequestsCollection as $packRequest) {
            $resultArray[$packRequest->getResourceFieldId()]['warehouse'] = $packRequest->getResourceFieldName();
            $resultArray[$packRequest->getResourceFieldId()]['pack'] = $packRequest->getData('total_packed_requests');
        }

        foreach ($resultArray as $warehouseData) {
            $item = new DataObject;
            $item->setWarehouseName($warehouseData['warehouse']);
            $totalPicked = isset($warehouseData['pick']) ? $warehouseData['pick'] : 0;
            $totalPacked = isset($warehouseData['pack']) ? $warehouseData['pack'] : 0;
            $item->setData('total_picked_requests', $totalPicked);
            $item->setData('total_packed_requests', $totalPacked);
            $collection->addItem($item);
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $warehouseNameLabel = __('Warehouse Name');
        if ($this->fulfilManagement->isMSIEnable()) {
            $warehouseNameLabel = __('Source Name');
        }
        $this->addColumn(
            'warehouse_name',
            [
                'header' => $warehouseNameLabel,
                'index' => 'warehouse_name',
                'filter' => false,
                'sortable' => false,
                'totals_label' => __(''),
                'header_css_class' => 'col-warehouse-name',
                'column_css_class' => 'col-warehouse-name'
            ]
        );

        $this->addColumn(
            'total_picked_requests',
            [
                'header' => __('Total Picked Requests'),
                'index' => 'total_picked_requests',
                'filter' => false,
                'sortable' => false,
                'totals_label' => __(''),
                'header_css_class' => 'col-total-picked-requests',
                'column_css_class' => 'col-total-picked-requests'
            ]
        );

        $this->addColumn(
            'total_packed_requests',
            [
                'header' => __('Total Packed Requests'),
                'index' => 'total_packed_requests',
                'filter' => false,
                'sortable' => false,
                'totals_label' => __(''),
                'header_css_class' => 'col-total-packed-requests',
                'column_css_class' => 'col-total-packed-requests'
            ]
        );

        return parent::_prepareColumns();
    }

    public function getPickRequestsByWarehouse()
    {
        /** @var \Magestore\FulfilReport\Model\ResourceModel\PickRequest\FulfilWarehouse\Collection $collection */
        $collection = $this->pickRequestCollectionFactory->create();

        /** @var \Magento\Framework\Stdlib\DateTime\DateTime $dateTime */
        $dateTime = \Magento\Framework\App\ObjectManager::getInstance()->create(
            '\Magento\Framework\Stdlib\DateTime\DateTime'
        );

        if ($this->getFilterData()->getData('from')) {
            $fromDate = $this->getFilterData()->getData('from');
            $fromDate = $dateTime->date('Y-m-d 00:00:00', $fromDate);
        }

        if ($this->getFilterData()->getData('to')) {
            $toDate = $this->getFilterData()->getData('to');
            $toDate = $dateTime->date('Y-m-d 23:59:59', $toDate);
        }

        $collection->addFieldToFilter('main_table.updated_at', [
            'from' => $fromDate,
            'to' => $toDate
        ]);

        return $collection;
    }

    public function getPackRequestsByWarehouse()
    {
        /** @var \Magestore\FulfilReport\Model\ResourceModel\PackRequest\FulfilWarehouse\Collection $collection */
        $collection = $this->packRequestCollectionFactory->create();

        /** @var \Magento\Framework\Stdlib\DateTime\DateTime $dateTime */
        $dateTime = \Magento\Framework\App\ObjectManager::getInstance()->create(
            '\Magento\Framework\Stdlib\DateTime\DateTime'
        );

        if ($this->getFilterData()->getData('from')) {
            $fromDate = $this->getFilterData()->getData('from');
            $fromDate = $dateTime->date('Y-m-d 00:00:00', $fromDate);
        }

        if ($this->getFilterData()->getData('to')) {
            $toDate = $this->getFilterData()->getData('to');
            $toDate = $dateTime->date('Y-m-d 23:59:59', $toDate);
        }

        $collection->addFieldToFilter('main_table.updated_at', [
            'from' => $fromDate,
            'to' => $toDate
        ]);

        return $collection;
    }
}
