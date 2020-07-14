<?php

namespace Magestore\Webpos\Model\Staff\Data\Login\Location;

class Address extends \Magento\Framework\DataObject implements \Magestore\Webpos\Api\Data\Staff\Login\Location\AddressInterface {

    /**
     * @inheritdoc
     */
    public function getStreet() {
        return $this->getData(self::STREET);
    }	
    /**
     * @inheritdoc
     */
    public function setStreet($street) {
        return $this->setData(self::STREET, $street);
    }

    /**
     * @inheritdoc
     */
    public function getCity() {
        return $this->getData(self::CITY);
    }	
    /**
     * @inheritdoc
     */
    public function setCity($city) {
        return $this->setData(self::CITY, $city);
    }

    /**
     * @inheritdoc
     */
    public function getRegion() {
        return $this->getData(self::REGION);
    }	
    /**
     * @inheritdoc
     */
    public function setRegion($region) {
        return $this->setData(self::REGION, $region);
    }

    /**
     * @inheritdoc
     */
    public function getRegionId() {
        return $this->getData(self::REGION_ID);
    }	
    /**
     * @inheritdoc
     */
    public function setRegionId($regionId) {
        return $this->setData(self::REGION_ID, $regionId);
    }

    /**
     * @inheritdoc
     */
    public function getCountryId() {
        return $this->getData(self::COUNTRY_ID);
    }	
    /**
     * @inheritdoc
     */
    public function setCountryId($country) {
        return $this->setData(self::COUNTRY_ID, $country);
    }

    /**
     * @inheritdoc
     */
    public function getCountry() {
        return $this->getData(self::COUNTRY);
    }
    /**
     * @inheritdoc
     */
    public function setCountry($country) {
        return $this->setData(self::COUNTRY, $country);
    }

    /**
     * @inheritdoc
     */
    public function getPostcode() {
        return $this->getData(self::POSTCODE);
    }	
    /**
     * @inheritdoc
     */
    public function setPostcode($postcode) {
        return $this->setData(self::POSTCODE, $postcode);
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
        \Magestore\Webpos\Api\Data\Staff\Login\Location\AddressExtensionInterface $extensionAttributes
    ){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}