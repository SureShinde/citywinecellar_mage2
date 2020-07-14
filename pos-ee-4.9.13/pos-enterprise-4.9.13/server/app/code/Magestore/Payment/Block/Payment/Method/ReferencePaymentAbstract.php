<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Payment\Block\Payment\Method;

/**
 * Class ReferencePaymentAbstract
 * @package Magestore\Payment\Block\Payment\Method
 */
class ReferencePaymentAbstract extends \Magento\Payment\Block\Info
{
    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Sales\Order\Payment\Collection
     */
    protected $orderPaymentCollection;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $helperPricing;


    /**
     * InfoAbstract constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Pricing\Helper\Data $helperPricing
     * @param \Magestore\Webpos\Model\ResourceModel\Sales\Order\Payment\Collection $orderPaymentCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Pricing\Helper\Data $helperPricing,
        \Magestore\Webpos\Model\ResourceModel\Sales\Order\Payment\Collection $orderPaymentCollection,
        array $data = []
    ) {
        $this->orderPaymentCollection = $orderPaymentCollection;
        $this->helperPricing = $helperPricing;

        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * @param null $transport
     * @return $this|\Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }
        $data = array();
        $orderId = $this->getInfo()->getData('parent_id');
        $code =  $this->getInfo()->getData('method');
        $amount = $this->getPaymentAmount($orderId, $code);
        if ($amount) {
            $referenceLabel = __('Reference No');
            $data[(string)$referenceLabel] = $this->helperPricing->currency($amount, true, false);
        }
        $transport = parent::_prepareSpecificInformation($transport);
        return $transport->setData(array_merge($data, $transport->getData()));
    }


    /**
     * @param $orderId
     * @param $code
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPaymentAmount($orderId, $code)
    {
        $payments = $this->orderPaymentCollection
            ->addFieldToFilter('order_id', $orderId)
            ->addFieldToFilter('method', $code)
        ;
        $amount = 0;
        if($payments->getSize() > 0){
            $payment = $payments->getFirstItem();
            $amount = $payment->getRealAmount();
        }
        return $amount;
    }

    /**
     * Construct function
     */
    protected function _construct()
    {
        parent::_construct();
    }


    /**
     * Get method title from setting
     */
    public function getMethodTitle()
    {
        $title = $this->_scopeConfig->getValue('payment/cashforpos/title', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($title == '') {
            $title = __("Cash");
        }
        return $title;
    }
    /**
     *
     * @return string
     */
    public function getCreditCardMethodTitle()
    {
        $title = $this->_scopeConfig->getValue('payment/ccforpos/title', ScopeInterface::SCOPE_STORE);
        if ($title == '') {
            $title = __("Credit Card");
        }
        return $title;
    }
}
