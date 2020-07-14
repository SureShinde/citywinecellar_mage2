<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Config;

use Magestore\Webpos\Api\Data\Config\ConfigInterface;

/**
 * Class Config
 * @package Magestore\Webpos\Model\Config
 */
class Config extends \Magento\Framework\DataObject implements \Magestore\Webpos\Api\Data\Config\ConfigInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * Config constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        array $data = []
    )
    {
        parent::__construct($data);
        $this->scopeConfig = $scopeConfig;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @inheritdoc
     */
    public function getPermissions()
    {
        return $this->getData(self::PERMISSIONS);
    }

    /**
     * @inheritdoc
     */
    public function setPermissions($permissions)
    {
        return $this->setData(self::PERMISSIONS, $permissions);
    }

    /**
     * Get settings
     *
     * @api
     * @return \Magestore\Webpos\Api\Data\Config\SystemConfigInterface[]
     */
    public function getSettings()
    {
        return $this->getData(self::SETTINGS);
    }

    /**
     * Set settings
     *
     * @api
     * @param \Magestore\Webpos\Api\Data\Config\SystemConfigInterface[] $settings
     * @return $this
     */
    public function setSettings($settings)
    {
        return $this->setData(self::SETTINGS, $settings);
    }

    /**
     * Get currencies
     *
     * @api
     * @return \Magestore\Webpos\Api\Data\Config\CurrencyInterface[]
     */
    public function getCurrencies()
    {
        return $this->getData(self::CURRENCIES);
    }

    /**
     * Set currencies
     *
     * @api
     * @param \Magestore\Webpos\Api\Data\Config\CurrencyInterface[] $currencies
     * @return $this
     */
    public function setCurrencies($currencies)
    {
        return $this->setData(self::CURRENCIES, $currencies);
    }

    /**
     * Get base currency code
     *
     * @api
     * @return string
     */
    public function getBaseCurrencyCode()
    {
        return $this->getData(self::BASE_CURRENCY_CODE);
    }

    /**
     * Set base currency code
     *
     * @api
     * @param string $baseCurrencyCode
     * @return $this
     */
    public function setBaseCurrencyCode($baseCurrencyCode)
    {
        return $this->setData(self::BASE_CURRENCY_CODE, $baseCurrencyCode);
    }

    /**
     * Get current currency code
     *
     * @api
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        return $this->getData(self::CURRENT_CURRENCY_CODE);
    }

    /**
     * Set current currency code
     *
     * @api
     * @param string $currentCurrencyCode
     * @return $this
     */
    public function setCurrentCurrencyCode($currentCurrencyCode)
    {
        return $this->setData(self::CURRENT_CURRENCY_CODE, $currentCurrencyCode);
    }

    /**
     * Get price format
     *
     * @api
     * @return \Magestore\Webpos\Api\Data\Config\PriceFormatInterface[]
     */
    public function getPriceFormats()
    {
        return $this->getData(self::PRICE_FORMATS);
    }

    /**
     * Set price format
     *
     * @api
     * @param \Magestore\Webpos\Api\Data\Config\PriceFormatInterface[] $priceFormats
     * @return $this
     */
    public function setPriceFormats($priceFormats)
    {
        return $this->setData(self::PRICE_FORMATS, $priceFormats);
    }

    /**
     * Get price format
     *
     * @api
     * @return \Magestore\Webpos\Api\Data\Config\GuestCustomerInterface
     */
    public function getGuestCustomer()
    {
        return $this->getData(self::GUEST_CUSTOMER);
    }

    /**
     * Set price format
     *
     * @api
     * @param \Magestore\Webpos\Api\Data\Config\GuestCustomerInterface $guestInfo
     * @return $this
     */
    public function setGuestCustomer($guestInfo)
    {
        return $this->setData(self::GUEST_CUSTOMER, $guestInfo);
    }

    /**
     * @return string
     */
    public function getStoreName()
    {
        return $this->scopeConfig->getValue('general/store_information/name');
    }

    /**
     * @param string $storeName
     * @return $this
     */
    public function setStoreName($storeName)
    {
        return $this->setData(self::STORE_NAME, $storeName);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerGroups()
    {
        return $this->getData(self::CUSTOMER_GROUPS);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerGroups($customerGroups)
    {
        return $this->setData(self::CUSTOMER_GROUPS, $customerGroups);
    }

    /**
     * @inheritdoc
     */
    public function getShipping()
    {
        return $this->getData(self::SHIPPING);
    }

    /**
     * @inheritdoc
     */
    public function setShipping($shipping)
    {
        return $this->setData(self::SHIPPING, $shipping);
    }

    /**
     * @inheritdoc
     */
    public function getPayment()
    {
        return $this->getData(self::PAYMENT);
    }

    /**
     * @inheritdoc
     */
    public function setPayment($payment)
    {
        return $this->setData(self::PAYMENT, $payment);
    }

    /**
     * @return \Magestore\Webpos\Api\Data\Tax\TaxRateInterface[]
     */
    public function getTaxRates()
    {
        return $this->getData(self::TAX_RATES);
    }

    /**
     * @param \Magestore\Webpos\Api\Data\Tax\TaxRateInterface[] $taxRates
     * @return $this
     */
    public function setTaxRates($taxRates)
    {
        return $this->setData(self::TAX_RATES, $taxRates);
    }

    /**
     * @return \Magestore\Webpos\Api\Data\Tax\TaxRateInterface[]
     */
    public function getTaxRules()
    {
        return $this->getData(self::TAX_RULES);
    }

    /**
     * @param \Magestore\Webpos\Api\Data\Tax\TaxRuleInterface[] $taxRules
     * @return $this
     */
    public function setTaxRules($taxRules)
    {
        return $this->setData(self::TAX_RULES, $taxRules);
    }

    /**
     * {@inheritDoc}
     */
    public function getProductTaxClasses()
    {
        return $this->_getData(self::PRODUCT_TAX_CLASSES);
    }

    /**
     * {@inheritDoc}
     */
    public function setProductTaxClasses($taxClasses)
    {
        return $this->setData(self::PRODUCT_TAX_CLASSES, $taxClasses);
    }

    /**
     * @return boolean
     */
    public function getIsPrimaryLocation()
    {
        return $this->_getData(self::IS_PRIMARY_LOCATION);
    }

    /**
     * @param boolean $isPrimaryLocation
     * @return $this
     */
    public function setIsPrimaryLocation($isPrimaryLocation)
    {
        return $this->setData(self::IS_PRIMARY_LOCATION, $isPrimaryLocation);
    }

    /**
     * Get denominations
     *
     * @api
     * @return \Magestore\Webpos\Api\Data\Denomination\DenominationInterface[]|null
     */
    public function getDenominations()
    {
        return $this->getData(self::DENOMINATIONS);
    }

    /**
     * Set denominations
     *
     * @api
     * @param \Magestore\Webpos\Api\Data\Denomination\DenominationInterface[]|null $denominations
     * @return ConfigInterface
     */
    public function setDenominations($denominations)
    {
        return $this->setData(self::DENOMINATIONS, $denominations);
    }

    /**
     * Get custom sale product id
     *
     * @return int|null
     */
    public function getCustomSaleProductId()
    {
        return $this->getData(self::CUSTOM_SALE_PRODUCT_ID);
    }

    /**
     * Set denominations
     *
     * @api
     * @param int|null $customSaleProductId
     * @return ConfigInterface
     */
    public function setCustomSaleProductId($customSaleProductId)
    {
        return $this->setData(self::CUSTOM_SALE_PRODUCT_ID, $customSaleProductId);
    }


    /**
     * Get enable modules
     *
     * @api
     * @return string[]
     */
    public function getEnableModules()
    {
        return $this->getData(self::ENABLE_MODULES);
    }

    /**
     * Set enable modules
     *
     * @api
     * @param string[] $enableModules
     * @return ConfigInterface
     */
    public function setEnableModules($enableModules)
    {
        return $this->setData(self::ENABLE_MODULES, $enableModules);
    }

    /**
     * Get max discount percent
     * @return float
     */
    public function getMaxDiscountPercent()
    {
        return $this->getData(self::MAX_DISCOUNT_PERCENT);
    }

    /**
     * @param $maxDiscountPercent
     * @return ConfigInterface
     */
    public function setMaxDiscountPercent($maxDiscountPercent)
    {
        return $this->setData(self::MAX_DISCOUNT_PERCENT, $maxDiscountPercent);
    }

    /**
     * @return \Magento\Customer\Api\Data\AttributeMetadataInterface[]
     */
    public function getCustomerForm()
    {
        return $this->getData(self::CUSTOMER_FORM);
    }

    /**
     * @param \Magento\Customer\Api\Data\AttributeMetadataInterface[] $customerForm
     * @return ConfigInterface
     */
    public function setCustomerForm($customerForm)
    {
        return $this->setData(self::CUSTOMER_FORM, $customerForm);
    }

    /**
     * @return \Magento\Customer\Api\Data\AttributeMetadataInterface[]
     */
    public function getCustomerAddressForm()
    {
        return $this->getData(self::CUSTOMER_ADDRESS_FORM);
    }

    /**
     * @param \Magento\Customer\Api\Data\AttributeMetadataInterface[] $customerAddressForm
     * @return ConfigInterface
     */
    public function setCustomerAddressForm($customerAddressForm)
    {
        return $this->setData(self::CUSTOMER_ADDRESS_FORM, $customerAddressForm);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerCustomAttributes()
    {
        return $this->getData(self::CUSTOMER_CUSTOM_ATTRIBUTES);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerCustomAttributes($customerCustomAttributes)
    {
        return $this->setData(self::CUSTOMER_CUSTOM_ATTRIBUTES, $customerCustomAttributes);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerAddressCustomAttributes()
    {
        return $this->getData(self::CUSTOMER_ADDRESS_CUSTOM_ATTRIBUTES);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerAddressCustomAttributes($customerAddressCustomAttributes)
    {
        return $this->setData(self::CUSTOMER_ADDRESS_CUSTOM_ATTRIBUTES, $customerAddressCustomAttributes);
    }

    /**
     * Get root category
     *
     * @api
     * @return int|null
     */
    public function getRootCategoryId()
    {
        return $this->getData(self::ROOT_CATEGORY_ID);
    }

    /**
     * Set root category
     *
     * @api
     * @param int $rootCategoryId
     * @return ConfigInterface
     */
    public function setRootCategoryId($rootCategoryId)
    {
        return $this->setData(self::ROOT_CATEGORY_ID, $rootCategoryId);
    }

    /**
     * @return string
     */
    public function getCountriesWithOptionalZip()
    {
        return $this->getData(self::COUNTRIES_WITH_OPTIONAL_ZIP);
    }

    /**
     * @param string $countriesWithOptionalZip
     * @return ConfigInterface
     */
    public function setCountriesWithOptionalZip($countriesWithOptionalZip)
    {
        return $this->setData(self::COUNTRIES_WITH_OPTIONAL_ZIP, $countriesWithOptionalZip);
    }

    /**
     * @return string
     */
    public function getMagentoVersion()
    {
        if ($version = $this->getData(self::MAGENTO_VERSION)) {
            return $version;
        }
        return $this->productMetadata->getVersion();
    }

    /**
     * @param string $countriesWithOptionalZip
     * @return ConfigInterface
     */
    public function setMagentoVersion($magentoVersion)
    {
        return $this->setData(self::MAGENTO_VERSION, $magentoVersion);
    }

    /**
     * @return bool|void
     */
    public function getIsWebposStandard()
    {
        return $this->getData(self::IS_WEBPOS_STANDARD);
    }

    /**
     * @param bool $isWebposStandard
     * @return ConfigInterface|void
     */
    public function setIsWebposStandard($isWebposStandard)
    {
        return $this->setData(self::IS_WEBPOS_STANDARD, $isWebposStandard);
    }

    /**
     * @return \Magestore\Webpos\Api\Data\Config\LocationInterface[]|null
     */
    public function getLocations()
    {
        return $this->getData(self::LOCATIONS);
    }

    /**
     * @param \Magestore\Webpos\Api\Data\Config\LocationInterface[]|null $locations
     * @return ConfigInterface
     */
    public function setLocations($locations)
    {
        return $this->setData(self::LOCATIONS, $locations);
    }

    /**
     * @return \Magestore\Webpos\Api\Data\InventorySales\SalesChannelInterface[]|null
     */
    public function getSalesChannels()
    {
        return $this->getData(self::SALES_CHANNELS);
    }

    /**
     * @param \Magestore\Webpos\Api\Data\InventorySales\SalesChannelInterface[]|null $salesChannel
     * @return ConfigInterface
     */
    public function setSalesChannels($salesChannels)
    {
        return $this->setData(self::SALES_CHANNELS, $salesChannels);
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface[]|null
     */
    public function getStores()
    {
        return $this->getData(self::STORES);
    }

    /**
     * @param \Magento\Store\Api\Data\StoreInterface[]|null $stores
     * @return ConfigInterface
     */
    public function setStores($stores)
    {
        return $this->setData(self::STORES, $stores);
    }

    /**
     * @return \Magento\Store\Api\Data\WebsiteInterface[]|null
     */
    public function getWebsites()
    {
        return $this->getData(self::WEBSITES);
    }

    /**
     * @param \Magento\Store\Api\Data\WebsiteInterface[]|null $websites
     * @return ConfigInterface
     */
    public function setWebsites($websites)
    {
        return $this->setData(self::WEBSITES, $websites);
    }

    /**
     * @inheritdoc
     */
    public function getServerTimezoneOffset()
    {
        return $this->getData(self::SERVER_TIMEZONE_OFFSET);
    }

    /**
     * @inheritdoc
     */
    public function setServerTimezoneOffset($serverTimezoneOffset)
    {
        return $this->setData(self::SERVER_TIMEZONE_OFFSET, $serverTimezoneOffset);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Config\ConfigExtensionInterface|null
     * @since 102.0.0
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Config\ConfigExtensionInterface $extensionAttributes
     * @return $this
     * @since 102.0.0
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Config\ConfigExtensionInterface $extensionAttributes
    ){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }

    /**
     * @return \Magestore\Webpos\Api\Data\Config\PaymentTypeInterface
     */
    public function getPaymentType()
    {
        return $this->getData(self::PAYMENT_TYPE);
    }

    /**
     * @param \Magestore\Webpos\Api\Data\Config\PaymentTypeInterface $paymentType
     * @return ConfigInterface
     */
    public function setPaymentType($paymentType)
    {
        return $this->setData(self::PAYMENT_TYPE, $paymentType);
    }

    /**
     * @inheritdoc
     */
    public function getRefundPaymentType()
    {
        return $this->getData(self::REFUND_PAYMENT_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setRefundPaymentType($value)
    {
        return $this->setData(self::REFUND_PAYMENT_TYPE, $value);
    }
}
