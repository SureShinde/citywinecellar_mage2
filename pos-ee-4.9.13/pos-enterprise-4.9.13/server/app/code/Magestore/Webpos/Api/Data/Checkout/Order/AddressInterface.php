<?php

namespace Magestore\Webpos\Api\Data\Checkout\Order;

interface AddressInterface extends \Magento\Framework\Api\ExtensibleDataInterface {
    const ENTITY_ID = 'entity_id';
    const PARENT_ID = 'parent_id';
    const CUSTOMER_ADDRESS_ID = 'customer_address_id';
    const QUOTE_ADDRESS_ID = 'quote_address_id';
    const REGION_ID = 'region_id';
    const CUSTOMER_ID = 'customer_id';
    const FAX = 'fax';
    const REGION = 'region';
    const POSTCODE = 'postcode';
    const LASTNAME = 'lastname';
    const STREET = 'street';
    const CITY = 'city';
    const EMAIL = 'email';
    const TELEPHONE = 'telephone';
    const COUNTRY_ID = 'country_id';
    const FIRSTNAME = 'firstname';
    const ADDRESS_TYPE = 'address_type';
    const PREFIX = 'prefix';
    const MIDDLENAME = 'middlename';
    const SUFFIX = 'suffix';
    const COMPANY = 'company';
    const VAT_ID = 'vat_id';
    const VAT_IS_VALID = 'vat_is_valid';
    const VAT_REQUEST_ID = 'vat_request_id';
    const VAT_REQUEST_DATE = 'vat_request_date';
    const VAT_REQUEST_SUCCESS = 'vat_request_success';

    /**
     * Get Entity Id
     *
     * @return int|null
     */
    public function getEntityId();	
    /**
     * Set Entity Id
     *
     * @param int $entityId
     * @return AddressInterface
     */
    public function setEntityId($entityId);

    /**
     * Get Parent Id
     *
     * @return int|null
     */
    public function getParentId();	
    /**
     * Set Parent Id
     *
     * @param int $parentId
     * @return AddressInterface
     */
    public function setParentId($parentId);

    /**
     * Get Customer Address Id
     *
     * @return int|null
     */
    public function getCustomerAddressId();	
    /**
     * Set Customer Address Id
     *
     * @param int $customerAddressId
     * @return AddressInterface
     */
    public function setCustomerAddressId($customerAddressId);

    /**
     * Get Quote Address Id
     *
     * @return int|null
     */
    public function getQuoteAddressId();	
    /**
     * Set Quote Address Id
     *
     * @param int $quoteAddressId
     * @return AddressInterface
     */
    public function setQuoteAddressId($quoteAddressId);

    /**
     * Get Region Id
     *
     * @return int|null
     */
    public function getRegionId();	
    /**
     * Set Region Id
     *
     * @param int $regionId
     * @return AddressInterface
     */
    public function setRegionId($regionId);

    /**
     * Get Customer Id
     *
     * @return int|null
     */
    public function getCustomerId();	
    /**
     * Set Customer Id
     *
     * @param int $customerId
     * @return AddressInterface
     */
    public function setCustomerId($customerId);

    /**
     * Get Fax
     *
     * @return string|null
     */
    public function getFax();	
    /**
     * Set Fax
     *
     * @param string $fax
     * @return AddressInterface
     */
    public function setFax($fax);

    /**
     * Get Region
     *
     * @return string|null
     */
    public function getRegion();	
    /**
     * Set Region
     *
     * @param string $region
     * @return AddressInterface
     */
    public function setRegion($region);

    /**
     * Get Postcode
     *
     * @return string|null
     */
    public function getPostcode();	
    /**
     * Set Postcode
     *
     * @param string $postcode
     * @return AddressInterface
     */
    public function setPostcode($postcode);

    /**
     * Get Lastname
     *
     * @return string|null
     */
    public function getLastname();	
    /**
     * Set Lastname
     *
     * @param string $lastname
     * @return AddressInterface
     */
    public function setLastname($lastname);

