<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Giftvoucher
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Rewardpoints\Block\Adminhtml\Customer\Tab;

use Magento\Customer\Controller\RegistryConstants;

/**
 * Customer tab - Reward points history
 */
class History extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Magestore\Rewardpoints\Model\Customer
     */
    protected $_rewardCustomer;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magestore\Rewardpoints\Model\Transaction
     */
    protected $_transaction;

    /**
     * @var \Magestore\Rewardpoints\Ui\Component\Listing\Column\Transaction\Actions $actions
     */
    protected $_actions;

    /**
     * @var \Magestore\Rewardpoints\Ui\Component\Listing\Column\Transaction\Status $status
     */
    protected $_status;

    /**
     * @var \Magestore\Rewardpoints\Ui\Component\Listing\Column\Transaction\StoreView $storeView
     */
    protected $_storeView;

    /**
     * History constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magestore\Rewardpoints\Model\Customer $rewardCustomer
     * @param \Magestore\Rewardpoints\Model\TransactionFactory $transaction
     * @param \Magestore\Rewardpoints\Ui\Component\Listing\Column\Transaction\Actions $actions
     * @param \Magestore\Rewardpoints\Ui\Component\Listing\Column\Transaction\Status $status
     * @param \Magestore\Rewardpoints\Ui\Component\Listing\Column\Transaction\StoreView $storeView
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $coreRegistry,
        \Magestore\Rewardpoints\Model\Customer $rewardCustomer,
        \Magestore\Rewardpoints\Model\TransactionFactory $transaction,
        \Magestore\Rewardpoints\Ui\Component\Listing\Column\Transaction\Actions $actions,
        \Magestore\Rewardpoints\Ui\Component\Listing\Column\Transaction\Status $status,
        \Magestore\Rewardpoints\Ui\Component\Listing\Column\Transaction\StoreView $storeView,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_rewardCustomer = $rewardCustomer;
        $this->_transaction = $transaction;
        $this->_actions = $actions;
        $this->_status = $status;
        $this->_storeView = $storeView;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {

        parent::_construct();
        $this->setId('transactionHistoryGrid');
        $this->setDefaultSort('transaction_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * @inheritDoc
     */
    protected function _prepareCollection()
    {
        $customerId = $this->getRequest()->getParam('customer_id');
        if (!$customerId) {
            $customerId = $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
        }
        $collection = $this->_transaction->create()
            ->getCollection()
            ->addFieldToFilter('customer_id', $customerId);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @inheritDoc
     */
    protected function _prepareColumns()
    {
        $this->addColumn('transaction_id', [
            'header'    => __('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'transaction_id',
            'type'      => 'number',
        ]);

        $this->addColumn('title', [
            'header'    => __('Title'),
            'align'     =>'left',
            'index'     => 'title',
        ]);

        $this->addColumn('action', [
            'header'    => __('Action'),
            'align'     => 'left',
            'index'     => 'action',
            'type'      => 'options',
            'options'   => $this->_actions->toOptionHash(),
        ]);

        $this->addColumn('point_amount', [
            'header'    => __('Points'),
            'align'     => 'right',
            'index'     => 'point_amount',
            'type'      => 'number',
        ]);

        $this->addColumn('point_used', [
            'header'    => __('Points Used'),
            'align'     => 'right',
            'index'     => 'point_used',
            'type'      => 'number',
        ]);

        $this->addColumn('created_time', [
            'header'    => __('Created On'),
            'index'     => 'created_time',
            'type'      => 'datetime',
        ]);

        $this->addColumn('expiration_date', [
            'header'    => __('Expires On'),
            'index'     => 'expiration_date',
            'type'      => 'datetime',
        ]);

        $this->addColumn('status', [
            'header'    => __('Status'),
            'align'     => 'left',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => $this->_status->toOptionHash(),
        ]);

        $this->addColumn('store_id', [
            'header'    => __('Store View'),
            'align'     => 'left',
            'index'     => 'store_id',
            'type'      => 'options',
            'options'   => $this->_storeView->toOptionHash(),
        ]);

        return parent::_prepareColumns();
    }

    /**
     * @inheritDoc
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('rewardpoints/transaction/view', ['id' => $row->getId()]);
    }

    /**
     * @inheritDoc
     */
    public function getGridUrl()
    {
        return $this->getUrl('rewardpoints/customer/rewardhistorygrid', ['_current' => true]);
    }
}
