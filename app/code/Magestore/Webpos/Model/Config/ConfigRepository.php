<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magestore\Payment\Model\Payment\PaymentType;
use Magestore\Payment\Model\Payment\RefundPaymentType;
use Magestore\Payment\Model\Payment\RefundPaymentTypeFactory;
use Magestore\Webpos\Api\Data\Config\RefundPaymentTypeInterface;
use Magestore\Webpos\Api\Data\Config\RefundPaymentTypeInterfaceFactory;

/**
 * Class ConfigRepository
 * @package Magestore\Webpos\Model\Config
 */
class ConfigRepository implements \Magestore\Webpos\Api\Config\ConfigRepositoryInterface
{
    /**
     * @var \Magestore\Webpos\Api\Data\Config\SystemConfigInterfaceFactory
     */
    protected $systemConfigFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magestore\Webpos\Api\Data\Config\ConfigInterface
     */
    protected $config;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $localeCurrency;

    /**
     * @var \Magento\Framework\Locale\FormatInterface
     */
    protected $localeFormat;

    /**
     * @var \Magestore\Webpos\Api\Data\Config\PriceFormatInterfaceFactory
     */
    protected $priceFormatFactory;

    /**
     * @var \Magestore\Webpos\Api\Data\Config\GuestCustomerInterface
     */
    protected $guestCustomer;

    /**
     * @var \Magestore\Webpos\Api\Data\Config\ShippingInterface
     */
    protected $shipping;
    /**
     * @var \Magestore\Webpos\Api\Data\Config\PaymentInterface
     */
    protected $payment;

    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $regionFactory;

    /**
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    protected $customerGroupRepository;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaInterface
     */
    protected $searchCriteria;

    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $helper;

    protected $request;

    protected $staffManagement;

    protected $staffRepository;

    protected $roleRepository;

    /**
     * @var \Magestore\Appadmin\Model\Staff\RoleFactory
     */
    protected $roleFactory;

    protected $ruleCollectionFactory;

    protected $aclResource;

    protected $taxRateRepository;

    protected $taxRuleRepository;

    /**
     * @var \Magento\Tax\Model\TaxClass\Source\Product
     */
    protected $productTaxClass;

    protected $productTaxClassFactory;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magestore\Webpos\Api\Location\LocationRepositoryInterface
     */
    protected $locationRepository;

    /**
     * @var \Magestore\Webpos\Api\Denomination\DenominationRepositoryInterface
     */
    protected $denominationRepository;

    /**
     * @var \Magestore\Webpos\Helper\Product\CustomSale $customSaleHelper
     */
    protected $customSaleHelper;


    /**#@-*/

    /**#@-*/
    protected $customerMetadataService;
    /**
     * @var
     */
    protected $addressMetadataService;

    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $directoryHelper;

    /**
     * @var \Magestore\Webpos\Api\WebposManagementInterface
     */
    protected $webposManagement;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilderFactory
     */
    protected $searchCriteriaBuilderFactory;

    /**
     * @var \Magestore\Webpos\Model\Config\Data\LocationFactory
     */
    protected $configLocationFactory;

    /**
     * @var \Magento\Store\Api\WebsiteRepositoryInterface
     */
    protected $websiteRepository;

    /**
     * @var \Magestore\Webpos\Model\InventorySales\SalesChannelFactory
     */
    protected $salesChannelFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magestore\Webpos\Api\Data\Config\PaymentTypeInterface
     */
    protected $paymentType;

    /**
     * @var RefundPaymentTypeInterfaceFactory
     */
    protected $refundPaymentTypeFactory;

    /**
     * @var PaymentType
     */
    protected $allPaymentType;

    /**
     * @var RefundPaymentTypeFactory
     */
    protected $allRefundPaymentTypeFactory;

    /**
     * @var \Magento\Store\Api\Data\StoreInterface
     */
    protected $currentStore;

    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Sales\Order
     */
    protected $orderResourceModel;

