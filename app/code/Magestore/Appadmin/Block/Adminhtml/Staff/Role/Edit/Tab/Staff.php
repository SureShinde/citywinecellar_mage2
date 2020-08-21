<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Block\Adminhtml\Staff\Role\Edit\Tab;

/**
 * Edit rold Staff
 */
class Staff extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magestore\Appadmin\Model\ResourceModel\Staff\Staff\CollectionFactory
     */
    protected $_staffCollectionFactory;

    /**
     * Staff constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magestore\Appadmin\Model\ResourceModel\Staff\Staff\CollectionFactory $staffCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magestore\Appadmin\Model\ResourceModel\Staff\Staff\CollectionFactory $staffCollectionFactory,
        array $data = []
    ) {
        $this->_staffCollectionFactory = $staffCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Construct
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('staff_grid');
        $this->setDefaultSort('staff_id');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('id')) {
            $this->setDefaultFilter(['in_staff' => 1]);
        }
    }

    /**
     * Prepare Collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_staffCollectionFactory->create();
        $roleId = (int)$this->getRequest()->getParam('id');
        $collection->addFieldToFilter('role_id', $roleId);

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare Columns
     *
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'staff_id',
            [
                'header' => __('ID'),
                'width' => '50px',
                'index' => 'staff_id',
                'type' => 'number',
            ]
        );

        $this->addColumn(
            'username',
            [
                'header' => __('User Name'),
                'index' => 'username'
            ]
        );

        $this->addColumn(
            'staff_name',
            [
                'header' => __('Display Name'),
                'index' => 'name'
            ]
        );

        $this->addColumn(
            'email',
            [
                'header' => __('Email'),
                'index' => 'email'
            ]
        );

        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => [
                    1 => 'Enable',
                    2 => 'Disable',
                ]
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Get Grid Url
     *
     * @return mixed|string
     */
    public function getGridUrl()
    {
        return $this->getData('grid_url')
            ? $this->getData('grid_url') :
            $this->getUrl('*/*/staffgrid', ['_current' => true, 'id' => $this->getRequest()->getParam('id')]);
    }

    /**
     * Get Row Url
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getRowUrl($row)
    {
        return '';
    }
}
