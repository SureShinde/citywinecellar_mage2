<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Helper;

use \Magento\Store\Model\ScopeInterface;

/**
 * class \Magestore\Webpos\Helper\Payment
 * 
 * Web POS Payment helper
 * Methods:
 *  getCasgMethodTitle
 *  getCcMethodTitle
 *  getCodMethodTitle
 *  getCp1MethodTitle
 *  getCp2MethodTitle
 *  getDefaultPaymentMethod
 *  getMultipaymentActiveMethodTitle
 *  getMultipaymentMethodTitle
 *  isAllowOnWebPOS
 *  isCashPaymentEnabled
 *  isCcPaymentEnabled
 *  isCodPaymentEnabled
 *  isCp1PaymentEnabled
 *  isCp2PaymentEnabled
 *  isMultiPaymentEnabled
 *  isWebposShippingEnabled
 *  updateCashTransactionFromOrder
 * 
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Payment extends Data
{

    /**
     *
     * @return string
     */
    public function getPaymentTitle($code)
    {
        $title = $this->scopeConfig->getValue('payment/'.$code.'/title', ScopeInterface::SCOPE_STORE);
        return $title;
    }

    /**
     * get title of Cash payment method
     * @return string
     */
    public function getCashMethodTitle()
    {
        $title = $this->scopeConfig->getValue('payment/cashforpos/title', ScopeInterface::SCOPE_STORE);
        if ($title == '') {
            $title = __("Cash ( For Web POS only)");
        }
        return $title;
    }
    
    /**
     * 
     * @return boolean
     */
    public function isCashPaymentEnabled()
    {
        return ($this->scopeConfig->getValue('payment/cashforpos/active', ScopeInterface::SCOPE_STORE) && $this->isAllowOnWebPOS('cashforpos'));
    }
    
    /**
     * 
     * @return string
     */
    public function getCcMethodTitle()
    {
        $title = $this->scopeConfig->getValue('payment/ccforpos/title', ScopeInterface::SCOPE_STORE);
        if ($title == '') {
            $title = __("Cash ( For Web POS only)");
        }
        return $title;
    }
    
    /**
     * 
     * @return boolean
     */
    public function isCcPaymentEnabled()
    {
        return ($this->scopeConfig->getValue('payment/ccforpos/active', ScopeInterface::SCOPE_STORE) && $this->isAllowOnWebPOS('ccforpos'));
    }
    
    /**
     * 
     * @return string
     */
    public function isWebposShippingEnabled()
    {
        return $this->scopeConfig->getValue('carriers/webpos_shipping/active', ScopeInterface::SCOPE_STORE);
    }

    /**
     * 
     * @return string
     */
    public function getCodMethodTitle()
    {
        $title = $this->scopeConfig->getValue('payment/codforpos/title', ScopeInterface::SCOPE_STORE);
        if ($title == '') {
            $title = __("Web POS - Cash On Delivery");
        }
        return $title;
    }
    
    /**
     * 
     * @return boolean
     */
    public function isCodPaymentEnabled()
    {
        return ($this->scopeConfig->getValue('payment/codforpos/active', ScopeInterface::SCOPE_STORE) && $this->isAllowOnWebPOS('codforpos'));
    }

    /**
     * 
     * @return string
     */
    public function getMultipaymentMethodTitle()
    {
        $title = $this->scopeConfig->getValue('payment/multipaymentforpos/title', ScopeInterface::SCOPE_STORE);
        if ($title == '') {
            $title = __("Web POS - Split Payments");
        }
        return $title;
    }

    /**
     * 
     * @return array
     */
    public function getMultipaymentActiveMethodTitle()
    {
        $payments = $this->scopeConfig->getValue('payment/multipaymentforpos/payments', ScopeInterface::SCOPE_STORE);
        if ($payments == '') {
            $payments = explode(',', 'cashforpos,ccforpos,codforpos');
        }
        return explode(',', $payments);
    }
    
    /**
     * 
     * @return boolean
     */
    public function isMultiPaymentEnabled()
    {
        return ($this->scopeConfig->getValue('payment/multipaymentforpos/active', ScopeInterface::SCOPE_STORE) && $this->isAllowOnWebPOS('multipaymentforpos'));
    }
    
    /**
     * 
     * @param string $code
     * @return boolean
     */
    public function isAllowOnWebPOS($code)
    {
        if ($this->scopeConfig->getValue('webpos/payment/allowspecific_payment', ScopeInterface::SCOPE_STORE) == '1') {
            $specificpayment = $this->scopeConfig->getValue('webpos/payment/specificpayment', ScopeInterface::SCOPE_STORE);
            $specificpayment = explode(',', $specificpayment);
            if (in_array($code, $specificpayment)) {
                return true;
            } else {
                return false;
            }
        }
        return true;
    }
    
    /**
     * 
     * @return string
     */
    public function getDefaultPaymentMethod()
    {
        return $this->scopeConfig->getValue('webpos/payment/defaultpayment', ScopeInterface::SCOPE_STORE);
    }
    

    /**
     * Check webpos payment
     *
     * @param string
     * @return boolean
     */
    public function isWebposPayment($code)
    {
        $payments = array('multipaymentforpos','cashforpos','ccforpos','codforpos');
        return in_array($code, $payments);
    }

    /**
     * Check webpos payment is pay later
     *
     * @param string
     * @return boolean
     */
    public function isPayLater($code)
    {
        $isPayLater = $this->scopeConfig->getValue('payment/'.$code.'/pay_later', ScopeInterface::SCOPE_STORE);
        return $isPayLater;
    }

    /**
     * Check webpos payment is pay later
     *
     * @param string
     * @return boolean
     */
    public function isReferenceNumber($code)
    {
        $isReferenceNumber = $this->scopeConfig->getValue('payment/'.$code.'/use_reference_number', ScopeInterface::SCOPE_STORE);
        return $isReferenceNumber;
    }

    /**
     * Check webpos paypal enable
     *
     * @param string
     * @return boolean
     */
    public function isPaypalEnable()
    {
        $isPaypalEnable = $this->scopeConfig->getValue('webpos/payment/paypal/enable', ScopeInterface::SCOPE_STORE);
        return $isPaypalEnable;
    }

    /**
     * get use cvv
     * @return string
     */
    public function useCvv($code)
    {
        $useCvv = $this->scopeConfig->getValue('payment/'.$code.'/useccv', ScopeInterface::SCOPE_STORE);
        return $useCvv;
    }

    /**
     * @return bool
     */
    public function isRetailerPos()
    {
        if(isset($_SERVER['HTTP_USER_AGENT'])) {
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            if ((strpos(strtolower($userAgent), 'ipad')!==false || strpos(strtolower($userAgent), 'android')!==false)
                && (!strpos(strtolower($userAgent), 'mozilla')!==false)
            ) {
                return true;
            }
        }
        return false;
    }

}