    /**
     * ConfigRepository constructor.
     * @param \Magestore\Webpos\Api\Data\Config\SystemConfigInterfaceFactory $systemConfigInterfaceFactory
     * @param \Magestore\Webpos\Api\Data\Config\ConfigInterface $config
     * @param \Magestore\Webpos\Api\Data\Config\PriceFormatInterfaceFactory $priceFormatFactory
     * @param \Magestore\Webpos\Api\Data\Config\GuestCustomerInterface $guestCustomer
     * @param \Magestore\Webpos\Api\Data\Config\ShippingInterface $shipping
     * @param \Magestore\Webpos\Api\Data\Config\PaymentInterface $payment
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Customer\Api\GroupRepositoryInterface $customerGroupRepository
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param \Magestore\Webpos\Helper\Data $helper
     * @param \Magento\Framework\Webapi\Rest\Request $request
     * @param \Magestore\Webpos\Api\Staff\StaffManagementInterface $staffManagement
     * @param \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface $staffRepository
     * @param \Magestore\Appadmin\Model\ResourceModel\Staff\AuthorizationRule\CollectionFactory $ruleCollectionFactory
     * @param \Magestore\Appadmin\Model\Staff\Acl\AclResource\Provider $aclResource
     * @param \Magestore\Webpos\Model\Tax\TaxRateRepository $taxRateRepository
     * @param \Magestore\Webpos\Model\Tax\TaxRuleRepository $taxRuleRepository
     * @param \Magento\Tax\Model\TaxClass\Source\Product $productTaxClass
     * @param \Magestore\Webpos\Api\Data\Config\ProductTaxClassesInterfaceFactory $productTaxClassFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magestore\Webpos\Api\Location\LocationRepositoryInterface $locationRepository
     * @param \Magestore\Webpos\Api\Denomination\DenominationRepositoryInterface $denominationRepository
     * @param \Magestore\Webpos\Helper\Product\CustomSale $customSaleHelper
     * @param \Magestore\Appadmin\Model\Staff\RoleFactory $roleFactory
     * @param \Magestore\Webpos\Model\Customer\CustomerMetadata $customerMetadataService
     * @param \Magestore\Webpos\Model\Customer\AddressMetadata $addressMetadataService
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magestore\Webpos\Api\WebposManagementInterface $webposManagement
     * @param \Magento\Framework\Api\SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param Data\LocationFactory $configLocationFactory
     * @param \Magento\Store\Api\WebsiteRepositoryInterface $websiteRepository
     * @param \Magestore\Webpos\Model\InventorySales\SalesChannelFactory $salesChannelFactory
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param PaymentType $allPaymentType
     * @param RefundPaymentTypeFactory $allRefundPaymentTypeFactory
     * @param \Magestore\Webpos\Api\Data\Config\PaymentTypeInterface $paymentType
     * @param \Magestore\Webpos\Api\Data\Config\RefundPaymentTypeInterfaceFactory $refundPaymentTypeFactory
     * @param \Magestore\Webpos\Model\ResourceModel\Sales\Order $orderResourceModel
     */
    public function __construct(
        \Magestore\Webpos\Api\Data\Config\SystemConfigInterfaceFactory $systemConfigInterfaceFactory,
        \Magestore\Webpos\Api\Data\Config\ConfigInterface $config,
        \Magestore\Webpos\Api\Data\Config\PriceFormatInterfaceFactory $priceFormatFactory,
        \Magestore\Webpos\Api\Data\Config\GuestCustomerInterface $guestCustomer,
        \Magestore\Webpos\Api\Data\Config\ShippingInterface $shipping,
        \Magestore\Webpos\Api\Data\Config\PaymentInterface $payment,
        ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Customer\Api\GroupRepositoryInterface $customerGroupRepository,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria,
        \Magestore\Webpos\Helper\Data $helper,
        \Magento\Framework\Webapi\Rest\Request $request,
        \Magestore\Webpos\Api\Staff\StaffManagementInterface $staffManagement,
        \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface $staffRepository,
        \Magestore\Appadmin\Model\ResourceModel\Staff\AuthorizationRule\CollectionFactory $ruleCollectionFactory,
        \Magestore\Appadmin\Model\Staff\Acl\AclResource\Provider $aclResource,
        \Magestore\Webpos\Model\Tax\TaxRateRepository $taxRateRepository,
        \Magestore\Webpos\Model\Tax\TaxRuleRepository $taxRuleRepository,
        \Magento\Tax\Model\TaxClass\Source\Product $productTaxClass,
        \Magestore\Webpos\Api\Data\Config\ProductTaxClassesInterfaceFactory $productTaxClassFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Registry $coreRegistry,
        \Magestore\Webpos\Api\Location\LocationRepositoryInterface $locationRepository,
        \Magestore\Webpos\Api\Denomination\DenominationRepositoryInterface $denominationRepository,
        \Magestore\Webpos\Helper\Product\CustomSale $customSaleHelper,
        \Magestore\Appadmin\Model\Staff\RoleFactory $roleFactory,
        \Magestore\Webpos\Model\Customer\CustomerMetadata $customerMetadataService,
        \Magestore\Webpos\Model\Customer\AddressMetadata $addressMetadataService,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magestore\Webpos\Api\WebposManagementInterface $webposManagement,
        \Magento\Framework\Api\SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        \Magestore\Webpos\Model\Config\Data\LocationFactory $configLocationFactory,
        \Magento\Store\Api\WebsiteRepositoryInterface $websiteRepository,
        \Magestore\Webpos\Model\InventorySales\SalesChannelFactory $salesChannelFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magestore\Payment\Model\Payment\PaymentType $allPaymentType,
        RefundPaymentTypeFactory $allRefundPaymentTypeFactory,
        \Magestore\Webpos\Api\Data\Config\PaymentTypeInterface $paymentType,
        RefundPaymentTypeInterfaceFactory $refundPaymentTypeFactory,
        \Magestore\Webpos\Model\ResourceModel\Sales\Order $orderResourceModel
    )
    {
        $this->systemConfigFactory = $systemConfigInterfaceFactory;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->config = $config;
        $this->currencyFactory = $currencyFactory;
        $this->localeCurrency = $localeCurrency;
        $this->localeFormat = $localeFormat;
        $this->priceFormatFactory = $priceFormatFactory;
        $this->guestCustomer = $guestCustomer;
        $this->shipping = $shipping;
        $this->payment = $payment;
        $this->regionFactory = $regionFactory;
        $this->customerGroupRepository = $customerGroupRepository;
        $this->searchCriteria = $searchCriteria;
        $this->helper = $helper;
        $this->request = $request;
        $this->staffManagement = $staffManagement;
        $this->staffRepository = $staffRepository;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->aclResource = $aclResource;
        $this->taxRateRepository = $taxRateRepository;
        $this->taxRuleRepository = $taxRuleRepository;
        $this->productTaxClass = $productTaxClass;
        $this->productTaxClassFactory = $productTaxClassFactory;
        $this->moduleManager = $moduleManager;
        $this->coreRegistry = $coreRegistry;
        $this->locationRepository = $locationRepository;
        $this->denominationRepository = $denominationRepository;
        $this->customSaleHelper = $customSaleHelper;
        $this->roleFactory = $roleFactory;
        $this->customerMetadataService = $customerMetadataService;
        $this->addressMetadataService = $addressMetadataService;
        $this->directoryHelper = $directoryHelper;
        $this->webposManagement = $webposManagement;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->configLocationFactory = $configLocationFactory;
        $this->websiteRepository = $websiteRepository;
        $this->salesChannelFactory = $salesChannelFactory;
        $this->resourceConnection = $resourceConnection;
        $this->date = $date;
        $this->paymentType = $paymentType;
        $this->refundPaymentTypeFactory = $refundPaymentTypeFactory;
        $this->allPaymentType = $allPaymentType;
        $this->allRefundPaymentTypeFactory = $allRefundPaymentTypeFactory;
        $this->orderResourceModel = $orderResourceModel;
    }

