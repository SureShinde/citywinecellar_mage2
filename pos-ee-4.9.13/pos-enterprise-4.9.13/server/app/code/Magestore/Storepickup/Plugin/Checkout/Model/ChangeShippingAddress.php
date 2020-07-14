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

class ChangeShippingAddress extends \Magento\Checkout\Model\GuestShippingInformationManagement {

    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;
    /**
     * @var \Magento\Checkout\Api\ShippingInformationManagementInterface
     */
    protected $shippingInformationManagement;
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
     * @var \Magento\Sales\Api\Data\OrderAddressInterface
     */
    protected $_orderAddressInterface;
    /**
     * @var \Magestore\Storepickup\Helper\Region
     */
    protected $regionHelper;
    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $regionFactory;

    /**
     * ChangeShippingAddress constructor.
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magestore\Storepickup\Model\StoreFactory $storeCollection
     * @param \Magento\Sales\Api\Data\OrderAddressInterface $orderAddressInterface
     * @param \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
     * @param \Magento\Checkout\Api\ShippingInformationManagementInterface $shippingInformationManagement
     * @param \Magestore\Storepickup\Helper\Region $regionHelper
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magestore\Storepickup\Model\StoreFactory $storeCollection,
        \Magento\Sales\Api\Data\OrderAddressInterface $orderAddressInterface,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory,
        \Magento\Checkout\Api\ShippingInformationManagementInterface $shippingInformationManagement,
        \Magestore\Storepickup\Helper\Region $regionHelper,
        \Magento\Directory\Model\RegionFactory $regionFactory
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_storeCollection = $storeCollection;
        $this->_orderAddressInterface = $orderAddressInterface;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->shippingInformationManagement = $shippingInformationManagement;
        $this->regionHelper = $regionHelper;
        $this->regionFactory = $regionFactory;
    }

    /**
     * Before save address information
     *
     * @param \Magento\Checkout\Model\GuestShippingInformationManagement $subject
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\GuestShippingInformationManagement $subject,
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