    /**
     * Get Street
     *
     * @return string[]|null
     */
    public function getStreet();	
    /**
     * Set Street
     *
     * @param string[]|null $street
     * @return AddressInterface
     */
    public function setStreet($street);

    /**
     * Get City
     *
     * @return string|null
     */
    public function getCity();	
    /**
     * Set City
     *
     * @param string $city
     * @return AddressInterface
     */
    public function setCity($city);

    /**
     * Get Email
     *
     * @return string|null
     */
    public function getEmail();	
    /**
     * Set Email
     *
     * @param string $email
     * @return AddressInterface
     */
    public function setEmail($email);

    /**
     * Get Telephone
     *
     * @return string|null
     */
    public function getTelephone();	
    /**
     * Set Telephone
     *
     * @param string $telephone
     * @return AddressInterface
     */
    public function setTelephone($telephone);

    /**
     * Get Country Id
     *
     * @return string|null
     */
    public function getCountryId();	
    /**
     * Set Country Id
     *
     * @param string $countryId
     * @return AddressInterface
     */
    public function setCountryId($countryId);

    /**
     * Get Firstname
     *
     * @return string|null
     */
    public function getFirstname();	
    /**
     * Set Firstname
     *
     * @param string $firstname
     * @return AddressInterface
     */
    public function setFirstname($firstname);

    /**
     * Get Address Type
     *
     * @return string|null
     */
    public function getAddressType();	
    /**
     * Set Address Type
     *
     * @param string $addressType
     * @return AddressInterface
     */
    public function setAddressType($addressType);

    /**
     * Get Prefix
     *
     * @return string|null
     */
    public function getPrefix();	
    /**
     * Set Prefix
     *
     * @param string|null $prefix
     * @return AddressInterface
     */
    public function setPrefix($prefix);

    /**
     * Get Middlename
     *
     * @return string|null
     */
    public function getMiddlename();	
    /**
     * Set Middlename
     *
     * @param string|null $middlename
     * @return AddressInterface
     */
    public function setMiddlename($middlename);

    /**
     * Get Suffix
     *
     * @return string|null
     */
    public function getSuffix();	
    /**
     * Set Suffix
     *
     * @param string|null $suffix
     * @return AddressInterface
     */
    public function setSuffix($suffix);

    /**
     * Get Company
     *
     * @return string|null
     */
    public function getCompany();	
    /**
     * Set Company
     *
     * @param string|null $company
     * @return AddressInterface
     */
    public function setCompany($company);

    /**
     * Get Vat Id
     *
     * @return string|null
     */
    public function getVatId();	
    /**
     * Set Vat Id
     *
     * @param string|null $vatId
     * @return AddressInterface
     */
    public function setVatId($vatId);

    /**
     * Get Vat Is Valid
     *
     * @return int|null
     */
    public function getVatIsValid();	
    /**
     * Set Vat Is Valid
     *
     * @param int|null $vatIsValid
     * @return AddressInterface
     */
    public function setVatIsValid($vatIsValid);

    /**
     * Get Vat Request Id
     *
     * @return string|null
     */
    public function getVatRequestId();	
    /**
     * Set Vat Request Id
     *
     * @param string|null $vatRequestId
     * @return AddressInterface
     */
    public function setVatRequestId($vatRequestId);

    /**
     * Get Vat Request Date
     *
     * @return string|null
     */
    public function getVatRequestDate();	
    /**
     * Set Vat Request Date
     *
     * @param string|null $vatRequestDate
     * @return AddressInterface
     */
    public function setVatRequestDate($vatRequestDate);

    /**
     * Get Vat Request Success
     *
     * @return string|null
     */
    public function getVatRequestSuccess();	
    /**
     * Set Vat Request Success
     *
     * @param string|null $vatRequestSuccess
     * @return AddressInterface
     */
    public function setVatRequestSuccess($vatRequestSuccess);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\AddressExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\Order\AddressExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Checkout\Order\AddressExtensionInterface $extensionAttributes
    );
}