<?php

namespace Magestore\Webpos\Model\Checkout\Quote;

class Address extends \Magento\Framework\DataObject implements \Magestore\Webpos\Api\Data\Checkout\Quote\AddressInterface
{

    /**
     * @inheritdoc
     */
    public function getAddressId() {
        return $this->getData(self::ADDRESS_ID);
    }	
    /**
     * @inheritdoc
     */
    public function setAddressId($addressId) {
        return $this->setData(self::ADDRESS_ID, $addressId);
    }

    /**
     * @inheritdoc
     */
    public function getQuoteId() {
        return $this->getData(self::QUOTE_ID);
    }	
    /**
     * @inheritdoc
     */
    public function setQuoteId($quoteId) {
        return $this->setData(self::QUOTE_ID, $quoteId);
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt() {
        return $this->getData(self::CREATED_AT);
    }	
    /**
     * @inheritdoc
     */
    public function setCreatedAt($createdAt) {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt() {
        return $this->getData(self::UPDATED_AT);
    }	
    /**
     * @inheritdoc
     */
    public function setUpdatedAt($updatedAt) {
        return $this->setData(self::UPDATED_AT, $updatedAt);
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
    public function getSaveInAddressBook() {
        return $this->getData(self::SAVE_IN_ADDRESS_BOOK);
    }	
    /**
     * @inheritdoc
     */
    public function setSaveInAddressBook($saveInAddressBook) {
        return $this->setData(self::SAVE_IN_ADDRESS_BOOK, $saveInAddressBook);
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
    public function getSameAsBilling() {
        return $this->getData(self::SAME_AS_BILLING);
    }	
    /**
     * @inheritdoc
     */
    public function setSameAsBilling($sameAsBilling) {
        return $this->setData(self::SAME_AS_BILLING, $sameAsBilling);
    }

    /**
     * @inheritdoc
     */
    public function getCollectShippingRates() {
        return $this->getData(self::COLLECT_SHIPPING_RATES);
    }	
    /**
     * @inheritdoc
     */
    public function setCollectShippingRates($collectShippingRates) {
        return $this->setData(self::COLLECT_SHIPPING_RATES, $collectShippingRates);
    }

    /**
     * @inheritdoc
     */
    public function getShippingMethod() {
        return $this->getData(self::SHIPPING_METHOD);
    }	
    /**
     * @inheritdoc
     */
    public function setShippingMethod($shippingMethod) {
        return $this->setData(self::SHIPPING_METHOD, $shippingMethod);
    }

    /**
     * @inheritdoc
     */
    public function getShippingDescription() {
        return $this->getData(self::SHIPPING_DESCRIPTION);
    }	
    /**
     * @inheritdoc
     */
    public function setShippingDescription($shippingDescription) {
        return $this->setData(self::SHIPPING_DESCRIPTION, $shippingDescription);
    }

    /**
     * @inheritdoc
     */
    public function getWeight() {
        return $this->getData(self::WEIGHT);
    }	
    /**
     * @inheritdoc
     */
    public function setWeight($weight) {
        return $this->setData(self::WEIGHT, round($weight, 4));
    }

    /**
     * @inheritdoc
     */
    public function getSubtotal() {
        return $this->getData(self::SUBTOTAL);
    }	
    /**
     * @inheritdoc
     */
    public function setSubtotal($subtotal) {
        return $this->setData(self::SUBTOTAL, round($subtotal, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseSubtotal() {
        return $this->getData(self::BASE_SUBTOTAL);
    }	
    /**
     * @inheritdoc
     */
    public function setBaseSubtotal($baseSubtotal) {
        return $this->setData(self::BASE_SUBTOTAL, round($baseSubtotal, 4));
    }

    /**
     * @inheritdoc
     */
    public function getSubtotalWithDiscount() {
        return $this->getData(self::SUBTOTAL_WITH_DISCOUNT);
    }	
    /**
     * @inheritdoc
     */
    public function setSubtotalWithDiscount($subtotalWithDiscount) {
        return $this->setData(self::SUBTOTAL_WITH_DISCOUNT, round($subtotalWithDiscount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseSubtotalWithDiscount() {
        return $this->getData(self::BASE_SUBTOTAL_WITH_DISCOUNT);
    }	
    /**
     * @inheritdoc
     */
    public function setBaseSubtotalWithDiscount($baseSubtotalWithDiscount) {
        return $this->setData(self::BASE_SUBTOTAL_WITH_DISCOUNT, round($baseSubtotalWithDiscount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getTaxAmount() {
        return $this->getData(self::TAX_AMOUNT);
    }	
    /**
     * @inheritdoc
     */
    public function setTaxAmount($taxAmount) {
        return $this->setData(self::TAX_AMOUNT, round($taxAmount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseTaxAmount() {
        return $this->getData(self::BASE_TAX_AMOUNT);
    }	
    /**
     * @inheritdoc
     */
    public function setBaseTaxAmount($baseTaxAmount) {
        return $this->setData(self::BASE_TAX_AMOUNT, round($baseTaxAmount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getShippingAmount() {
        return $this->getData(self::SHIPPING_AMOUNT);
    }	
    /**
     * @inheritdoc
     */
    public function setShippingAmount($shippingAmount) {
        return $this->setData(self::SHIPPING_AMOUNT, round($shippingAmount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseShippingAmount() {
        return $this->getData(self::BASE_SHIPPING_AMOUNT);
    }	
    /**
     * @inheritdoc
     */
    public function setBaseShippingAmount($baseShippingAmount) {
        return $this->setData(self::BASE_SHIPPING_AMOUNT, round($baseShippingAmount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getShippingTaxAmount() {
        return $this->getData(self::SHIPPING_TAX_AMOUNT);
    }	
    /**
     * @inheritdoc
     */
    public function setShippingTaxAmount($shippingTaxAmount) {
        return $this->setData(self::SHIPPING_TAX_AMOUNT, round($shippingTaxAmount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseShippingTaxAmount() {
        return $this->getData(self::BASE_SHIPPING_TAX_AMOUNT);
    }	
    /**
     * @inheritdoc
     */
    public function setBaseShippingTaxAmount($baseShippingTaxAmount) {
        return $this->setData(self::BASE_SHIPPING_TAX_AMOUNT, round($baseShippingTaxAmount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getDiscountAmount() {
        return $this->getData(self::DISCOUNT_AMOUNT);
    }	
    /**
     * @inheritdoc
     */
    public function setDiscountAmount($discountAmount) {
        return $this->setData(self::DISCOUNT_AMOUNT, round($discountAmount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseDiscountAmount() {
        return $this->getData(self::BASE_DISCOUNT_AMOUNT);
    }	
    /**
     * @inheritdoc
     */
    public function setBaseDiscountAmount($baseDiscountAmount) {
        return $this->setData(self::BASE_DISCOUNT_AMOUNT, round($baseDiscountAmount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getGrandTotal() {
        return $this->getData(self::GRAND_TOTAL);
    }	
    /**
     * @inheritdoc
     */
    public function setGrandTotal($grandTotal) {
        return $this->setData(self::GRAND_TOTAL, round($grandTotal, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseGrandTotal() {
        return $this->getData(self::BASE_GRAND_TOTAL);
    }	
    /**
     * @inheritdoc
     */
    public function setBaseGrandTotal($baseGrandTotal) {
        return $this->setData(self::BASE_GRAND_TOTAL, round($baseGrandTotal, 4));
    }

    /**
     * @inheritdoc
     */
    public function getCustomerNotes() {
        return $this->getData(self::CUSTOMER_NOTES);
    }	
    /**
     * @inheritdoc
     */
    public function setCustomerNotes($customerNotes) {
        return $this->setData(self::CUSTOMER_NOTES, $customerNotes);
    }

    /**
     * @inheritdoc
     */
    public function getAppliedTaxes() {
        return $this->getData(self::APPLIED_TAXES);
    }	
    /**
     * @inheritdoc
     */
    public function setAppliedTaxes($appliedTaxes) {
        return $this->setData(self::APPLIED_TAXES, $appliedTaxes);
    }

    /**
     * @inheritdoc
     */
    public function getDiscountDescription() {
        return $this->getData(self::DISCOUNT_DESCRIPTION);
    }	
    /**
     * @inheritdoc
     */
    public function setDiscountDescription($discountDescription) {
        return $this->setData(self::DISCOUNT_DESCRIPTION, $discountDescription);
    }

    /**
     * @inheritdoc
     */
    public function getShippingDiscountAmount() {
        return $this->getData(self::SHIPPING_DISCOUNT_AMOUNT);
    }	
    /**
     * @inheritdoc
     */
    public function setShippingDiscountAmount($shippingDiscountAmount) {
        return $this->setData(self::SHIPPING_DISCOUNT_AMOUNT, round($shippingDiscountAmount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseShippingDiscountAmount() {
        return $this->getData(self::BASE_SHIPPING_DISCOUNT_AMOUNT);
    }	
    /**
     * @inheritdoc
     */
    public function setBaseShippingDiscountAmount($baseShippingDiscountAmount) {
        return $this->setData(self::BASE_SHIPPING_DISCOUNT_AMOUNT, round($baseShippingDiscountAmount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getSubtotalInclTax() {
        return $this->getData(self::SUBTOTAL_INCL_TAX);
    }	
    /**
     * @inheritdoc
     */
    public function setSubtotalInclTax($subtotalInclTax) {
        return $this->setData(self::SUBTOTAL_INCL_TAX, round($subtotalInclTax, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseSubtotalTotalInclTax() {
        return $this->getData(self::BASE_SUBTOTAL_TOTAL_INCL_TAX);
    }	
    /**
     * @inheritdoc
     */
    public function setBaseSubtotalTotalInclTax($baseSubtotalTotalInclTax) {
        return $this->setData(self::BASE_SUBTOTAL_TOTAL_INCL_TAX, round($baseSubtotalTotalInclTax, 4));
    }

    /**
     * @inheritdoc
     */
    public function getDiscountTaxCompensationAmount() {
        return $this->getData(self::DISCOUNT_TAX_COMPENSATION_AMOUNT);
    }	
    /**
     * @inheritdoc
     */
    public function setDiscountTaxCompensationAmount($discountTaxCompensationAmount) {
        return $this->setData(self::DISCOUNT_TAX_COMPENSATION_AMOUNT, round($discountTaxCompensationAmount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseDiscountTaxCompensationAmount() {
        return $this->getData(self::BASE_DISCOUNT_TAX_COMPENSATION_AMOUNT);
    }	
    /**
     * @inheritdoc
     */
    public function setBaseDiscountTaxCompensationAmount($baseDiscountTaxCompensationAmount) {
        return $this->setData(self::BASE_DISCOUNT_TAX_COMPENSATION_AMOUNT, round($baseDiscountTaxCompensationAmount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getShippingDiscountTaxCompensationAmount() {
        return $this->getData(self::SHIPPING_DISCOUNT_TAX_COMPENSATION_AMOUNT);
    }	
    /**
     * @inheritdoc
     */
    public function setShippingDiscountTaxCompensationAmount($shippingDiscountTaxCompensationAmount) {
        return $this->setData(self::SHIPPING_DISCOUNT_TAX_COMPENSATION_AMOUNT, round($shippingDiscountTaxCompensationAmount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseShippingDiscountTaxCompensationAmnt() {
        return $this->getData(self::BASE_SHIPPING_DISCOUNT_TAX_COMPENSATION_AMNT);
    }	
    /**
     * @inheritdoc
     */
    public function setBaseShippingDiscountTaxCompensationAmnt($baseShippingDiscountTaxCompensationAmnt) {
        return $this->setData(self::BASE_SHIPPING_DISCOUNT_TAX_COMPENSATION_AMNT, round($baseShippingDiscountTaxCompensationAmnt, 4));
    }

    /**
     * @inheritdoc
     */
    public function getShippingInclTax() {
        return $this->getData(self::SHIPPING_INCL_TAX);
    }	
    /**
     * @inheritdoc
     */
    public function setShippingInclTax($shippingInclTax) {
        return $this->setData(self::SHIPPING_INCL_TAX, round($shippingInclTax, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseShippingInclTax() {
        return $this->getData(self::BASE_SHIPPING_INCL_TAX);
    }	
    /**
     * @inheritdoc
     */
    public function setBaseShippingInclTax($baseShippingInclTax) {
        return $this->setData(self::BASE_SHIPPING_INCL_TAX, round($baseShippingInclTax, 4));
    }

    /**
     * @inheritdoc
     */
    public function getFreeShipping() {
        return $this->getData(self::FREE_SHIPPING);
    }	
    /**
     * @inheritdoc
     */
    public function setFreeShipping($freeShipping) {
        return $this->setData(self::FREE_SHIPPING, $freeShipping);
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
     * @inheritdoc
     */
    public function getGiftMessageId() {
        return $this->getData(self::GIFT_MESSAGE_ID);
    }	
    /**
     * @inheritdoc
     */
    public function setGiftMessageId($giftMessageId) {
        return $this->setData(self::GIFT_MESSAGE_ID, $giftMessageId);
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
        \Magestore\Webpos\Api\Data\Checkout\Quote\AddressExtensionInterface $extensionAttributes
    ){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}