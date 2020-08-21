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
 * @package     Magestore_Customercredit
 * @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

namespace Magestore\Customercredit\Block\Adminhtml\Customer\Tab;

use Magento\Customer\Controller\RegistryConstants;

/**
 * Class Transaction
 *
 * Transaction block
 */
class Transaction extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    /**
     * @var \Magestore\Customercredit\Model\TransactionFactory
     */
    protected $_transactionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magestore\Customercredit\Model\TransactionFactory $transactionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $coreRegistry,
        \Magestore\Customercredit\Model\TransactionFactory $transactionFactory,
        array $data = []
    ) {
        $this->_storeManager = $context->getStoreManager();
        $this->_coreRegistry = $coreRegistry;
        $this->_transactionFactory = $transactionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('transactionGrid');
        $this->setDefaultSort('transaction_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Apply sorting and filtering to collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $customerId = $this->getRequest()->getParam('customer_id');
        if (!$customerId) {
            $customerId = $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
        }
        $collection = $this->_transactionFactory->create()->getCollection()
            ->addFieldToFilter('customer_id', $customerId);
        $collection->getSelect()->joinLeft(
            ['table_type_transaction' => $collection->getTable('type_transaction')],
            'table_type_transaction.type_transaction_id = main_table.type_transaction_id',
            ['type_transaction' => 'table_type_transaction.transaction_name']
        );
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    /**
     * @inheritDoc
     */
    protected function _prepareColumns()
    {
        $this->addColumn('transaction_id', [
            'header' => __('ID'),
            'align' => 'left',
            'width' => '50px',
            'type' => 'number',
            'index' => 'transaction_id',
        ]);
        $this->addColumn('type_transaction', [
            'header' => __('Type of Transaction'),
            'align' => 'left',
            'filter_index' => 'table_type_transaction.transaction_name',
            'index' => 'type_transaction',
        ]);

        $this->addColumn('detail_transaction', [
            'header' => __('Transaction Detail'),
            'align' => 'left',
            'index' => 'detail_transaction',
        ]);
        $currency = $this->_storeManager->getStore()->getBaseCurrencyCode();

        $this->addColumn('amount_credit', [
            'header' => __('Added/ Subtracted'),
            'align' => 'left',
            'index' => 'amount_credit',
            'currency_code' => $currency,
            'type' => 'price',
        ]);
        $this->addColumn('end_balance', [
            'header' => __('Credit Balance'),
            'align' => 'left',
            'index' => 'end_balance',
            'currency_code' => $currency,
            'type' => 'price',
        ]);
        $this->addColumn('transaction_time', [
            'header' => __('Transaction Time'),
            'align' => 'left',
            'index' => 'transaction_time',
            'type' => 'datetime',
        ]);

        $this->addColumn('status', [
            'header' => __('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'filter' => false,
        ]);

        return parent::_prepareColumns();
    }

    /**
     * Retrieve grid reload url
     *
     * @return string;
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'customercreditadmin/customer/transaction',
            [
                '_current' => true,
                'customer_id' => $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID),
            ]
        );
    }
}
