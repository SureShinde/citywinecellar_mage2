<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Helper;

use \Magento\Store\Model\ScopeInterface;

/**
 * Helper Payment
 */
class Payment extends Data
{

    /**
     * Payment title
     *
     * @param string $code
     * @return string
     */
    public function getPaymentTitle($code)
    {
        $title = $this->scopeConfig->getValue('payment/' . $code . '/title', ScopeInterface::SCOPE_STORE);
        return $title;
    }

    /**
     * Get title of Cash payment method
     *
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
     * Is Cash Payment Enabled
     *
     * @return boolean
     */
    public function isCashPaymentEnabled()
    {
        return (
            $this->scopeConfig->getValue(
                'payment/cashforpos/active',
                ScopeInterface::SCOPE_STORE
            ) && $this->isAllowOnWebPOS('cashforpos')
        );
    }

    /**
     * Get Cc Method Title
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
     * Is Cc Payment Enabled
     *
     * @return boolean
     */
    public function isCcPaymentEnabled()
    {
        return (
            $this->scopeConfig->getValue(
                'payment/ccforpos/active',
                ScopeInterface::SCOPE_STORE
            ) && $this->isAllowOnWebPOS('ccforpos')
        );
    }

    /**
     * Is Webpos Shipping Enabled
     *
     * @return string
     */
    public function isWebposShippingEnabled()
    {
        return $this->scopeConfig->getValue('carriers/webpos_shipping/active', ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Cod Method Title
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
     * Is Cod Payment Enabled
     *
     * @return boolean
     */
    public function isCodPaymentEnabled()
    {
        return (
            $this->scopeConfig->getValue(
                'payment/codforpos/active',
                ScopeInterface::SCOPE_STORE
            ) && $this->isAllowOnWebPOS('codforpos')
        );
    }

    /**
     * Get Multipayment Method Title
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
     * Get Multipayment Active Method Title
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
     * Is Multi Payment Enabled
     *
     * @return boolean
     */
    public function isMultiPaymentEnabled()
    {
        return (
            $this->scopeConfig->getValue(
                'payment/multipaymentforpos/active',
                ScopeInterface::SCOPE_STORE
            ) && $this->isAllowOnWebPOS('multipaymentforpos')
        );
    }

    /**
     * Is Allow On WebPOS
     *
     * @param string $code
     * @return boolean
     */
    public function isAllowOnWebPOS($code)
    {
        if ($this->scopeConfig->getValue('webpos/payment/allowspecific_payment', ScopeInterface::SCOPE_STORE) == '1') {
            $specificpayment = $this->scopeConfig->getValue(
                'webpos/payment/specificpayment',
                ScopeInterface::SCOPE_STORE
            );
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
     * Get Default Payment Method
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
     * @param string $code
     * @return boolean
     */
    public function isWebposPayment($code)
    {
        $payments = ['multipaymentforpos', 'cashforpos', 'ccforpos', 'codforpos'];
        return in_array($code, $payments);
    }

    /**
     * Check webpos payment is pay later
     *
     * @param string $code
     * @return boolean
     */
    public function isPayLater($code)
    {
        $isPayLater = $this->scopeConfig->getValue('payment/' . $code . '/pay_later', ScopeInterface::SCOPE_STORE);
        return $isPayLater;
    }

    /**
     * Check webpos payment is pay later
     *
     * @param string $code
     * @return boolean
     */
    public function isReferenceNumber($code)
    {
        $isReferenceNumber = $this->scopeConfig->getValue(
            'payment/' . $code . '/use_reference_number',
            ScopeInterface::SCOPE_STORE
        );
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
     * Get use cvv
     *
     * @param string $code
     * @return mixed
     */
    public function useCvv($code)
    {
        $useCvv = $this->scopeConfig->getValue('payment/' . $code . '/useccv', ScopeInterface::SCOPE_STORE);
        return $useCvv;
    }

    /**
     * Is Retailer Pos
     *
     * @return bool
     */
    public function isRetailerPos()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Framework\App\RequestInterface $request */
        $request = $objectManager->get(\Magento\Framework\App\RequestInterface::class);
        if ($request->getServer('HTTP_USER_AGENT') !== null) {
            $userAgent = $request->getServer('HTTP_USER_AGENT');
            if ((strpos(strtolower($userAgent), 'ipad') !== false
                    || strpos(strtolower($userAgent), 'android') !== false)
                && (!strpos(strtolower($userAgent), 'mozilla') !== false)
            ) {
                return true;
            }
        }
        return false;
    }
}
