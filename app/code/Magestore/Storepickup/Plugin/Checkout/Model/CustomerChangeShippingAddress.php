<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Storepickup
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Storepickup\Plugin\Checkout\Model;

class CustomerChangeShippingAddress extends \Magento\Checkout\Model\ShippingInformationManagement{

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @codeCoverageIgnore
     */
    protected $_checkoutSession;

    /**
     * @var \Magestore\Storepickup\Model\StoreFactory
     */
    protected $_storeCollection;


    /**
     * @var \Magestore\Storepickup\Helper\Region
     */
    protected $regionHelper;

    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $regionFactory;

    /**
     * CustomerChangeShippingAddress constructor.
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magestore\Storepickup\Model\StoreFactory $storeCollection
     * @param \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement
     * @param \Magento\Checkout\Model\PaymentDetailsFactory $paymentDetailsFactory
     * @param \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalsRepository
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Quote\Model\QuoteAddressValidator $addressValidator
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector
     * @param \Magestore\Storepickup\Helper\Region $regionHelper
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     */
    public function __construct(
		\Magento\Checkout\Model\Session $checkoutSession,
        \Magestore\Storepickup\Model\StoreFactory $storeCollection,
        \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement,
        \Magento\Checkout\Model\PaymentDetailsFactory $paymentDetailsFactory,
        \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalsRepository,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Quote\Model\QuoteAddressValidator $addressValidator,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector,
        \Magestore\Storepickup\Helper\Region $regionHelper,
        \Magento\Directory\Model\RegionFactory $regionFactory
    ) {
		$this->_checkoutSession = $checkoutSession;
        $this->_storeCollection = $storeCollection;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->paymentDetailsFactory = $paymentDetailsFactory;
        $this->cartTotalsRepository = $cartTotalsRepository;
        $this->quoteRepository = $quoteRepository;
        $this->addressValidator = $addressValidator;
        $this->logger = $logger;
        $this->addressRepository = $addressRepository;
        $this->scopeConfig = $scopeConfig;
        $this->totalsCollector = $totalsCollector;
        $this->regionHelper = $regionHelper;
        $this->regionFactory = $regionFactory;
    }

    /**
     * Before save address information
     *
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        if ($addressInformation->getShippingMethodCode() == "storepickup") {
            $address = $addressInformation->getShippingAddress();
            $storePickupSession = $this->_checkoutSession->getData('storepickup_session', true);
            if ($storePickupSession) {
                $this->_checkoutSession->setData('storepickup_shipping_description', $storePickupSession);
                $storeId = $storePickupSession['store_id'];
                $collectionStore = $this->_storeCollection->create();
                $store = $collectionStore->load($storeId, 'storepickup_id');

                if ($store->getStateId()) {
                    /** @var \Magento\Directory\Model\Region $region */
                    $regionModel = $this->regionFactory->create()->load($store->getStateId());
                    $region = $regionModel->getName();
                    $regionId = $store->getStateId();
                    $regionCode = $regionModel->getCode();
                } else {
                    $region = $store->getData('state');
                    $regionId = 0;
                    $regionCode= '';
                }

                $address->setFirstname('Store');
                $address->setLastname($store->getData('store_name'));
                $address->setStreet($store->getData('address'));
                $address->setCity($store->getCity());
                $address->setRegion($region);
                $address->setRegionId($regionId);
                $address->setRegionCode($regionCode);
                $address->setPostcode($store->getData('zipcode'));
                $address->setCountryId($store->getData('country_id'));
                $address->setCompany($store->getData('company', ''));
                $address->setCustomerAddressId(null);

                if ($store->getFax()) {
                    $address->setFax($store->getFax());
                }
                if ($store->getPhone()) {
                    $address->setTelephone($store->getPhone());
                }
                $addressInformation->setShippingAddress($address);
            }
        }
    }

}
