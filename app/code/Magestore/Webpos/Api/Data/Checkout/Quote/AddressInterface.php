<?php

namespace Magestore\Webpos\Api\Data\Checkout\Quote;

interface AddressInterface extends \Magento\Framework\Api\ExtensibleDataInterface {
    const ADDRESS_ID = 'address_id';
    const QUOTE_ID = 'quote_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const CUSTOMER_ID = 'customer_id';
    const SAVE_IN_ADDRESS_BOOK = 'save_in_address_book';
    const CUSTOMER_ADDRESS_ID = 'customer_address_id';
    const ADDRESS_TYPE = 'address_type';
    const EMAIL = 'email';
    const PREFIX = 'prefix';
    const FIRSTNAME = 'firstname';
    const MIDDLENAME = 'middlename';
    const LASTNAME = 'lastname';
    const SUFFIX = 'suffix';
    const COMPANY = 'company';
    const STREET = 'street';
    const CITY = 'city';
    const REGION = 'region';
    const REGION_ID = 'region_id';
    const POSTCODE = 'postcode';
    const COUNTRY_ID = 'country_id';
    const TELEPHONE = 'telephone';
    const FAX = 'fax';
    const SAME_AS_BILLING = 'same_as_billing';
    const COLLECT_SHIPPING_RATES = 'collect_shipping_rates';
    const SHIPPING_METHOD = 'shipping_method';
    const SHIPPING_DESCRIPTION = 'shipping_description';
    const WEIGHT = 'weight';
    const SUBTOTAL = 'subtotal';
    const BASE_SUBTOTAL = 'base_subtotal';
    const SUBTOTAL_WITH_DISCOUNT = 'subtotal_with_discount';
    const BASE_SUBTOTAL_WITH_DISCOUNT = 'base_subtotal_with_discount';
    const TAX_AMOUNT = 'tax_amount';
    const BASE_TAX_AMOUNT = 'base_tax_amount';
    const SHIPPING_AMOUNT = 'shipping_amount';
    const BASE_SHIPPING_AMOUNT = 'base_shipping_amount';
    const SHIPPING_TAX_AMOUNT = 'shipping_tax_amount';
    const BASE_SHIPPING_TAX_AMOUNT = 'base_shipping_tax_amount';
    const DISCOUNT_AMOUNT = 'discount_amount';
    const BASE_DISCOUNT_AMOUNT = 'base_discount_amount';
    const GRAND_TOTAL = 'grand_total';
    const BASE_GRAND_TOTAL = 'base_grand_total';
    const CUSTOMER_NOTES = 'customer_notes';
    const APPLIED_TAXES = 'applied_taxes';
    const DISCOUNT_DESCRIPTION = 'discount_description';
    const SHIPPING_DISCOUNT_AMOUNT = 'shipping_discount_amount';
    const BASE_SHIPPING_DISCOUNT_AMOUNT = 'base_shipping_discount_amount';
    const SUBTOTAL_INCL_TAX = 'subtotal_incl_tax';
    const BASE_SUBTOTAL_TOTAL_INCL_TAX = 'base_subtotal_total_incl_tax';
    const DISCOUNT_TAX_COMPENSATION_AMOUNT = 'discount_tax_compensation_amount';
    const BASE_DISCOUNT_TAX_COMPENSATION_AMOUNT = 'base_discount_tax_compensation_amount';
    const SHIPPING_DISCOUNT_TAX_COMPENSATION_AMOUNT = 'shipping_discount_tax_compensation_amount';
    const BASE_SHIPPING_DISCOUNT_TAX_COMPENSATION_AMNT = 'base_shipping_discount_tax_compensation_amnt';
    const SHIPPING_INCL_TAX = 'shipping_incl_tax';
    const BASE_SHIPPING_INCL_TAX = 'base_shipping_incl_tax';
    const FREE_SHIPPING = 'free_shipping';
    const VAT_ID = 'vat_id';
    const VAT_IS_VALID = 'vat_is_valid';
    const VAT_REQUEST_ID = 'vat_request_id';
    const VAT_REQUEST_DATE = 'vat_request_date';
    const VAT_REQUEST_SUCCESS = 'vat_request_success';
    const GIFT_MESSAGE_ID = 'gift_message_id';


