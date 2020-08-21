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

namespace Magestore\Customercredit\Block\Order\Creditmemo;

/**
 * Class Refund
 *
 * Creditmemo refund block
 */
class Refund extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;
    /**
     * @var \Magestore\Customercredit\Helper\Data
     */
    protected $_creditHelper;
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customer;
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_order;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * Refund constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magestore\Customercredit\Helper\Data $creditHelper
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magestore\Customercredit\Helper\Data $creditHelper,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_objectManager = $objectManager;
        $this->priceCurrency = $priceCurrency;
        $this->_creditHelper = $creditHelper;
        $this->_customer = $customerFactory;
        $this->_order = $orderFactory;
        $this->_request = $context->getRequest();
        parent::__construct($context, $data);
    }

    /**
     * Get Creditmemo
     *
     * @return \Magento\Sales\Model\Order\Creditmemo
     */
    public function getCreditmemo()
    {
        return $this->_coreRegistry->registry('current_creditmemo');
    }

    /**
     * Get order
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getCreditmemo()->getOrder();
    }

    /**
     * Get Customer
     *
     * @return bool|\Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        $order = $this->getOrder();
        if ($order->getCustomerIsGuest()) {
            return false;
        }
        $customer = $this->_objectManager->get(\Magento\Customer\Model\Customer::class)->load($order->getCustomerId());
        if ($customer->getId()) {
            return $customer;
        }
        return false;
    }

    /**
     * Get Grand Total
     *
     * @return float
     */
    public function getGrandTotal()
    {
        $totalsBlock = $this->_objectManager->get(\Magento\Sales\Block\Order\Creditmemo\Totals::class);
        $creditmemo = $totalsBlock->getCreditmemo();
        return $creditmemo->getGrandTotal();
    }

    /**
     * Get Max Amount
     *
     * @return float
     */
    public function getMaxAmount()
    {
        $maxAmount = 0;
        if ($this->getCreditmemo()->getUseStoreCreditAmount() && $this->isEnableCredit()) {
            $maxAmount += floatval($this->getCreditmemo()->getUseStoreCreditAmount());
        }
        if ($this->getCreditmemo()->getCustomercreditDiscount()) {
            $maxAmount += floatval($this->getCreditmemo()->getCustomercreditDiscount());
        }
        return $this->priceCurrency->round($maxAmount);
    }

    /**
     * Enable Template
     *
     * @return bool
     */
    public function enableTemplate()
    {
        $order_id = $this->_request->getParam('order_id');
        return $this->_creditHelper->isBuyCreditProduct($order_id);
    }

    /**
     * Is Assign Credit
     *
     * @return bool
     */
    public function isAssignCredit()
    {
        return true;
    }

    /**
     * Format Price
     *
     * @param float $price
     * @return string
     */
    public function formatPrice($price)
    {
        return $this->getOrder()->format($price);
    }

    /**
     * Is Enable Credit
     *
     * @return mixed
     */
    public function isEnableCredit()
    {
        return $this->_creditHelper->getGeneralConfig('enable');
    }
}
