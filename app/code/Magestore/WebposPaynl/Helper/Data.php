<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposPaynl\Helper;

/**
 * Helper Data
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_dateTime;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
    ) {
        $this->_storeManager = $storeManager;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_localeDate = $localeDate;
        $this->_dateTime = $dateTime;
        parent::__construct($context);
    }

    /**
     * Get Store
     *
     * @return \Magento\Store\Api\Data\StoreInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStore()
    {
        return $this->_storeManager->getStore();
    }

    /**
     * FormatCurrency
     *
     * @param string $data
     * @return string
     */
    public function formatCurrency($data)
    {
        $currencyHelper = $this->_objectManager->get(\Magento\Framework\Pricing\Helper\Data::class);
        return $currencyHelper->currency($data, true, false);
    }

    /**
     * Format Price
     *
     * @param string $data
     * @return string
     */
    public function formatPrice($data)
    {
        $checkoutHelper = $this->_objectManager->get(\Magento\Checkout\Helper\Data::class);
        return $checkoutHelper->formatPrice($data);
    }

    /**
     * Format Date
     *
     * @param string $data
     * @param string $format
     * @return string
     */
    public function formatDate($data, $format = '')
    {
        $format = ($format == '') ? 'M d,Y H:i:s a' : $format;
        return $this->_localeDate->date(new \DateTime($data))->format($format);
    }

    /**
     * GetStoreConfig
     *
     * @param string $path
     * @return string
     */
    public function getStoreConfig($path)
    {
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Current Datetime
     *
     * @return string
     */
    public function getCurrentDatetime()
    {
        return $this->_dateTime->gmtDate();
    }

    /**
     * String class name
     *
     * @param string $class
     * @return \Magento\Framework\DataObject
     */
    public function getModel($class)
    {
        return $this->_objectManager->get($class);
    }

    /**
     * HtmlEscape
     *
     * @param string $str
     * @return string
     */
    public function htmlEscape($str)
    {
        /** @var \Magento\Framework\Escaper $escaper */
        $escaper = $this->_objectManager->get(\Magento\Framework\Escaper::class);
        return $escaper->escapeHtml($str);
    }

    /**
     * GetPaynlConfig
     *
     * @return array
     */
    public function getPaynlConfig()
    {
        $configData = [];
        $configItems = [
            'enable',
            'client_id',
            'client_secret',
            'is_sandbox',
        ];
        foreach ($configItems as $configItem) {
            $configData[$configItem] = $this->getStoreConfig('webpos/payment/paynl/' . $configItem);
        }
        return $configData;
    }

    /**
     * Is Allow Customer Pay With Email
     *
     * @return bool
     */
    public function isAllowCustomerPayWithEmail()
    {
        $enable = $this->getStoreConfig('webpos/payment/paynl/enable_send_invoice');
        return ($enable) ? true : false;
    }

    /**
     * Is Enable Paynl
     *
     * @return bool
     */
    public function isEnablePaynl()
    {
        $enable = $this->getStoreConfig('webpos/payment/paynl/enable');
        return ($enable) ? true : false;
    }

    /**
     * Is Allow Paypal Here
     *
     * @return bool
     */
    public function isAllowPaypalHere()
    {
        $enable = $this->getStoreConfig('webpos/payment/paynl/enable');
        return ($enable) ? true : false;
    }

    /**
     * Get Merchant Info
     *
     * @return array
     */
    public function getMerchantInfo()
    {
        $configData = [];
        $configItems = [
            'email',
            'firstname',
            'lastname',
            'buisiness_name',
            'phone',
            'street',
            'city',
            'state',
            'postal_code',
            'country_id'
        ];
        foreach ($configItems as $configItem) {
            $configData[$configItem] = $this->getStoreConfig(
                'webpos/payment/paypal/merchant_infomation/' . $configItem
            );
        }
        return $configData;
    }

    /**
     * Get Logo Url
     *
     * @return string
     */
    public function getLogoUrl()
    {
        $helper = $this->_objectManager->get(\Magestore\Webpos\Helper\Data::class);
        $url = $helper->getWebPosImages();
        return (strpos($url, 'https') === false) ? '' : $url;
    }

    /**
     * Is Tax Calculated After Discount
     *
     * @return bool
     */
    public function isTaxCalculatedAfterDiscount()
    {
        $configData = $this->getStoreConfig('tax/calculation/apply_after_discount');
        return ($configData == 1) ? true : false;
    }

    /**
     * Get Url
     *
     * @param string $path
     * @param array $params
     * @return string
     */
    public function getUrl($path, $params = [])
    {
        return $this->_getUrl($path, $params);
    }

    /**
     * Add Log
     *
     * @param string $message
     * @param string $type
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function addLog($message, $type = '')
    {
        switch ($type) {
            case 'info':
                $this->_logger->info($message);
                break;
            case 'debug':
                $this->_logger->debug($message);
                break;
            case 'info':
                $this->_logger->info($message);
                break;
            case 'notice':
                $this->_logger->notice($message);
                break;
            case 'warning':
                $this->_logger->warning($message);
                break;
            case 'error':
                $this->_logger->error($message);
                break;
            case 'emergency':
                $this->_logger->emergency($message);
                break;
            case 'critical':
                $this->_logger->critical($message);
                break;
            case 'alert':
                $this->_logger->alert($message);
                break;
            default:
                $this->_logger->error($message);
                break;
        }
    }

    /**
     * Validate Required SDK
     *
     * @return bool
     */
    public function validateRequiredSDK()
    {
        return (class_exists(\Paynl\Instore::class)) ? true : false;
    }
}
