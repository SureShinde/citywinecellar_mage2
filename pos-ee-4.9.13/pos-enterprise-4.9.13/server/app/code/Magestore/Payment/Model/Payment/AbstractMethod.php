<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Payment\Model\Payment;

/**
 * Class AbstractMethod
 * @package Magestore\Payment\Model\Payment
 */
class AbstractMethod extends \Magento\Payment\Model\Method\AbstractMethod
{
    /**
     *
     */
    const ALLOW_SPECIFIC_PAYMENT = 'webpos/payment/allowspecific_payment';
    /**
     *
     */
    const SPECIFIC_PAYMENT = 'webpos/payment/specificpayment';


    /**
     * @var string
     */
    protected $enabledPath = '';

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;


    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->request = $request;

        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data
        );
    }


    /**
     * Enable for Web POS only
     * @return boolean
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        $routeName = $this->request->getRouteName();
        $settingEnabled = $this->isEnabledSetting();
        if ($routeName == "webpos"&& $settingEnabled == true){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isEnabledSetting(){
        return ($this->_scopeConfig->getValue($this->enabledPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE) && $this->isAllowOnWebPOS($this->getCode()));
    }

    /**
     *
     * @param string $code
     * @return boolean
     */
    public function isAllowOnWebPOS($code)
    {
        if ($this->_scopeConfig->getValue(self::ALLOW_SPECIFIC_PAYMENT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE) == '1') {
            $specificpayment = $this->_scopeConfig->getValue(self::SPECIFIC_PAYMENT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $specificpayment = explode(',', $specificpayment);
            if (in_array($code, $specificpayment)) {
                return true;
            } else {
                return false;
            }
        }
        return true;
    }

}