    /**
     * Get Address Id
     *
     * @return int|null
     */
    public function getAddressId();	
    /**
     * Set Address Id
     *
     * @param int|null $addressId
     * @return AddressInterface
     */
    public function setAddressId($addressId);

    /**
     * Get Quote Id
     *
     * @return int|null
     */
    public function getQuoteId();	
    /**
     * Set Quote Id
     *
     * @param int|null $quoteId
     * @return AddressInterface
     */
    public function setQuoteId($quoteId);

    /**
     * Get Created At
     *
     * @return string|null
     */
    public function getCreatedAt();	
    /**
     * Set Created At
     *
     * @param string|null $createdAt
     * @return AddressInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get Updated At
     *
     * @return string|null
     */
    public function getUpdatedAt();	
    /**
     * Set Updated At
     *
     * @param string|null $updatedAt
     * @return AddressInterface
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get Customer Id
     *
     * @return int|null
     */
    public function getCustomerId();	
    /**
     * Set Customer Id
     *
     * @param int|null $customerId
     * @return AddressInterface
     */
    public function setCustomerId($customerId);

    /**
     * Get Save In Address Book
     *
     * @return int|null
     */
    public function getSaveInAddressBook();	
    /**
     * Set Save In Address Book
     *
     * @param int|null $saveInAddressBook
     * @return AddressInterface
     */
    public function setSaveInAddressBook($saveInAddressBook);

    /**
     * Get Customer Address Id
     *
     * @return int|null
     */
    public function getCustomerAddressId();	
    /**
     * Set Customer Address Id
     *
     * @param int|null $customerAddressId
     * @return AddressInterface
     */
    public function setCustomerAddressId($customerAddressId);

    /**
     * Get Address Type
     *
     * @return string|null
     */
    public function getAddressType();	
    /**
     * Set Address Type
     *
     * @param string|null $addressType
     * @return AddressInterface
     */
    public function setAddressType($addressType);

    /**
     * Get Email
     *
     * @return string|null
     */
    public function getEmail();	
    /**
     * Set Email
     *
     * @param string|null $email
     * @return AddressInterface
     */
    public function setEmail($email);

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
     * Get Firstname
     *
     * @return string|null
     */
    public function getFirstname();	
    /**
     * Set Firstname
     *
     * @param string|null $firstname
     * @return AddressInterface
     */
    public function setFirstname($firstname);

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
     * Get Lastname
     *
     * @return string|null
     */
    public function getLastname();	
    /**
     * Set Lastname
     *
     * @param string|null $lastname
     * @return AddressInterface
     */
    public function setLastname($lastname);

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
     * Get Street
     *
     * @return string|null
     */
    public function getStreet();	
    /**
     * Set Street
     *
     * @param string|null $street
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
     * @param string|null $city
     * @return AddressInterface
     */
    public function setCity($city);

    /**
     * Get Region
     *
     * @return string|null
     */
    public function getRegion();	
    /**
     * Set Region
     *
     * @param string|null $region
     * @return AddressInterface
     */
    public function setRegion($region);

    /**
     * Get Region Id
     *
     * @return int|null
     */
    public function getRegionId();	
    /**
     * Set Region Id
     *
     * @param int|null $regionId
     * @return AddressInterface
     */
    public function setRegionId($regionId);

    /**
     * Get Postcode
     *
     * @return string|null
     */
    public function getPostcode();	
    /**
     * Set Postcode
     *
     * @param string|null $postcode
     * @return AddressInterface
     */
    public function setPostcode($postcode);

