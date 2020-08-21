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

namespace Magestore\Customercredit\Block\Adminhtml\Transaction;

/**
 * Class Grid
 *
 * Transaction grid block
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magestore\Customercredit\Model\TransactionFactory
     */
    protected $_transactionFactory;
    /**
     * @var \Magestore\Customercredit\Model\TransactionTypeFactory
     */
    protected $_transactionTypeFactory;

    /**
     * Grid constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magestore\Customercredit\Model\TransactionFactory $transactionFactory
     * @param \Magestore\Customercredit\Model\TransactionTypeFactory $transactionTypeFactory
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magestore\Customercredit\Model\TransactionFactory $transactionFactory,
        \Magestore\Customercredit\Model\TransactionTypeFactory $transactionTypeFactory,
        array $data = []
    ) {
        $this->_storeManager = $context->getStoreManager();
        $this->_transactionFactory = $transactionFactory;
        $this->_transactionTypeFactory = $transactionTypeFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('customercreditGrid');
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
        $collection = $this->_transactionFactory->create()->getCollection();
        $collection->getSelect()->joinLeft(
            ['table_type_transaction' => $collection->getTable('type_transaction')],
            'table_type_transaction.type_transaction_id = main_table.type_transaction_id',
            ['type_transaction' => 'table_type_transaction.transaction_name']
        );
         $collection->getSelect()
             ->joinLeft(
                 ['table_customer' => $collection->getTable('customer_entity')],
                 'table_customer.entity_id = main_table.customer_id',
                 [
                     'customer_email' => 'table_customer.email',
                     'firstname' => 'table_customer.firstname',
                     'lastname' => 'table_customer.lastname'
                 ]
             )->columns(
                 new \Zend_Db_Expr(
                     "CONCAT(`table_customer`.`firstname`, ' ',`table_customer`.`lastname`) AS customer_name"
                 )
             );
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Customer Email Filter
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function _customerEmailFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        $this->getCollection()->getSelect()->where(
            "`table_customer`.`email` LIKE ?",
            "%$value%"
        );
        return $this;
    }

    /**
     * Customer Name Filter
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function _customerNameFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        $this->getCollection()->getSelect()->where(
            "CONCAT(`table_customer`.`firstname`, ' ',`table_customer`.`lastname`) LIKE ?",
            "%$value%"
        );
        return $this;
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'transaction_id',
            [
                'header' => __('Transaction_Id'),
                'align' => 'left',
                'width' => '10px',
                'type' => 'number',
                'index' => 'transaction_id',
            ]
        );

        $typeArr = [];
        $collTrans = $this->_transactionTypeFactory->create()->getCollection();
        $count = 0;
        foreach ($collTrans as $item) {
            $count++;
            $typeArr[$count] = $item->getTransactionName();
        }

        $this->addColumn(
            'type_transaction_id',
            [
                'header' => __('Transaction Type'),
                'align' => 'left',
                'filter_index' => 'table_type_transaction.type_transaction_id',
                'index' => 'type_transaction_id',
                'type' => 'options',
                'options' => $typeArr,
            ]
        );

        $this->addColumn(
            'detail_transaction',
            [
                'header' => __('Transaction Detail'),
                'align' => 'left',
                'index' => 'detail_transaction',
            ]
        );

         $this->addColumn(
             'customer_name',
             [
                 'header' => __('Name'),
                 'index' => 'customer_name',
                 'filter_condition_callback' => [$this, '_customerNameFilter'],
             ]
         );
        $this->addColumn(
            'customer_email',
            [
                'header' => __('Email'),
                'width' => '150px',
                'index' => 'customer_email',
                'renderer' => \Magestore\Customercredit\Block\Adminhtml\Customer\Renderer\Customeremail::class,
                'filter_condition_callback' => [$this, '_customerEmailFilter'],
            ]
        );
        $currency = $this->_storeManager->getStore()->getBaseCurrencyCode();
        $this->addColumn(
            'amount_credit',
            [
                'header' => __('Added/Deducted'),
                'align' => 'left',
                'index' => 'amount_credit',
                'currency_code' => $currency,
                'type' => 'price',
            ]
        );
        $this->addColumn(
            'end_balance',
            [
                'header' => __('Credit Balance'),
                'align' => 'left',
                'index' => 'end_balance',
                'currency_code' => $currency,
                'type' => 'price',
            ]
        );
        $this->addColumn(
            'transaction_time',
            [
                'header' => __('Transaction Time'),
                'align' => 'left',
                'index' => 'transaction_time',
                'type' => 'datetime',
            ]
        );
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'align' => 'left',
                'width' => '80px',
                'index' => 'status',
                'filter' => false,
            ]
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/index', ['_current' => true]);
    }
}