    /**
     * get list location
     *
     * @return \Magestore\Webpos\Api\Data\Config\ConfigInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAllConfig()
    {
        $configurations = [];
        $store = $this->helper->getCurrentStoreView();
        $this->currentStore = $store;
        $storeId = $store->getId();
        $config = $this->getConfigPath();
        foreach ($config as $item) {
            $configModel = $this->systemConfigFactory->create();
            $configModel->setData(\Magestore\Webpos\Api\Data\Config\SystemConfigInterface::PATH, $item);
            $value = $this->scopeConfig->getValue($item, 'stores', $storeId);
            if ($item === 'webpos/custom_receipt/receipt_logo' && $value) {
                $value = $this->getBaseMediaUrl() . 'webpos/receipt_logo/' . $value;
            }
            $value = $value == null ? '' : $value;
            $configModel->setData(\Magestore\Webpos\Api\Data\Config\SystemConfigInterface::VALUE, $value);
            $configurations[] = $configModel;
        }
        $this->config->setSettings($configurations);
        $this->config->setPermissions($this->getPermissions());
        $baseCurrencyCode = $this->scopeConfig->getValue(
            \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE, 'stores', $storeId
        );
        $this->config->setBaseCurrencyCode($store->getBaseCurrencyCode());
        $this->config->setCurrentCurrencyCode($store->getDefaultCurrencyCode());
        $this->config->setCustomerGroups($this->getCustomerGroups());
        $output = $this->getCurrencyList();
        $this->config->setCurrencies($output['currencies']);
        $this->config->setPriceFormats($output['price_formats']);
        $this->config->setGuestCustomer($this->getGuestCustomerInfo());
        $this->config->setShipping($this->getShippingInfo());
        $this->config->setPayment($this->getPaymentInfo());
        $this->config->setTaxRates($this->getTaxRates());
        $this->config->setTaxRules($this->getTaxRules());
        $this->config->setIsPrimaryLocation($this->isPrimaryLocation());
        $this->config->setDenominations($this->getDenominations());
        $this->config->setEnableModules($this->getEnableModules());
        $this->config->setMaxDiscountPercent($this->getMaxDiscountPercent());
        $this->config->setCustomerForm($this->customerMetadataService->getAttributes('adminhtml_customer'));
        $this->config->setCustomerAddressForm($this->addressMetadataService->getAttributes('adminhtml_customer_address'));
        $this->config->setCustomerCustomAttributes($this->customerMetadataService->getCustomAttributesMetadata());
        $this->config->setCustomerAddressCustomAttributes($this->addressMetadataService->getCustomAttributesMetadata());
        $this->config->setRootCategoryId($this->helper->getCurrentStoreView()->getRootCategoryId());
        $this->config->setCountriesWithOptionalZip($this->directoryHelper->getCountriesWithOptionalZip(true));
        /* config to check is webpos standard or not */
        $this->config->setIsWebposStandard($this->webposManagement->isWebposStandard());
        $this->config->setLocations($this->getConfigLocations());
        $this->config->setStores($this->getConfigStores());
        $this->config->setWebsites($this->getConfigWebsites());
        $this->config->setSalesChannels($this->getConfigSalesChannels());
        $this->config->setServerTimezoneOffset($this->date->getGmtOffset());
        $this->config->setPaymentType($this->getPaymentType());
        $this->config->setRefundPaymentType($this->getRefundPaymentType());

