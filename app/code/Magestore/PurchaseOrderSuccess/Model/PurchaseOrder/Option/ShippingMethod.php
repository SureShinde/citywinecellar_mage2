<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option;

/**
 * Class ShippingMethod
 * @package Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option
 */
class ShippingMethod extends \Magestore\PurchaseOrderSuccess\Model\Option\AbstractOption
{
    const SHIPPING_METHOD_CONFIG_PATH = 'purchaseordersuccess/shipping_method/shipping_method';
    
    const OPTION_NONE_VALUE = 'os_none_payment_method';
    
    const OPTION_NEW_VALUE = 'os_new_shipping_method';

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
    
    public function getShippingMethodOptions(){
        $config = $this->scopeConfig->getValue(self::SHIPPING_METHOD_CONFIG_PATH);
        $shippingMethods = $this->unserializeArray($config);
        $options = [self::OPTION_NONE_VALUE => __('Select a shipping method')];
        if($shippingMethods)
            foreach ($shippingMethods as $method){
                if($method['status'] == \Magestore\PurchaseOrderSuccess\Block\Adminhtml\Form\Field\Status::ENABLE_VALUE)
                    $options[$method['name']] = $method['name'];
            }
        $options[self::OPTION_NEW_VALUE] = __('New shipping method');
        return $options;
    }

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public function getOptionHash()
    {
        return $this->getShippingMethodOptions();
    }
}