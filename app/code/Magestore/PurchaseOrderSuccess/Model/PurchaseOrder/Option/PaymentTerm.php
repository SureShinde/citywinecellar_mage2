<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option;

/**
 * Class PaymentTerm
 * @package Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option
 */
class PaymentTerm extends \Magestore\PurchaseOrderSuccess\Model\Option\AbstractOption
{
    const PAYMENT_TERM_CONFIG_PATH = 'purchaseordersuccess/payment_term/payment_term';

    const OPTION_NONE_VALUE = 'os_none_payment_term';

    const OPTION_NEW_VALUE = 'os_new_payment_term';
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
    
    public function getPaymentTermOptions(){
        $config = $this->scopeConfig->getValue(self::PAYMENT_TERM_CONFIG_PATH);
        $paymentTerms = $this->unserializeArray($config);
        $options = [self::OPTION_NONE_VALUE => __('Select a payment term')];
        if($paymentTerms)
            foreach ($paymentTerms as $term){
                if($term['status'] == \Magestore\PurchaseOrderSuccess\Block\Adminhtml\Form\Field\Status::ENABLE_VALUE)
                    $options[$term['name']] = $term['name'];
            }
        $options[self::OPTION_NEW_VALUE] = __('New payment term');
        return $options;
    }

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public function getOptionHash()
    {
        return $this->getPaymentTermOptions();
    }
}