    /**
     * Get Country Id
     *
     * @return string|null
     */
    public function getCountryId();	
    /**
     * Set Country Id
     *
     * @param string|null $countryId
     * @return AddressInterface
     */
    public function setCountryId($countryId);

    /**
     * Get Telephone
     *
     * @return string|null
     */
    public function getTelephone();	
    /**
     * Set Telephone
     *
     * @param string|null $telephone
     * @return AddressInterface
     */
    public function setTelephone($telephone);

    /**
     * Get Fax
     *
     * @return string|null
     */
    public function getFax();	
    /**
     * Set Fax
     *
     * @param string|null $fax
     * @return AddressInterface
     */
    public function setFax($fax);

    /**
     * Get Same As Billing
     *
     * @return int|null
     */
    public function getSameAsBilling();	
    /**
     * Set Same As Billing
     *
     * @param int|null $sameAsBilling
     * @return AddressInterface
     */
    public function setSameAsBilling($sameAsBilling);

    /**
     * Get Collect Shipping Rates
     *
     * @return int|null
     */
    public function getCollectShippingRates();	
    /**
     * Set Collect Shipping Rates
     *
     * @param int|null $collectShippingRates
     * @return AddressInterface
     */
    public function setCollectShippingRates($collectShippingRates);

    /**
     * Get Shipping Method
     *
     * @return string|null
     */
    public function getShippingMethod();	
    /**
     * Set Shipping Method
     *
     * @param string|null $shippingMethod
     * @return AddressInterface
     */
    public function setShippingMethod($shippingMethod);

    /**
     * Get Shipping Description
     *
     * @return string|null
     */
    public function getShippingDescription();	
    /**
     * Set Shipping Description
     *
     * @param string|null $shippingDescription
     * @return AddressInterface
     */
    public function setShippingDescription($shippingDescription);

    /**
     * Get Weight
     *
     * @return float|null
     */
    public function getWeight();	
    /**
     * Set Weight
     *
     * @param float|null $weight
     * @return AddressInterface
     */
    public function setWeight($weight);

    /**
     * Get Subtotal
     *
     * @return float|null
     */
    public function getSubtotal();	
    /**
     * Set Subtotal
     *
     * @param float|null $subtotal
     * @return AddressInterface
     */
    public function setSubtotal($subtotal);

    /**
     * Get Base Subtotal
     *
     * @return float|null
     */
    public function getBaseSubtotal();	
    /**
     * Set Base Subtotal
     *
     * @param float|null $baseSubtotal
     * @return AddressInterface
     */
    public function setBaseSubtotal($baseSubtotal);

    /**
     * Get Subtotal With Discount
     *
     * @return float|null
     */
    public function getSubtotalWithDiscount();	
    /**
     * Set Subtotal With Discount
     *
     * @param float|null $subtotalWithDiscount
     * @return AddressInterface
     */
    public function setSubtotalWithDiscount($subtotalWithDiscount);

    /**
     * Get Base Subtotal With Discount
     *
     * @return float|null
     */
    public function getBaseSubtotalWithDiscount();	
    /**
     * Set Base Subtotal With Discount
     *
     * @param float|null $baseSubtotalWithDiscount
     * @return AddressInterface
     */
    public function setBaseSubtotalWithDiscount($baseSubtotalWithDiscount);

    /**
     * Get Tax Amount
     *
     * @return float|null
     */
    public function getTaxAmount();	
    /**
     * Set Tax Amount
     *
     * @param float|null $taxAmount
     * @return AddressInterface
     */
    public function setTaxAmount($taxAmount);

    /**
     * Get Base Tax Amount
     *
     * @return float|null
     */
    public function getBaseTaxAmount();	
    /**
     * Set Base Tax Amount
     *
     * @param float|null $baseTaxAmount
     * @return AddressInterface
     */
    public function setBaseTaxAmount($baseTaxAmount);

    /**
     * Get Shipping Amount
     *
     * @return float|null
     */
    public function getShippingAmount();	
    /**
     * Set Shipping Amount
     *
     * @param float|null $shippingAmount
     * @return AddressInterface
     */
    public function setShippingAmount($shippingAmount);

