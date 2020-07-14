<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Shipping;

/**
 * Interface ShippingMethodInterface
 * @package Magestore\Webpos\Api\Data\Shipping
 */
interface ShippingMethodInterface extends \Magento\Framework\Api\ExtensibleDataInterface {

    /**
     *
     */
    const CODE = 'code';
    /**
     *
     */
    const TITLE = 'title';
    /**
     *
     */
    const DESCRIPTION = 'description';
    /**
     *
     */
    const ERROR_MESSAGE = 'error_message';
    /**
     *
     */
    const SHIPMENT_REQUEST_TYPE = 'shipment_request_type';
    /**
     *
     */
    const TYPE = 'type';
    /**
     *
     */
    const PRICE = 'price';
    /**
     *
     */
    const CONDITION_NAME = 'condition_name';
    /**
     *
     */
    const IS_DEFAULT = 'is_default';
    /**
     *
     */
    const SPECIFIC_COUNTRIES_ALLOW = 'specific_countries_allow';
    /**
     *
     */
    const SPECIFIC_COUNTRY = 'specific_country';
    /**
     *
     */
    const HANDLING_FEE = 'handling_fee';
    /**
     *
     */
    const HANDLING_TYPE = 'handling_type';
    /**
     *
     */
    const HANDLING_ACTION = 'handling_action';
    /**
     *
     */
    const MAX_PACKAGE_WEIGHT = 'max_package_weight';
    /**
     *
     */
    const INCLUDE_VIRTUAL_PRICE = 'include_virtual_price';
    /**
     *
     */
    const FREE_SHIPPING_SUBTOTAL = 'free_shipping_subtotal';
    /**
     *
     */
    const RATES = 'rates';

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     * @return ShippingMethodInterface
     */
    public function setCode($code);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     * @return ShippingMethodInterface
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     * @return ShippingMethodInterface
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getErrorMessage();

    /**
     * @param string $errorMessage
     * @return ShippingMethodInterface
     */
    public function setErrorMessage($errorMessage);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     * @return ShippingMethodInterface
     */
    public function setType($type);

    /**
     * @return int
     */
    public function getShipmentRequestType();

    /**
     * @param int $shipmentRequestType
     * @return ShippingMethodInterface
     */
    public function setShipmentRequestType($shipmentRequestType);

    /**
     * @return string
     */
    public function getConditionName();

    /**
     * @param string $conditionName
     * @return ShippingMethodInterface
     */
    public function setConditionName($conditionName);

    /**
     * @return float
     */
    public function getPrice();

    /**
     * @param float $price
     * @return ShippingMethodInterface
     */
    public function setPrice($price);

    /**
     * @return int
     */
    public function getIsDefault();

    /**
     * @param int $isDefault
     * @return ShippingMethodInterface
     */
    public function setIsDefault($isDefault);

    /**
     * @return int
     */
    public function getSpecificCountriesAllow();

    /**
     * @param int $specificCountriesAllow
     * @return ShippingMethodInterface
     */
    public function setSpecificCountriesAllow($specificCountriesAllow);

    /**
     * @return string
     */
    public function getSpecificCountry();

    /**
     * @param string $specificCountry
     * @return ShippingMethodInterface
     */
    public function setSpecificCountry($specificCountry);

    /**
     * @return float
     */
    public function getHandlingFee();

    /**
     * @param float $handlingFee
     * @return ShippingMethodInterface
     */
    public function setHandlingFee($handlingFee);

    /**
     * @return string
     */
    public function getHandlingType();

    /**
     * @param string $handlingType
     * @return ShippingMethodInterface
     */
    public function setHandlingType($handlingType);

    /**
     * @return string
     */
    public function getHandlingAction();

    /**
     * @param string $handlingAction
     * @return ShippingMethodInterface
     */
    public function setHandlingAction($handlingAction);

    /**
     * @return float
     */
    public function getMaxPackageWeight();

    /**
     * @param float $maxPackageWeight
     * @return ShippingMethodInterface
     */
    public function setMaxPackageWeight($maxPackageWeight);

    /**
     * @return int
     */
    public function getIncludeVirtualPrice();

    /**
     * @param int $includeVirtualPrice
     * @return ShippingMethodInterface
     */
    public function setIncludeVirtualPrice($includeVirtualPrice);

    /**
     * @return float
     */
    public function getFreeShippingSubtotal();

    /**
     * @param float $freeShippingSubtotal
     * @return ShippingMethodInterface
     */
    public function setFreeShippingSubtotal($freeShippingSubtotal);

    /**
     * @return mixed[]
     */
    public function getRates();

    /**
     * @param mixed[] $rates
     * @return ShippingMethodInterface
     */
    public function setRates($rates);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Shipping\ShippingMethodExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Shipping\ShippingMethodExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Shipping\ShippingMethodExtensionInterface $extensionAttributes
    );
}