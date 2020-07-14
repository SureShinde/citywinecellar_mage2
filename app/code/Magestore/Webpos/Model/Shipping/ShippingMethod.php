<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Shipping;


/**
 * Class Pos
 * @package Magestore\Webpos\Model\Pos
 */
class ShippingMethod extends \Magento\Framework\DataObject implements \Magestore\Webpos\Api\Data\Shipping\ShippingMethodInterface
{
    /**
     * @return string
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->getData(self::ERROR_MESSAGE);
    }

    /**
     * @param string $errorMessage
     * @return $this
     */
    public function setErrorMessage($errorMessage)
    {
        return $this->setData(self::ERROR_MESSAGE, $errorMessage);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * @return int
     */
    public function getShipmentRequestType()
    {
        return $this->getData(self::SHIPMENT_REQUEST_TYPE);
    }

    /**
     * @param int $shipmentRequestType
     * @return $this
     */
    public function setShipmentRequestType($shipmentRequestType)
    {
        return $this->setData(self::SHIPMENT_REQUEST_TYPE, $shipmentRequestType);
    }

    /**
     * @return string
     */
    public function getConditionName()
    {
        return $this->getData(self::CONDITION_NAME);
    }

    /**
     * @param string $conditionName
     * @return $this
     */
    public function setConditionName($conditionName)
    {
        return $this->setData(self::CONDITION_NAME, $conditionName);
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->getData(self::PRICE);
    }

    /**
     * @param float $price
     * @return $this
     */
    public function setPrice($price)
    {
        return $this->setData(self::PRICE, $price);
    }

    /**
     * @return int
     */
    public function getIsDefault()
    {
        return $this->getData(self::IS_DEFAULT);
    }

    /**
     * @param int $isDefault
     * @return $this
     */
    public function setIsDefault($isDefault)
    {
        return $this->setData(self::IS_DEFAULT, $isDefault);
    }

    /**
     * @return int
     */
    public function getSpecificCountriesAllow()
    {
        return $this->getData(self::SPECIFIC_COUNTRIES_ALLOW);
    }

    /**
     * @param int $specificCountriesAllow
     * @return $this
     */
    public function setSpecificCountriesAllow($specificCountriesAllow)
    {
        return $this->setData(self::SPECIFIC_COUNTRIES_ALLOW, $specificCountriesAllow);
    }

    /**
     * @return string
     */
    public function getSpecificCountry()
    {
        return $this->getData(self::SPECIFIC_COUNTRY);
    }

    /**
     * @param string $specificCountry
     * @return $this
     */
    public function setSpecificCountry($specificCountry)
    {
        return $this->setData(self::SPECIFIC_COUNTRY, $specificCountry);
    }

    /**
     * @return float
     */
    public function getHandlingFee()
    {
        return $this->getData(self::HANDLING_FEE);
    }

    /**
     * @param float $handlingFee
     * @return $this
     */
    public function setHandlingFee($handlingFee)
    {
        return $this->setData(self::HANDLING_FEE, $handlingFee);
    }

    /**
     * @return string
     */
    public function getHandlingType()
    {
        return $this->getData(self::HANDLING_TYPE);
    }

    /**
     * @param string $handlingType
     * @return $this
     */
    public function setHandlingType($handlingType)
    {
        return $this->setData(self::HANDLING_TYPE, $handlingType);
    }

    /**
     * @return string
     */
    public function getHandlingAction()
    {
        return $this->getData(self::HANDLING_ACTION);
    }

    /**
     * @param string $handlingAction
     * @return $this
     */
    public function setHandlingAction($handlingAction)
    {
        return $this->setData(self::HANDLING_ACTION, $handlingAction);
    }

    /**
     * @return float
     */
    public function getMaxPackageWeight()
    {
        return $this->getData(self::MAX_PACKAGE_WEIGHT);
    }

    /**
     * @param float $maxPackageWeight
     * @return $this
     */
    public function setMaxPackageWeight($maxPackageWeight)
    {
        return $this->setData(self::MAX_PACKAGE_WEIGHT, $maxPackageWeight);
    }

    /**
     * @return int
     */
    public function getIncludeVirtualPrice()
    {
        return $this->getData(self::INCLUDE_VIRTUAL_PRICE);
    }

    /**
     * @param int $includeVirtualPrice
     * @return $this
     */
    public function setIncludeVirtualPrice($includeVirtualPrice)
    {
        return $this->setData(self::INCLUDE_VIRTUAL_PRICE, $includeVirtualPrice);
    }

    /**
     * @return float
     */
    public function getFreeShippingSubtotal()
    {
        return $this->getData(self::FREE_SHIPPING_SUBTOTAL);
    }

    /**
     * @param float $freeShippingSubtotal
     * @return $this
     */
    public function setFreeShippingSubtotal($freeShippingSubtotal)
    {
        return $this->setData(self::FREE_SHIPPING_SUBTOTAL, $freeShippingSubtotal);
    }

    /**
     * @return array
     */
    public function getRates()
    {
        return $this->getData(self::RATES);
    }

    /**
     * @param array $rates
     * @return $this
     */
    public function setRates($rates)
    {
        return $this->setData(self::RATES, $rates);
    }
    /**
     * @inheritDoc
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Shipping\ShippingMethodExtensionInterface $extensionAttributes
    ){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}