    /**
     * Get Base Shipping Amount
     *
     * @return float|null
     */
    public function getBaseShippingAmount();	
    /**
     * Set Base Shipping Amount
     *
     * @param float|null $baseShippingAmount
     * @return AddressInterface
     */
    public function setBaseShippingAmount($baseShippingAmount);

    /**
     * Get Shipping Tax Amount
     *
     * @return float|null
     */
    public function getShippingTaxAmount();	
    /**
     * Set Shipping Tax Amount
     *
     * @param float|null $shippingTaxAmount
     * @return AddressInterface
     */
    public function setShippingTaxAmount($shippingTaxAmount);

    /**
     * Get Base Shipping Tax Amount
     *
     * @return float|null
     */
    public function getBaseShippingTaxAmount();	
    /**
     * Set Base Shipping Tax Amount
     *
     * @param float|null $baseShippingTaxAmount
     * @return AddressInterface
     */
    public function setBaseShippingTaxAmount($baseShippingTaxAmount);

    /**
     * Get Discount Amount
     *
     * @return float|null
     */
    public function getDiscountAmount();	
    /**
     * Set Discount Amount
     *
     * @param float|null $discountAmount
     * @return AddressInterface
     */
    public function setDiscountAmount($discountAmount);

    /**
     * Get Base Discount Amount
     *
     * @return float|null
     */
    public function getBaseDiscountAmount();	
    /**
     * Set Base Discount Amount
     *
     * @param float|null $baseDiscountAmount
     * @return AddressInterface
     */
    public function setBaseDiscountAmount($baseDiscountAmount);

    /**
     * Get Grand Total
     *
     * @return float|null
     */
    public function getGrandTotal();	
    /**
     * Set Grand Total
     *
     * @param float|null $grandTotal
     * @return AddressInterface
     */
    public function setGrandTotal($grandTotal);

    /**
     * Get Base Grand Total
     *
     * @return float|null
     */
    public function getBaseGrandTotal();	
    /**
     * Set Base Grand Total
     *
     * @param float|null $baseGrandTotal
     * @return AddressInterface
     */
    public function setBaseGrandTotal($baseGrandTotal);

    /**
     * Get Customer Notes
     *
     * @return string|null
     */
    public function getCustomerNotes();	
    /**
     * Set Customer Notes
     *
     * @param string|null $customerNotes
     * @return AddressInterface
     */
    public function setCustomerNotes($customerNotes);

    /**
     * Get Applied Taxes
     *
     * @return string|null
     */
    public function getAppliedTaxes();	
    /**
     * Set Applied Taxes
     *
     * @param string|null $appliedTaxes
     * @return AddressInterface
     */
    public function setAppliedTaxes($appliedTaxes);

    /**
     * Get Discount Description
     *
     * @return string|null
     */
    public function getDiscountDescription();	
    /**
     * Set Discount Description
     *
     * @param string|null $discountDescription
     * @return AddressInterface
     */
    public function setDiscountDescription($discountDescription);

    /**
     * Get Shipping Discount Amount
     *
     * @return float|null
     */
    public function getShippingDiscountAmount();	
    /**
     * Set Shipping Discount Amount
     *
     * @param float|null $shippingDiscountAmount
     * @return AddressInterface
     */
    public function setShippingDiscountAmount($shippingDiscountAmount);

    /**
     * Get Base Shipping Discount Amount
     *
     * @return float|null
     */
    public function getBaseShippingDiscountAmount();	
    /**
     * Set Base Shipping Discount Amount
     *
     * @param float|null $baseShippingDiscountAmount
     * @return AddressInterface
     */
    public function setBaseShippingDiscountAmount($baseShippingDiscountAmount);

