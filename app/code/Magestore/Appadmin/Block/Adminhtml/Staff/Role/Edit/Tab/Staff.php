<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Block\Adminhtml\Staff\Role\Edit\Tab;
/**
 * Class Staff
 * @package Magestore\Appadmin\Block\Adminhtml\Staff\Role\Edit\Tab
 */
class Staff extends \Magento\Backend\Block\Widget\Grid\Extended {

    /**
     * @var \Magestore\Appadmin\Model\ResourceModel\Staff\Staff\CollectionFactory
     */
    protected $_staffCollectionFactory;

    /**
     * Staff constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magestore\Appadmin\Model\ResourceModel\Staff\Staff\CollectionFactory $staffCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magestore\Appadmin\Model\ResourceModel\Staff\Staff\CollectionFactory $staffCollectionFactory,
        array $data = array()
    )
    {
        $this->_staffCollectionFactory = $staffCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     *
     */
    public function _construct() {
        parent::_construct();
        $this->setId('staff_grid');
        $this->setDefaultSort('staff_id');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('id')) {
            $this->setDefaultFilter(array('in_staff' => 1));
        }
    }

    /**
     * @return $this
     */
    protected function _prepareCollection() {
        $collection = $this->_staffCollectionFactory->create();
        $roleId = (int)$this->getRequest()->getParam('id');
        $collection->addFieldToFilter('role_id', $roleId);

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns() {
        $this->addColumn('staff_id', array(
            'header' => __('ID'),
            'width' => '50px',
            'index' => 'staff_id',
            'type' => 'number',
        ));

        $this->addColumn('username', array(
            'header' => __('User Name'),
            'index' => 'username'
        ));

        $this->addColumn('staff_name', array(
            'header' => __('Display Name'),
            'index' => 'name'
        ));

        $this->addColumn('email', array(
            'header' => __('Email'),
            'index' => 'email'
        ));

        $this->addColumn('status', array(
            'header' => __('Status'),
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => 'Enable',
                2 => 'Disable',
            ),

        ));

        return parent::_prepareColumns();
    }

    /**
     * @return mixed|string
     */
    public function getGridUrl() {
        return $this->getData('grid_url') ? $this->getData('grid_url') :
            $this->getUrl('*/*/staffgrid', array('_current' => true, 'id' => $this->getRequest()->getParam('id')));
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row) {
        return '';
    }
}