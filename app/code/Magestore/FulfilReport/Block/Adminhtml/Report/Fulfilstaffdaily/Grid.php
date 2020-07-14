<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilReport\Block\Adminhtml\Report\Fulfilstaffdaily;

use Magestore\FulfilSuccess\Api\Data\PickRequestInterface;
use Magestore\FulfilSuccess\Api\Data\PackRequestInterface;

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
     * @var \Magestore\FulfilReport\Model\ResourceModel\PickRequest\FulfilStaffDaily\CollectionFactory
     */
    protected $pickRequestCollectionFactory;

    /**
     * @var \Magestore\FulfilReport\Model\ResourceModel\PackRequest\FulfilStaffDaily\CollectionFactory
     */
    protected $packRequestCollectionFactory;

    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magestore\FulfilReport\Model\ResourceModel\PickRequest\FulfilStaffDaily\CollectionFactory $pickRequestCollectionFactory
     * @param \Magestore\FulfilReport\Model\ResourceModel\PackRequest\FulfilStaffDaily\CollectionFactory $packRequestCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magestore\FulfilReport\Model\ResourceModel\PickRequest\FulfilStaffDaily\CollectionFactory $pickRequestCollectionFactory,
        \Magestore\FulfilReport\Model\ResourceModel\PackRequest\FulfilStaffDaily\CollectionFactory $packRequestCollectionFactory,
        array $data = [])
    {
        parent::__construct($context, $backendHelper, $data);
        $this->pickRequestCollectionFactory = $pickRequestCollectionFactory;
        $this->packRequestCollectionFactory = $packRequestCollectionFactory;
        $this->_filterVisibility = false;
    }

    protected function _prepareCollection()
    {
        if ($this->getFilterData()->getData('fulfil_action') == \Magestore\FulfilReport\Model\Action\Options::PACKED) {
            /** @var \Magestore\FulfilReport\Model\ResourceModel\PackRequest\FulfilStaffDaily\Collection $collection */
            $collection = $this->packRequestCollectionFactory->create();
        } else {
            /** @var \Magestore\FulfilReport\Model\ResourceModel\PickRequest\FulfilStaffDaily\Collection $collection */
            $collection = $this->pickRequestCollectionFactory->create();
        }

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

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'date',
            [
                'header' => __('Day'),
                'index' => 'date',
                'filter' => false,
                'sortable' => false,
                'totals_label' => __(''),
                'header_css_class' => 'col-date',
                'column_css_class' => 'col-date'
            ]
        );

        $this->addColumn(
            'username',
            [
                'header' => __('User Name'),
                'index' => 'username',
                'filter' => false,
                'sortable' => false,
                'totals_label' => __(''),
                'header_css_class' => 'col-user-name',
                'column_css_class' => 'col-user-name'
            ]
        );

        $this->addColumn(
            'total_requests',
            [
                'header' => __('Total Requests'),
                'index' => 'total_requests',
                'type' => 'number',
                'filter' => false,
                'sortable' => false,
                'totals_label' => __(''),
                'header_css_class' => 'col-total_requests',
                'column_css_class' => 'col-total_requests'
            ]
        );

        return parent::_prepareColumns();
    }
}
