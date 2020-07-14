<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Adminhtml\Pos\Tab;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class LinkedPos extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Pos\Pos\CollectionFactory
     */
    protected $posCollectionFactory;
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magestore\Webpos\Model\ResourceModel\Pos\Pos\CollectionFactory $posCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magestore\Webpos\Model\ResourceModel\Pos\Pos\CollectionFactory $posCollectionFactory,
        array $data = []
    ) {
        $this->posCollectionFactory = $posCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('linked-pos');
        $this->setDefaultSort('pos_id');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
        $this->setEmptyText(__('No POS available in this location.'));
        $this->setFilterVisibility(false);
    }

    /**
     * @return Grid
     */
    protected function _prepareCollection()
    {
        $locationId = (int)$this->getRequest()->getParam('id');
        $collection = $this->posCollectionFactory->create()->addFieldToFilter('location_id', $locationId);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'pos_id',
            [
                'header' => __('ID'),
                'index' => 'pos_id',
                'type' => 'text'
            ]
        );

        $this->addColumn(
            'pos_name',
            [
                'header' => __('POS Name'),
                'index' => 'pos_name',
                'type' => 'text'
            ]
        );

        $this->addColumn(
            'status',
            [
                'header' => __('Operating Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => [
                    '1' => 'Enabled',
                    '2' => 'Disabled'
                ]
            ]
        );

        $this->addColumn(
            'edit',
            [
                'header' => __('Action'),
                'type' => 'action',
                'getter' => 'getPosId',
                'actions' => [
                    [
                        'caption' => __('Details'),
                        'url' => [
                            'base' => 'webposadmin/pos/edit'
                        ],
                        'field' => 'id'
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('webposadmin/location_location/grid', ['_current' => true]);
    }
}