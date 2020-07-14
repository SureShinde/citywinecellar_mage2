<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option;

/**
 * Class PaymentMethod
 * @package Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option
 */
class PaymentMethod extends \Magestore\PurchaseOrderSuccess\Model\Option\AbstractOption
{
    const PAYMENT_METHOD_CONFIG_PATH = 'purchaseordersuccess/payment_method/payment_method';

    const OPTION_NEW_VALUE = 'os_new_payment_method';
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * ShippingMethod constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ){
        $this->scopeConfig = $scopeConfig;
    }
    
    public function getPaymentMethodOptions(){
        $config = $this->scopeConfig->getValue(self::PAYMENT_METHOD_CONFIG_PATH);
        $paymentMethods = $this->unserializeArray($config);
        $options = ['' => __('Select a payment method')];
        if($paymentMethods)
            foreach ($paymentMethods as $method){
                if($method['status'] == \Magestore\PurchaseOrderSuccess\Block\Adminhtml\Form\Field\Status::ENABLE_VALUE)
                    $options[$method['name']] = $method['name'];
            }
        $options[self::OPTION_NEW_VALUE] = __('New payment method');
        return $options;
    }

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public function getOptionHash()
    {
        return $this->getPaymentMethodOptions();
    }
}