    /**
     * Get Subtotal Incl Tax
     *
     * @return float|null
     */
    public function getSubtotalInclTax();	
    /**
     * Set Subtotal Incl Tax
     *
     * @param float|null $subtotalInclTax
     * @return AddressInterface
     */
    public function setSubtotalInclTax($subtotalInclTax);

    /**
     * Get Base Subtotal Total Incl Tax
     *
     * @return float|null
     */
    public function getBaseSubtotalTotalInclTax();	
    /**
     * Set Base Subtotal Total Incl Tax
     *
     * @param float|null $baseSubtotalTotalInclTax
     * @return AddressInterface
     */
    public function setBaseSubtotalTotalInclTax($baseSubtotalTotalInclTax);

    /**
     * Get Discount Tax Compensation Amount
     *
     * @return float|null
     */
    public function getDiscountTaxCompensationAmount();	
    /**
     * Set Discount Tax Compensation Amount
     *
     * @param float|null $discountTaxCompensationAmount
     * @return AddressInterface
     */
    public function setDiscountTaxCompensationAmount($discountTaxCompensationAmount);

    /**
     * Get Base Discount Tax Compensation Amount
     *
     * @return float|null
     */
    public function getBaseDiscountTaxCompensationAmount();	
    /**
     * Set Base Discount Tax Compensation Amount
     *
     * @param float|null $baseDiscountTaxCompensationAmount
     * @return AddressInterface
     */
    public function setBaseDiscountTaxCompensationAmount($baseDiscountTaxCompensationAmount);

    /**
     * Get Shipping Discount Tax Compensation Amount
     *
     * @return float|null
     */
    public function getShippingDiscountTaxCompensationAmount();	
    /**
     * Set Shipping Discount Tax Compensation Amount
     *
     * @param float|null $shippingDiscountTaxCompensationAmount
     * @return AddressInterface
     */
    public function setShippingDiscountTaxCompensationAmount($shippingDiscountTaxCompensationAmount);

    /**
     * Get Base Shipping Discount Tax Compensation Amnt
     *
     * @return float|null
     */
    public function getBaseShippingDiscountTaxCompensationAmnt();	
    /**
     * Set Base Shipping Discount Tax Compensation Amnt
     *
     * @param float|null $baseShippingDiscountTaxCompensationAmnt
     * @return AddressInterface
     */
    public function setBaseShippingDiscountTaxCompensationAmnt($baseShippingDiscountTaxCompensationAmnt);

    /**
     * Get Shipping Incl Tax
     *
     * @return float|null
     */
    public function getShippingInclTax();	
    /**
     * Set Shipping Incl Tax
     *
     * @param float|null $shippingInclTax
     * @return AddressInterface
     */
    public function setShippingInclTax($shippingInclTax);

    /**
     * Get Base Shipping Incl Tax
     *
     * @return float|null
     */
    public function getBaseShippingInclTax();	
    /**
     * Set Base Shipping Incl Tax
     *
     * @param float|null $baseShippingInclTax
     * @return AddressInterface
     */
    public function setBaseShippingInclTax($baseShippingInclTax);

    /**
     * Get Free Shipping
     *
     * @return int|null
     */
    public function getFreeShipping();	
    /**
     * Set Free Shipping
     *
     * @param int|null $freeShipping
     * @return AddressInterface
     */
    public function setFreeShipping($freeShipping);

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
     * @return int|null
     */
    public function getVatRequestSuccess();	
    /**
     * Set Vat Request Success
     *
     * @param int|null $vatRequestSuccess
     * @return AddressInterface
     */
    public function setVatRequestSuccess($vatRequestSuccess);

    /**
     * Get Gift Message Id
     *
     * @return int|null
     */
    public function getGiftMessageId();	
    /**
     * Set Gift Message Id
     *
     * @param int|null $giftMessageId
     * @return AddressInterface
     */
    public function setGiftMessageId($giftMessageId);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\AddressExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\Quote\AddressExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Checkout\Quote\AddressExtensionInterface $extensionAttributes
    );
}