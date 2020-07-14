<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Payment\Block\Payment\Method;

/**
 * Class MultiPayment
 * @package Magestore\Payment\Block\Payment\Method
 */
class MultiPayment extends \Magento\Payment\Block\Info
{
    /**
     * Helper payment object
     *
     * @var \Magestore\Webpos\Helper\Payment
     */
    protected $_helperPricing = '';

    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Sales\Order\Payment\CollectionFactory|string
     */
    protected $orderPaymentCollectionFactory = '';

    /**
     * Model order repository
     *
     * @var \Magento\Sales\Model\OrderRepository
     */
    protected $_orderRepository = '';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;


    /**
     * MultiPayment constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Pricing\Helper\Data $helperPricing
     * @param \Magestore\Webpos\Model\ResourceModel\Sales\Order\Payment\CollectionFactory $orderPaymentCollectionFactory
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Pricing\Helper\Data $helperPricing,
        \Magestore\Webpos\Model\ResourceModel\Sales\Order\Payment\CollectionFactory $orderPaymentCollectionFactory,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {

        $this->_helperPricing = $helperPricing;
            $this->orderPaymentCollectionFactory = $orderPaymentCollectionFactory;
        $this->_orderRepository = $orderRepository;
        $this->_coreRegistry = $registry;
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Construct function
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Magestore_Payment::payment/method/info/multi_payment.phtml');
    }

    /**
     * Get method title from setting
     */
    public function getMethodTitle()
    {
        $title = $this->_scopeConfig->getValue('payment/multipaymentforpos/title', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($title == '') {
            $title = __('Split Payments');
        }
        return $title;
    }


    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSpecificInformation(){
        $specificInformation = [];
        $actualTotalPaid = 0;
        foreach ($this->getOrderPaymentMethods() as $paymentMethod){
            if ($paymentMethod->getData('base_amount_paid') > 0) {
                $actualTotalPaid += $paymentMethod->getData('base_amount_paid');
                $specificInformation[] = array(
                    'label' => $paymentMethod->getData('title'),
                    'value' => $this->_helperPricing->currency($paymentMethod->getData('base_amount_paid'), true, false),
                    'reference_number' => $paymentMethod->getData('reference_number'),
                    'card_type' => $paymentMethod->getData('card_type'),
                    'cc_last4' => $paymentMethod->getData('cc_last4'),
                );
            }
        }
        $orderId = $this->getInfo()->getData('parent_id');
        $baseTotalPaid = 0;
        if($this->_coreRegistry->registry('current_order')){
            $baseTotalPaid = $this->_coreRegistry->registry('current_order')->getBaseTotalPaid();
        }else{
            try{
                $baseTotalPaid = $this->_orderRepository->get($orderId)->getBaseTotalPaid();
            }catch (\Exception $e){

            }
        }
        if($baseTotalPaid !== 0){
            if($actualTotalPaid < $baseTotalPaid){
                array_push($specificInformation,[
                    'label' => __('Other'),
                    'value' => $this->_helperPricing->currency($baseTotalPaid - $actualTotalPaid , true, false),
                ]);
            }
        }
        return $specificInformation;
    }

    /**
     * @return \Magestore\Webpos\Model\ResourceModel\Sales\Order\Payment\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getOrderPaymentMethods()
    {
        $orderId = $this->getInfo()->getData('parent_id');
        $payments = $this->orderPaymentCollectionFactory->create()
            ->addFieldToFilter('order_id', $orderId)
            ->addFieldToFilter('type', \Magestore\Webpos\Api\Data\Payment\PaymentInterface::ORDER_TYPE)
        ;
        return $payments;
    }

}
