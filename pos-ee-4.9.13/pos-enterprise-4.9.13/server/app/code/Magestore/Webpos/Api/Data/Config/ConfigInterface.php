<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Config;

    /**
     * @api
     */
/**
 * Interface ConfigInterface
 * @package Magestore\Webpos\Api\Data\Config
 */
interface ConfigInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const SETTINGS = 'settings';
    const CURRENCIES = 'currencies';
    const BASE_CURRENCY_CODE = 'base_currency_code';
    const CURRENT_CURRENCY_CODE = 'current_currency_code';
    const PRICE_FORMATS = 'price_formats';
    const GUEST_CUSTOMER = 'guest_customer';
    const STORE_NAME = 'store_name';
    const CUSTOMER_GROUPS = 'customer_groups';
    const SHIPPING = 'shipping';
    const PAYMENT = 'payment';
    const PERMISSIONS = 'permissions';
    const TAX_RATES = 'tax_rates';
    const TAX_RULES = 'tax_rules';
    const PRODUCT_TAX_CLASSES = 'product_tax_classes';
    const IS_PRIMARY_LOCATION = 'is_primary_location';
    const DENOMINATIONS = 'denominations';
    const CUSTOM_SALE_PRODUCT_ID = 'custom_sale_product_id';
    const ENABLE_MODULES = 'enable_modules';
    const MAX_DISCOUNT_PERCENT = 'max_discount_percent';
    const ROOT_CATEGORY_ID = 'ROOT_CATEGORY_ID';
    const CUSTOMER_FORM = 'customer_form';
    const CUSTOMER_ADDRESS_FORM = 'customer_address_form';
    const COUNTRIES_WITH_OPTIONAL_ZIP = 'countries_with_optional_zip';
    const MAGENTO_VERSION = 'magento_version';
    const IS_WEBPOS_STANDARD = 'is_wedpos_standard';
    const SALES_CHANNELS = 'sales_channels';
    const LOCATIONS = 'locations';
    const STORES = 'stores';
    const WEBSITES = 'websites';
    const SERVER_TIMEZONE_OFFSET = 'server_timezone_offset';
    const PAYMENT_TYPE = 'payment_type';
    const REFUND_PAYMENT_TYPE = 'refund_payment_type';
    const CUSTOMER_CUSTOM_ATTRIBUTES = 'customer_custom_attributes';
    const CUSTOMER_ADDRESS_CUSTOM_ATTRIBUTES = 'customer_address_custom_attributes';

    /**
     * @return mixed[]
     */
    public function getPermissions();

    /**
     * @param mixed[] $permissions
     * @return ConfigInterface
     */
    public function setPermissions($permissions);

    /**
     * Get settings
     *
     * @api
     * @return \Magestore\Webpos\Api\Data\Config\SystemConfigInterface[]
     */
    public function getSettings();

    /**
     * Set settings
     *
     * @api
     * @param \Magestore\Webpos\Api\Data\Config\SystemConfigInterface[] $settings
     * @return ConfigInterface
     */
    public function setSettings($settings);

    /**
     * Get currencies
     *
     * @api
     * @return \Magestore\Webpos\Api\Data\Config\CurrencyInterface[]
     */
    public function getCurrencies();

    /**
     * Set currencies
     *
     * @api
     * @param \Magestore\Webpos\Api\Data\Config\CurrencyInterface[] $currencies
     * @return ConfigInterface
     */
    public function setCurrencies($currencies);

    /**
     * Get base currency code
     *
     * @api
     * @return string
     */
    public function getBaseCurrencyCode();

    /**
     * Set base currency code
     *
     * @api
     * @param string $baseCurrencyCode
     * @return ConfigInterface
     */
    public function setBaseCurrencyCode($baseCurrencyCode);

    /**
     * Get current currency code
     *
     * @api
     * @return string
     */
    public function getCurrentCurrencyCode();

    /**
     * Set current currency code
     *
     * @api
     * @param string $currentCurrencyCode
     * @return ConfigInterface
     */
    public function setCurrentCurrencyCode($currentCurrencyCode);

    /**
     * Get price format
     *
     * @api
     * @return \Magestore\Webpos\Api\Data\Config\PriceFormatInterface[]
     */
    public function getPriceFormats();

    /**
     * Set price format
     *
     * @api
     * @param \Magestore\Webpos\Api\Data\Config\PriceFormatInterface[] $priceFormats
     * @return ConfigInterface
     */
    public function setPriceFormats($priceFormats);

    /**
     * Get price format
     *
     * @api
     * @return \Magestore\Webpos\Api\Data\Config\GuestCustomerInterface
     */
    public function getGuestCustomer();

    /**
     * Set price format
     *
     * @api
     * @param \Magestore\Webpos\Api\Data\Config\GuestCustomerInterface $guestInfo
     * @return ConfigInterface
     */
    public function setGuestCustomer($guestInfo);

    /**
     * @return string
     */
    public function getStoreName();

    /**
     * @param string $storeName
     * @return ConfigInterface
     */
    public function setStoreName($storeName);

    /**
     * @return \Magestore\Webpos\Api\Data\Config\CustomerGroupInterface[]
     */
    public function getCustomerGroups();

    /**
     * @param \Magestore\Webpos\Api\Data\Config\CustomerGroupInterface[] $customerGroups
     * @return ConfigInterface
     */
    public function setCustomerGroups($customerGroups);

    /**
     * @return \Magestore\Webpos\Api\Data\Config\ShippingInterface
     */
    public function getShipping();

    /**
     * @param \Magestore\Webpos\Api\Data\Config\ShippingInterface $shipping
     * @return ConfigInterface
     */
    public function setShipping($shipping);

    /**
     * @return \Magestore\Webpos\Api\Data\Config\PaymentInterface
     */
    public function getPayment();

    /**
     * @param \Magestore\Webpos\Api\Data\Config\PaymentInterface $payment
     * @return ConfigInterface
     */
    public function setPayment($payment);

    /**
     * @return \Magestore\Webpos\Api\Data\Tax\TaxRateInterface[]
     */
    public function getTaxRates();

    /**
     * @param \Magestore\Webpos\Api\Data\Tax\TaxRateInterface[] $taxRates
     * @return ConfigInterface
     */
    public function setTaxRates($taxRates);

    /**
     * @return \Magestore\Webpos\Api\Data\Tax\TaxRuleInterface[]
     */
    public function getTaxRules();

    /**
     * @param \Magestore\Webpos\Api\Data\Tax\TaxRuleInterface[] $taxRules
     * @return ConfigInterface
     */
    public function setTaxRules($taxRules);

    /**
     * @return \Magestore\Webpos\Api\Data\Config\ProductTaxClassesInterface[]
     */
    public function getProductTaxClasses();

    /**
     * @param \Magestore\Webpos\Api\Data\Config\ProductTaxClassesInterface[] $taxClasses
     * @return ConfigInterface
     */
    public function setProductTaxClasses($taxClasses);

    /**
     * @return boolean
     */
    public function getIsPrimaryLocation();

    /**
     * @param boolean $isPrimaryLocation
     * @return ConfigInterface
     */
    public function setIsPrimaryLocation($isPrimaryLocation);
    /**
     * Get denominations
     *
     * @api
     * @return \Magestore\Webpos\Api\Data\Denomination\DenominationInterface[]|null
     */
    public function getDenominations();

    /**
     * Set denominations
     *
     * @api
     * @param \Magestore\Webpos\Api\Data\Denomination\DenominationInterface[]|null $denominations
     * @return ConfigInterface
     */
    public function setDenominations($denominations);

    /**
     * Get custom sale product id
     *
     * @return int|null
     */
    public function getCustomSaleProductId();

    /**
     * Set custom sale product id
     *
     * @api
     * @param int|null $customSaleProductId
     * @return ConfigInterface
     */
    public function setCustomSaleProductId($customSaleProductId);

    /**
     * Get enable modules
     *
     * @api
     * @return string[]
     */
    public function getEnableModules();

    /**
     * Set enable modules
     *
     * @api
     * @param string[] $enableModules
     * @return ConfigInterface
     */
    public function setEnableModules($enableModules);

    /**
     * Get max discount percent
     * @return float
     */
    public function getMaxDiscountPercent();

    /**
     * @param $maxDiscountPercent
     * @return ConfigInterface
     */
    public function setMaxDiscountPercent($maxDiscountPercent);

    /**
     * Get root category
     *
     * @api
     * @return int|null
     */
    public function getRootCategoryId();

    /**
     * Set root category
     *
     * @api
     * @param int $rootCategoryId
     * @return ConfigInterface
     */
    public function setRootCategoryId($rootCategoryId);

    /**
     * @return \Magento\Customer\Api\Data\AttributeMetadataInterface[]
     */
    public function getCustomerForm();

    /**
     * @param \Magento\Customer\Api\Data\AttributeMetadataInterface[] $customerForm
     * @return ConfigInterface
     */
    public function setCustomerForm($customerForm);

    /**
     * @return \Magento\Customer\Api\Data\AttributeMetadataInterface[]
     */
    public function getCustomerAddressForm();

    /**
     * @param \Magento\Customer\Api\Data\AttributeMetadataInterface[] $customerAddressForm
     * @return ConfigInterface
     */
    public function setCustomerAddressForm($customerAddressForm);

    /**
     * @return \Magento\Customer\Api\Data\AttributeMetadataInterface[]
     */
    public function getCustomerCustomAttributes();

    /**
     * @param \Magento\Customer\Api\Data\AttributeMetadataInterface[] $customerCustomAttributes
     * @return ConfigInterface
     */
    public function setCustomerCustomAttributes($customerCustomAttributes);

    /**
     * @return \Magento\Customer\Api\Data\AttributeMetadataInterface[]
     */
    public function getCustomerAddressCustomAttributes();

    /**
     * @param \Magento\Customer\Api\Data\AttributeMetadataInterface[] $customerAddressCustomAttributes
     * @return ConfigInterface
     */
    public function setCustomerAddressCustomAttributes($customerAddressCustomAttributes);

    /**
     * @return string
     */
    public function getCountriesWithOptionalZip();

    /**
     * @param string $countriesWithOptionalZip
     * @return ConfigInterface
     */
    public function setCountriesWithOptionalZip($countriesWithOptionalZip);

    /**
     * @return string
     */
    public function getMagentoVersion();

    /**
     * @param string $magentoVersion
     * @return ConfigInterface
     */
    public function setMagentoVersion($magentoVersion);

    /**
     * @return boolean
     */
    public function getIsWebposStandard();

    /**
     * @param boolean $isWebposStandard
     * @return ConfigInterface
     */
    public function setIsWebposStandard($isWebposStandard);


    /**
     * @return \Magestore\Webpos\Api\Data\Config\LocationInterface[]|null
     */
    public function getLocations();

    /**
     * @param \Magestore\Webpos\Api\Data\Config\LocationInterface[]|null $locations
     * @return ConfigInterface
     */
    public function setLocations($locations);

    /**
     * @return \Magestore\Webpos\Api\Data\InventorySales\SalesChannelInterface[]|null
     */
    public function getSalesChannels();

    /**
     * @param \Magestore\Webpos\Api\Data\InventorySales\SalesChannelInterface[]|null $salesChannel
     * @return ConfigInterface
     */
    public function setSalesChannels($salesChannels);

    /**
     * @return \Magento\Store\Api\Data\StoreInterface[]|null
     */
    public function getStores();

    /**
     * @param \Magento\Store\Api\Data\StoreInterface[]|null $stores
     * @return ConfigInterface
     */
    public function setStores($stores);

    /**
     * @return \Magento\Store\Api\Data\WebsiteInterface[]|null
     */
    public function getWebsites();

    /**
     * @param \Magento\Store\Api\Data\WebsiteInterface[]|null $websites
     * @return ConfigInterface
     */
    public function setWebsites($websites);

    /**
     * @return int
     */
    public function getServerTimezoneOffset();

    /**
     * @param int $serverTimezoneOffset
     * @return ConfigInterface
     */
    public function setServerTimezoneOffset($serverTimezoneOffset);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Config\ConfigExtensionInterface|null
     * @since 102.0.0
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Config\ConfigExtensionInterface $extensionAttributes
     * @return $this
     * @since 102.0.0
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Config\ConfigExtensionInterface $extensionAttributes
    );

    /**
     * @return \Magestore\Webpos\Api\Data\Config\PaymentTypeInterface
     */
    public function getPaymentType();

    /**
     * @param \Magestore\Webpos\Api\Data\Config\PaymentTypeInterface $paymentType
     * @return ConfigInterface
     */
    public function setPaymentType($paymentType);

    /**
     * @return \Magestore\Webpos\Api\Data\Config\RefundPaymentTypeInterface
     */
    public function getRefundPaymentType();

    /**
     * @param \Magestore\Webpos\Api\Data\Config\RefundPaymentTypeInterface $value
     * @return ConfigInterface
     */
    public function setRefundPaymentType($value);
}