        $taxClasses = [];
        foreach ($this->productTaxClass->getAllOptions() as $option) {
            /** @var \Magestore\Webpos\Api\Data\Config\ProductTaxClassesInterface $model */
            $model = $this->productTaxClassFactory->create();
            $model->setLabel($option['label']);
            $model->setValue($option['value']);
            $taxClasses[] = $model;
        }
        $this->config->setProductTaxClasses($taxClasses);
        $this->config->setCustomSaleProductId($this->customSaleHelper->getProductId());
        return $this->config;
    }

    /**
     * @return \Magestore\Webpos\Api\Data\Config\PaymentTypeInterface
     */
    public function getPaymentType()
    {
        $information = $this->allPaymentType->getData();
        $this->paymentType->setCreditCardFormPayments(implode(',', $information['credit_card_form_payments']));
        $this->paymentType->setEWalletPayments(implode(',', $information['e_wallet_payments']));
        $this->paymentType->setFlatPayments(implode(',', $information['flat_payments']));
        $this->paymentType->setTerminalPayments(implode(',', $information['terminal_payments']));
        $this->paymentType->setInternetTerminalPayments(implode(',', $information['internet_terminal_payments']));
        $this->paymentType->setPreventCancelOrderRulePayments(implode(',', $information['prevent_cancel_order_rule_payments']));
        return $this->paymentType;
    }

    /**
     * @return RefundPaymentTypeInterface
     */
    public function getRefundPaymentType()
    {
        /** @var RefundPaymentType $allRefundPaymentType */
        $allRefundPaymentType = $this->allRefundPaymentTypeFactory->create();
        /** @var RefundPaymentTypeInterface $refundPaymentType */
        $refundPaymentType = $this->refundPaymentTypeFactory->create();
        $refundPaymentType->setAcceptedPayments(implode(',', $allRefundPaymentType->getAcceptedPayments()));
        $refundPaymentType->setUseTransactionPayments(implode(',', $allRefundPaymentType->getUseTransactionPayments()));
        return $refundPaymentType;
    }

    /**
     * @return string
     */
    public function getBaseMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPermissions()
    {
        $permissions = [];
        $sessionId = $this->request->getParam(\Magestore\WebposIntegration\Controller\Rest\RequestProcessor::SESSION_PARAM_KEY);
        try {
            $staffId = $this->staffManagement->authorizeSession($sessionId);
            $staffModel = $this->staffRepository->getById($staffId);
            $roleId = $staffModel->getRoleId();
            $ruleCollection = $this->ruleCollectionFactory->create()->addFieldToFilter('role_id', $roleId);
            foreach ($ruleCollection as $rule) {
                $permissions[] = $rule->getData('resource_id');
            }

        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Session with id "%1" does not exist.', $sessionId));
        }
        return $permissions;
    }


    /**
     * Get general config.
     *
     * @param
     * @return array
     */
    public function getConfigPath()
    {
        $configurations = [
            'general/locale/code',
            'general/region/display_all',
            'cataloginventory/item_options/manage_stock',
            'cataloginventory/item_options/backorders',
            'cataloginventory/item_options/max_sale_qty',
            'cataloginventory/item_options/min_qty',
            'cataloginventory/item_options/min_sale_qty',
            'cataloginventory/item_options/notify_stock_qty',
            'cataloginventory/item_options/auto_return',
            'cataloginventory/item_options/enable_qty_increments',
            'cataloginventory/item_options/qty_increments',
            'cataloginventory/options/can_subtract',
            'cataloginventory/options/can_back_in_stock',
            'customer/create_account/default_group',
            'shipping/origin/country_id',
            'shipping/origin/region_id',
            'shipping/origin/city',
            'shipping/origin/postcode',
            'tax/classes/shipping_tax_class',
            'tax/calculation/price_includes_tax',
            'tax/calculation/shipping_includes_tax',
            'tax/calculation/based_on',
            'tax/calculation/apply_tax_on',
            'tax/calculation/apply_after_discount',
            'tax/calculation/discount_tax',
            'tax/calculation/algorithm',
            'tax/calculation/cross_border_trade_enabled',
            'tax/defaults/country',
            'tax/defaults/region',
            'tax/defaults/postcode',
            'tax/display/type',
            'tax/cart_display/price',
            'tax/weee/enable',
            'tax/weee/display',
            'tax/weee/display_sales',
            'tax/weee/display_email',
            'tax/weee/include_in_subtotal',
            'tax/weee/apply_vat',
            'webpos/general/session_timeout',
            'webpos/general/webpos_color',
            'webpos/general/google_api_key',
            'webpos/product_search/barcode',
            'webpos/product_search/additional_attributes_on_grid',
            'webpos/checkout/add_out_of_stock_product',
            'webpos/checkout/need_confirm',
            'webpos/checkout/automatically_send_mail',
            'webpos/checkout/use_custom_prefix',
            'webpos/checkout/custom_prefix',
            'webpos/payment/bambora/enable',
            'webpos/payment/tyro/enable',
            'webpos/tax_configuration/custom_sale_default_tax_class',
            'webpos/tax_configuration/price_display/product_list',
            'webpos/tax_configuration/price_display/shipping',
            'webpos/tax_configuration/shopping_cart_display/price',
            'webpos/tax_configuration/shopping_cart_display/subtotal',
            'webpos/tax_configuration/shopping_cart_display/shipping_amount',
            'webpos/tax_configuration/shopping_cart_display/full_tax_summary',
            'webpos/tax_configuration/shopping_cart_display/zero_tax_subtotal',
            'webpos/tax_configuration/tax_display/price',
            'webpos/tax_configuration/tax_display/subtotal',
            'webpos/tax_configuration/tax_display/shipping_amount',
            'webpos/tax_configuration/tax_display/full_tax_summary',
            'webpos/tax_configuration/tax_display/zero_tax_subtotal',
            'webpos/tax_configuration/fpt/product_price',
            'webpos/tax_configuration/fpt/include_in_subtotal',
            'webpos/omnichannel_experience/fulfill_online',
            'webpos/session/enable',
            'webpos/session/enable_cash_control',
            'webpos/offline/product_time',
            'webpos/offline/stock_item_time',
            'webpos/offline/customer_time',
            'webpos/offline/order_time',
            'webpos/offline/session_time',
            'webpos/offline/order_since',
            'webpos/session/session_since',
            'webpos/custom_receipt/display_reason',
            'webpos/custom_receipt/receipt_logo',

            'webpos/performance/pos_default_mode',
            'webpos/performance/pos_tablet_default_mode',

            'customer/create_account/default_group',
            'customercredit/general/enable',
            'customercredit/spend/shipping',

            /** Version Info */
            'about/product/line',
            'about/product/version',

            /** gift cart config */
            'giftvoucher/general/active',
            'giftvoucher/general/showprefix',
            'giftvoucher/general/hiddenchar',
            'giftvoucher/general/status',
            'giftvoucher/general/enablecredit',
            'giftvoucher/general/use_for_ship',
            'giftvoucher/general/use_with_coupon'

        ];
        return $configurations;
    }

    /** get base currency code
     * @return mixed
     */
    public function getBaseCurrencyCode()
    {
        return $this->helper->getCurrentStoreView()->getBaseCurrency()->getCode();
    }

    /**
     * @return array
     */
    public function getCurrencyList()
    {
        $store = $this->helper->getCurrentStoreView();
        $currency = $this->currencyFactory->create();
        $baseCurrency = $this->helper->getCurrentStoreView()->getBaseCurrency();
        $baseCurrencyCode = $baseCurrency->getData('currency_code');
        $currencyList = array();
        $priceFormats = array();
        $output = [];
        $collection = $store->getAllowedCurrencies();
        $orderCurrencyList = $this->orderResourceModel->getAllOrderCurrency();
        if (count($collection) > 0) {
            foreach ($collection as $code) {
                $currencyRate = $baseCurrency->getRate($code);
                if (!$currencyRate && !in_array($code, $orderCurrencyList)) {
                    continue;
                }
                $allowCurrency = $this->localeCurrency->getCurrency($code);
                $currencySymbol = $allowCurrency->getSymbol() ? $allowCurrency->getSymbol() : $code;
                $currencyName = $allowCurrency->getName();
                $isDefault = $code == $baseCurrencyCode ? 1 : 0;
                $currency->setCode($code);
                $currency->setCurrencyName($currencyName);
                $currency->setCurrencySymbol($currencySymbol);
                $currency->setIsDefault($isDefault);
                $currency->setCurrencyRate($currencyRate);
                $currencyList[] = $currency->getData();
                $priceFormatModel = $this->priceFormatFactory->create();
                $priceFormat = $this->localeFormat->getPriceFormat(
                    null,
                    $code
                );
                $priceFormatModel->setCurrencyCode($code);
                $priceFormatModel->setDecimalSymbol($priceFormat['decimalSymbol']);
                $priceFormatModel->setGroupSymbol($priceFormat['groupSymbol']);
                $priceFormatModel->setGroupLength($priceFormat['groupLength']);
                $priceFormatModel->setIntegerRequired($priceFormat['integerRequired']);
                $priceFormatModel->setPattern($priceFormat['pattern']);
                $priceFormatModel->setPrecision($priceFormat['precision']);
                $priceFormatModel->setRequiredPrecision($priceFormat['requiredPrecision']);
                $priceFormats[] = $priceFormatModel;
            }
        }
        $output['currencies'] = $currencyList;
        $output['price_formats'] = $priceFormats;
        return $output;
    }

    /**
     * @return \Magestore\Webpos\Api\Data\Config\GuestCustomerInterface
     */
    public function getGuestCustomerInfo()
    {
        $guestInfo = [
            'webpos/guest_checkout/guest_status' => 'status',
            'webpos/guest_checkout/first_name' => 'first_name',
            'webpos/guest_checkout/last_name' => 'last_name',
            'webpos/guest_checkout/email' => 'email'
        ];

        $guestCustomer = $this->guestCustomer;

        foreach ($guestInfo as $key => $item) {
            $guestCustomer->setData($item, $this->scopeConfig->getValue($key, 'stores', $this->currentStore->getId()));
        }

        $region = $this->regionFactory->create()->load($guestCustomer->getRegion());
        if ($region->getId() && $region->getCountryId() == $guestCustomer->getCountry()) {
            $guestCustomer->setRegion($region->getName());
            $guestCustomer->setRegionId($region->getId());
        }

        return $guestCustomer;
    }

    /**
     * @return \Magestore\Webpos\Api\Data\Config\ShippingInterface
     */
    public function getShippingInfo()
    {
        $shippingInfo = [
            'webpos/shipping/method' => 'shipping_methods',
            'webpos/shipping/enable_delivery_date' => 'delivery_date',
            'webpos/shipping/default_shipping_title' => 'default_shipping_title'
        ];

        $shipping = $this->shipping;

        foreach ($shippingInfo as $key => $item) {
            $shipping->setData(
                $item,
                $this->scopeConfig->getValue($key, 'stores', $this->currentStore->getId())
                    ? $this->scopeConfig->getValue($key, 'stores', $this->currentStore->getId())
                    : ""
            );
        }

        return $shipping;
    }

    /**
     * @return \Magestore\Webpos\Api\Data\Config\PaymentInterface
     */
    public function getPaymentInfo()
    {
        $paymentInfo = [
            'webpos/payment/method' => 'payment_methods'
        ];

        $payment = $this->payment;

        foreach ($paymentInfo as $key => $item) {
            $payment->setData($item,
                $this->scopeConfig->getValue($key, 'stores', $this->currentStore->getId())
                    ? $this->scopeConfig->getValue($key, 'stores', $this->currentStore->getId())
                    : "");
        }

        return $payment;
    }

    /**
     * @return \Magestore\Webpos\Api\Data\Config\CustomerGroupInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerGroups()
    {
        $customerGroups = [];

        $searchCriteria = $this->searchCriteria;
        $searchCriteria->setFilterGroups([]);
        $searchCriteria->setSortOrders([]);
        $searchCriteria->setCurrentPage('');
        $searchCriteria->setPageSize('');

        $groups = $this->customerGroupRepository->getList($searchCriteria)->getItems();
        foreach ($groups as $group) {
            $customerGroups[] = $group;
        }

        return $customerGroups;
    }

    /**
     * @return array
     */
    public function getTaxRates()
    {
        $taxRates = [];

        $searchCriteria = $this->searchCriteria;
        $searchCriteria->setFilterGroups([]);
        $searchCriteria->setSortOrders([]);
        $searchCriteria->setCurrentPage('');
        $searchCriteria->setPageSize('');

        $rates = $this->taxRateRepository->getList($searchCriteria)->getItems();
        foreach ($rates as $rate) {
            if (!$rate->getTaxPostcode()) {
                $rate->setTaxPostcode('*');
            }
            $taxRates[] = $rate;
        }

        return $taxRates;
    }

    /**
     * @return array
     */
    public function getTaxRules()
    {
        $taxRules = [];

        $searchCriteria = $this->searchCriteria;
        $searchCriteria->setFilterGroups([]);
        $searchCriteria->setSortOrders([]);
        $searchCriteria->setCurrentPage('');
        $searchCriteria->setPageSize('');

        $rules = $this->taxRuleRepository->getList($searchCriteria)->getItems();
        foreach ($rules as $rule) {
            $taxRules[] = $rule;
        }

        return $taxRules;
    }

    /**
     * @return array
     */
    public function getDenominations()
    {
        $denominations = null;
        if (!$this->helper->isCashControl()) {
            return null;
        }

        $searchCriteria = $this->searchCriteria;
        $searchCriteria->setFilterGroups([]);
        $searchCriteria->setSortOrders([]);
        $searchCriteria->setCurrentPage('');
        $searchCriteria->setPageSize('');

        $denominations = $this->denominationRepository->getList($searchCriteria)->getItems();

        return $denominations;
    }

    /**
     * @return bool
     */
    public function isPrimaryLocation()
    {
        if (!$this->moduleManager->isEnabled('Magestore_InventorySuccess')) {
            return true;
        }
        $session = $this->coreRegistry->registry('currrent_session_model');
        if ($session && $session->getLocationId()) {
            $locationId = $session->getLocationId();
            try {
                $location = $this->locationRepository->getById($locationId);
                return $location->getIsPrimary();
            } catch (\Exception $exception) {
                return false;
            }
        }
        return false;
    }

    public function getEnableModules()
    {
        $enableModules = [];
        $modules = [
            'Magestore_InventorySuccess',
            'Magestore_Customercredit',
            'Magestore_Giftvoucher',
        ];
        $moduleMSI = "Magento_InventoryMSI";
        if ($this->webposManagement->isMSIEnable() && !$this->webposManagement->isWebposStandard()) {
            $enableModules[] = $moduleMSI;
        }
        foreach ($modules as $module) {
            if ($this->moduleManager->isEnabled($module)) {
                $enableModules[] = $module;
            }
        }
        return $enableModules;
    }

    public function getMaxDiscountPercent()
    {
        $sessionId = $this->request->getParam(\Magestore\WebposIntegration\Controller\Rest\RequestProcessor::SESSION_PARAM_KEY);
        try {
            $staffId = $this->staffManagement->authorizeSession($sessionId);
            $staffModel = $this->staffRepository->getById($staffId);
            $roleId = $staffModel->getRoleId();
            $role = $this->roleFactory->create()->load((int)$roleId);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Session with id "%1" does not exist.', $sessionId));
        }
        return $role->getMaxDiscountPercent();
    }

    /**
     * Get config locations
     *
     * @return \Magestore\Webpos\Api\Data\Config\LocationInterface[]|null
     */
    public function getConfigLocations()
    {
        if ($this->webposManagement->isMSIEnable()) {
            $searchCriteria = $this->searchCriteriaBuilderFactory->create()
                ->addFilter(\Magestore\Webpos\Api\Data\Location\LocationInterface::STOCK_ID, 0, 'gt')
                ->create();
            $locations = $this->locationRepository->getList($searchCriteria);
            $configLocations = [];
            foreach ($locations->getItems() as $item) {
                /** @var \Magestore\Webpos\Model\Config\Data\Location $configLocation */
                $configLocation = $this->configLocationFactory->create();
                $configLocation->setLocationId($item->getLocationId());
                $configLocation->setStockId($item->getStockId());
                $configLocations[] = $configLocation;
            }
            return $configLocations;
        }
        return null;
    }

    /**
     * Get config stores
     * @return \Magento\Store\Api\Data\StoreInterface[]
     */
    public function getConfigStores()
    {
        return $this->storeManager->getStores();
    }

    /**
     * Get config websites
     *
     * @return \Magento\Store\Api\Data\WebsiteInterface[]
     */
    public function getConfigWebsites()
    {
        return $this->websiteRepository->getList();
    }

    /**
     * Get config sales channels
     *
     * @return \Magestore\Webpos\Api\Data\InventorySales\SalesChannelInterface[]|null
     */
    public function getConfigSalesChannels()
    {
        if ($this->webposManagement->isMSIEnable()) {
            $salesChannels = [];
            $connection = $this->resourceConnection->getConnection();
            $tableName = $this->resourceConnection->getTableName('inventory_stock_sales_channel');
            $select = $connection->select()->from($tableName);
            $result = $connection->fetchAll($select);
            foreach ($result as $item) {
                /** @var \Magestore\Webpos\Model\InventorySales\SalesChannel $salesChannel */
                $salesChannel = $this->salesChannelFactory->create();
                $salesChannel->setType($item['type']);
                $salesChannel->setCode($item['code']);
                $salesChannel->setStockId($item['stock_id']);
                $salesChannels[] = $salesChannel;
            }
            return $salesChannels;
        }
        return null;
    }
}
