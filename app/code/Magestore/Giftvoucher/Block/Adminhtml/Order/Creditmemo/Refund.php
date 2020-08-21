<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block\Adminhtml\Order\Creditmemo;

/**
 * Adminhtml Giftvoucher Creditmemo Refund Block
 *
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Refund extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @param \Magento\Backend\Block\Template\Context|\Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_objectManager = $objectManager;
        $this->priceCurrency = $priceCurrency;
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
     * Get Order
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
     * @return boolean|\Magento\Customer\Model\Customer
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
     * Get Is Show
     *
     * @return boolean
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsShow()
    {
        return ($this->getCreditmemo()->getGiftVoucherDiscount());
    }

    /**
     * Get Max Amount
     *
     * @return float
     */
    public function getMaxAmount()
    {
        $maxAmount = 0;
        if ($this->getCreditmemo()->getGiftVoucherDiscount()) {
            $maxAmount += floatval($this->getCreditmemo()->getGiftVoucherDiscount());
        }
        return $this->priceCurrency->round($maxAmount);
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
     * @return boolean
     */
    public function isEnableCredit()
    {
        /*@TODO: load from configuration */

        return false;
    }
}
