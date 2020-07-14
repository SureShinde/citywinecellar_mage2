<?php

namespace Magestore\Webpos\Model\Checkout\Order;

class Address extends \Magento\Framework\DataObject implements \Magestore\Webpos\Api\Data\Checkout\Order\AddressInterface
{
    /**
     * @inheritdoc
     */
    public function getEntityId() {
        return $this->getData(self::ENTITY_ID);
    }
    /**
     * @inheritdoc
     */
    public function setEntityId($entityId) {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * @inheritdoc
     */
    public function getParentId() {
        return $this->getData(self::PARENT_ID);
    }
    /**
     * @inheritdoc
     */
    public function setParentId($parentId) {
        return $this->setData(self::PARENT_ID, $parentId);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerAddressId() {
        return $this->getData(self::CUSTOMER_ADDRESS_ID);
    }
    /**
     * @inheritdoc
     */
    public function setCustomerAddressId($customerAddressId) {
        return $this->setData(self::CUSTOMER_ADDRESS_ID, $customerAddressId);
    }

    /**
     * @inheritdoc
     */
    public function getQuoteAddressId() {
        return $this->getData(self::QUOTE_ADDRESS_ID);
    }
    /**
     * @inheritdoc
     */
    public function setQuoteAddressId($quoteAddressId) {
        return $this->setData(self::QUOTE_ADDRESS_ID, $quoteAddressId);
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
    public function getCustomerId() {
        return $this->getData(self::CUSTOMER_ID);
    }
    /**
     * @inheritdoc
     */
    public function setCustomerId($customerId) {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * @inheritdoc
     */
    public function getFax() {
        return $this->getData(self::FAX);
    }
    /**
     * @inheritdoc
     */
    public function setFax($fax) {
        return $this->setData(self::FAX, $fax);
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
     * @inheritdoc
     */
    public function getLastname() {
        return $this->getData(self::LASTNAME);
    }
    /**
     * @inheritdoc
     */
    public function setLastname($lastname) {
        return $this->setData(self::LASTNAME, $lastname);
    }

    /**
     * @inheritdoc
     */
    public function getStreet() {
        if (is_array($this->getData(self::STREET))) {
            return $this->getData(self::STREET);
        }
        return explode(PHP_EOL, $this->getData(self::STREET));
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
    public function getEmail() {
        return $this->getData(self::EMAIL);
    }
    /**
     * @inheritdoc
     */
    public function setEmail($email) {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * @inheritdoc
     */
    public function getTelephone() {
        return $this->getData(self::TELEPHONE);
    }
    /**
     * @inheritdoc
     */
    public function setTelephone($telephone) {
        return $this->setData(self::TELEPHONE, $telephone);
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
    public function setCountryId($countryId) {
        return $this->setData(self::COUNTRY_ID, $countryId);
    }

    /**
     * @inheritdoc
     */
    public function getFirstname() {
        return $this->getData(self::FIRSTNAME);
    }
    /**
     * @inheritdoc
     */
    public function setFirstname($firstname) {
        return $this->setData(self::FIRSTNAME, $firstname);
    }

    /**
     * @inheritdoc
     */
    public function getAddressType() {
        return $this->getData(self::ADDRESS_TYPE);
    }
    /**
     * @inheritdoc
     */
    public function setAddressType($addressType) {
        return $this->setData(self::ADDRESS_TYPE, $addressType);
    }

    /**
     * @inheritdoc
     */
    public function getPrefix() {
        return $this->getData(self::PREFIX);
    }
    /**
     * @inheritdoc
     */
    public function setPrefix($prefix) {
        return $this->setData(self::PREFIX, $prefix);
    }

    /**
     * @inheritdoc
     */
    public function getMiddlename() {
        return $this->getData(self::MIDDLENAME);
    }
    /**
     * @inheritdoc
     */
    public function setMiddlename($middlename) {
        return $this->setData(self::MIDDLENAME, $middlename);
    }

    /**
     * @inheritdoc
     */
    public function getSuffix() {
        return $this->getData(self::SUFFIX);
    }
    /**
     * @inheritdoc
     */
    public function setSuffix($suffix) {
        return $this->setData(self::SUFFIX, $suffix);
    }

    /**
     * @inheritdoc
     */
    public function getCompany() {
        return $this->getData(self::COMPANY);
    }
    /**
     * @inheritdoc
     */
    public function setCompany($company) {
        return $this->setData(self::COMPANY, $company);
    }

    /**
     * @inheritdoc
     */
    public function getVatId() {
        return $this->getData(self::VAT_ID);
    }
    /**
     * @inheritdoc
     */
    public function setVatId($vatId) {
        return $this->setData(self::VAT_ID, $vatId);
    }

    /**
     * @inheritdoc
     */
    public function getVatIsValid() {
        return $this->getData(self::VAT_IS_VALID);
    }
    /**
     * @inheritdoc
     */
    public function setVatIsValid($vatIsValid) {
        return $this->setData(self::VAT_IS_VALID, $vatIsValid);
    }

    /**
     * @inheritdoc
     */
    public function getVatRequestId() {
        return $this->getData(self::VAT_REQUEST_ID);
    }
    /**
     * @inheritdoc
     */
    public function setVatRequestId($vatRequestId) {
        return $this->setData(self::VAT_REQUEST_ID, $vatRequestId);
    }

    /**
     * @inheritdoc
     */
    public function getVatRequestDate() {
        return $this->getData(self::VAT_REQUEST_DATE);
    }
    /**
     * @inheritdoc
     */
    public function setVatRequestDate($vatRequestDate) {
        return $this->setData(self::VAT_REQUEST_DATE, $vatRequestDate);
    }

    /**
     * @inheritdoc
     */
    public function getVatRequestSuccess() {
        return $this->getData(self::VAT_REQUEST_SUCCESS);
    }
    /**
     * @inheritdoc
     */
    public function setVatRequestSuccess($vatRequestSuccess) {
        return $this->setData(self::VAT_REQUEST_SUCCESS, $vatRequestSuccess);
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
        \Magestore\Webpos\Api\Data\Checkout\Order\AddressExtensionInterface $extensionAttributes
    ){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}