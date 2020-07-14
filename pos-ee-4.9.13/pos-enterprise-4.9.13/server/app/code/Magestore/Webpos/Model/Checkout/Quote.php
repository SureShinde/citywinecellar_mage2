<?php

namespace Magestore\Webpos\Model\Checkout;

class Quote extends \Magento\Framework\DataObject implements \Magestore\Webpos\Api\Data\Checkout\QuoteInterface
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
    public function getStoreId() {
        return $this->getData(self::STORE_ID);
    }	
    /**
     * @inheritdoc
     */
    public function setStoreId($storeId) {
        return $this->setData(self::STORE_ID, $storeId);
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
    public function getConvertedAt() {
        return $this->getData(self::CONVERTED_AT);
    }	
    /**
     * @inheritdoc
     */
    public function setConvertedAt($convertedAt) {
        return $this->setData(self::CONVERTED_AT, $convertedAt);
    }

    /**
     * @inheritdoc
     */
    public function getIsActive() {
        return $this->getData(self::IS_ACTIVE);
    }	
    /**
     * @inheritdoc
     */
    public function setIsActive($isActive) {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * @inheritdoc
     */
    public function getIsVirtual() {
        return $this->getData(self::IS_VIRTUAL);
    }	
    /**
     * @inheritdoc
     */
    public function setIsVirtual($isVirtual) {
        return $this->setData(self::IS_VIRTUAL, $isVirtual);
    }

    /**
     * @inheritdoc
     */
    public function getIsMultiShipping() {
        return $this->getData(self::IS_MULTI_SHIPPING);
    }	
    /**
     * @inheritdoc
     */
    public function setIsMultiShipping($isMultiShipping) {
        return $this->setData(self::IS_MULTI_SHIPPING, $isMultiShipping);
    }

    /**
     * @inheritdoc
     */
    public function getItemsCount() {
        return $this->getData(self::ITEMS_COUNT);
    }	
    /**
     * @inheritdoc
     */
    public function setItemsCount($itemsCount) {
        return $this->setData(self::ITEMS_COUNT, $itemsCount);
    }

    /**
     * @inheritdoc
     */
    public function getItemsQty() {
        return $this->getData(self::ITEMS_QTY);
    }	
    /**
     * @inheritdoc
     */
    public function setItemsQty($itemsQty) {
        return $this->setData(self::ITEMS_QTY, round($itemsQty, 4));
    }

    /**
     * @inheritdoc
     */
    public function getOrigOrderId() {
        return $this->getData(self::ORIG_ORDER_ID);
    }	
    /**
     * @inheritdoc
     */
    public function setOrigOrderId($origOrderId) {
        return $this->setData(self::ORIG_ORDER_ID, $origOrderId);
    }

    /**
     * @inheritdoc
     */
    public function getStoreToBaseRate() {
        return $this->getData(self::STORE_TO_BASE_RATE);
    }	
    /**
     * @inheritdoc
     */
    public function setStoreToBaseRate($storeToBaseRate) {
        return $this->setData(self::STORE_TO_BASE_RATE, round($storeToBaseRate, 4));
    }

    /**
     * @inheritdoc
     */
    public function getStoreToQuoteRate() {
        return $this->getData(self::STORE_TO_QUOTE_RATE);
    }	
    /**
     * @inheritdoc
     */
    public function setStoreToQuoteRate($storeToQuoteRate) {
        return $this->setData(self::STORE_TO_QUOTE_RATE, round($storeToQuoteRate, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseCurrencyCode() {
        return $this->getData(self::BASE_CURRENCY_CODE);
    }	
    /**
     * @inheritdoc
     */
    public function setBaseCurrencyCode($baseCurrencyCode) {
        return $this->setData(self::BASE_CURRENCY_CODE, $baseCurrencyCode);
    }

    /**
     * @inheritdoc
     */
    public function getStoreCurrencyCode() {
        return $this->getData(self::STORE_CURRENCY_CODE);
    }	
    /**
     * @inheritdoc
     */
    public function setStoreCurrencyCode($storeCurrencyCode) {
        return $this->setData(self::STORE_CURRENCY_CODE, $storeCurrencyCode);
    }

    /**
     * @inheritdoc
     */
    public function getQuoteCurrencyCode() {
        return $this->getData(self::QUOTE_CURRENCY_CODE);
    }	
    /**
     * @inheritdoc
     */
    public function setQuoteCurrencyCode($quoteCurrencyCode) {
        return $this->setData(self::QUOTE_CURRENCY_CODE, $quoteCurrencyCode);
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
    public function getCheckoutMethod() {
        return $this->getData(self::CHECKOUT_METHOD);
    }	
    /**
     * @inheritdoc
     */
    public function setCheckoutMethod($checkoutMethod) {
        return $this->setData(self::CHECKOUT_METHOD, $checkoutMethod);
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
    public function getCustomerTaxClassId() {
        return $this->getData(self::CUSTOMER_TAX_CLASS_ID);
    }	
    /**
     * @inheritdoc
     */
    public function setCustomerTaxClassId($customerTaxClassId) {
        return $this->setData(self::CUSTOMER_TAX_CLASS_ID, $customerTaxClassId);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerGroupId() {
        return $this->getData(self::CUSTOMER_GROUP_ID);
    }	
    /**
     * @inheritdoc
     */
    public function setCustomerGroupId($customerGroupId) {
        return $this->setData(self::CUSTOMER_GROUP_ID, $customerGroupId);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerEmail() {
        return $this->getData(self::CUSTOMER_EMAIL);
    }	
    /**
     * @inheritdoc
     */
    public function setCustomerEmail($customerEmail) {
        return $this->setData(self::CUSTOMER_EMAIL, $customerEmail);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerPrefix() {
        return $this->getData(self::CUSTOMER_PREFIX);
    }	
    /**
     * @inheritdoc
     */
    public function setCustomerPrefix($customerPrefix) {
        return $this->setData(self::CUSTOMER_PREFIX, $customerPrefix);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerFirstname() {
        return $this->getData(self::CUSTOMER_FIRSTNAME);
    }	
    /**
     * @inheritdoc
     */
    public function setCustomerFirstname($customerFirstname) {
        return $this->setData(self::CUSTOMER_FIRSTNAME, $customerFirstname);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerMiddlename() {
        return $this->getData(self::CUSTOMER_MIDDLENAME);
    }	
    /**
     * @inheritdoc
     */
    public function setCustomerMiddlename($customerMiddlename) {
        return $this->setData(self::CUSTOMER_MIDDLENAME, $customerMiddlename);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerLastname() {
        return $this->getData(self::CUSTOMER_LASTNAME);
    }	
    /**
     * @inheritdoc
     */
    public function setCustomerLastname($customerLastname) {
        return $this->setData(self::CUSTOMER_LASTNAME, $customerLastname);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerSuffix() {
        return $this->getData(self::CUSTOMER_SUFFIX);
    }	
    /**
     * @inheritdoc
     */
    public function setCustomerSuffix($customerSuffix) {
        return $this->setData(self::CUSTOMER_SUFFIX, $customerSuffix);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerDob() {
        return $this->getData(self::CUSTOMER_DOB);
    }	
    /**
     * @inheritdoc
     */
    public function setCustomerDob($customerDob) {
        return $this->setData(self::CUSTOMER_DOB, $customerDob);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerNote() {
        return $this->getData(self::CUSTOMER_NOTE);
    }	
    /**
     * @inheritdoc
     */
    public function setCustomerNote($customerNote) {
        return $this->setData(self::CUSTOMER_NOTE, $customerNote);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerNoteNotify() {
        return $this->getData(self::CUSTOMER_NOTE_NOTIFY);
    }	
    /**
     * @inheritdoc
     */
    public function setCustomerNoteNotify($customerNoteNotify) {
        return $this->setData(self::CUSTOMER_NOTE_NOTIFY, $customerNoteNotify);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerIsGuest() {
        return $this->getData(self::CUSTOMER_IS_GUEST);
    }	
    /**
     * @inheritdoc
     */
    public function setCustomerIsGuest($customerIsGuest) {
        return $this->setData(self::CUSTOMER_IS_GUEST, $customerIsGuest);
    }

    /**
     * @inheritdoc
     */
    public function getRemoteIp() {
        return $this->getData(self::REMOTE_IP);
    }	
    /**
     * @inheritdoc
     */
    public function setRemoteIp($remoteIp) {
        return $this->setData(self::REMOTE_IP, $remoteIp);
    }

    /**
     * @inheritdoc
     */
    public function getAppliedRuleIds() {
        return $this->getData(self::APPLIED_RULE_IDS);
    }	
    /**
     * @inheritdoc
     */
    public function setAppliedRuleIds($appliedRuleIds) {
        return $this->setData(self::APPLIED_RULE_IDS, $appliedRuleIds);
    }

    /**
     * @inheritdoc
     */
    public function getReservedOrderId() {
        return $this->getData(self::RESERVED_ORDER_ID);
    }	
    /**
     * @inheritdoc
     */
    public function setReservedOrderId($reservedOrderId) {
        return $this->setData(self::RESERVED_ORDER_ID, $reservedOrderId);
    }

    /**
     * @inheritdoc
     */
    public function getPasswordHash() {
        return $this->getData(self::PASSWORD_HASH);
    }	
    /**
     * @inheritdoc
     */
    public function setPasswordHash($passwordHash) {
        return $this->setData(self::PASSWORD_HASH, $passwordHash);
    }

    /**
     * @inheritdoc
     */
    public function getCouponCode() {
        return $this->getData(self::COUPON_CODE);
    }	
    /**
     * @inheritdoc
     */
    public function setCouponCode($couponCode) {
        return $this->setData(self::COUPON_CODE, $couponCode);
    }

    /**
     * @inheritdoc
     */
    public function getGlobalCurrencyCode() {
        return $this->getData(self::GLOBAL_CURRENCY_CODE);
    }	
    /**
     * @inheritdoc
     */
    public function setGlobalCurrencyCode($globalCurrencyCode) {
        return $this->setData(self::GLOBAL_CURRENCY_CODE, $globalCurrencyCode);
    }

    /**
     * @inheritdoc
     */
    public function getBaseToGlobalRate() {
        return $this->getData(self::BASE_TO_GLOBAL_RATE);
    }	
    /**
     * @inheritdoc
     */
    public function setBaseToGlobalRate($baseToGlobalRate) {
        return $this->setData(self::BASE_TO_GLOBAL_RATE, round($baseToGlobalRate, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseToQuoteRate() {
        return $this->getData(self::BASE_TO_QUOTE_RATE);
    }	
    /**
     * @inheritdoc
     */
    public function setBaseToQuoteRate($baseToQuoteRate) {
        return $this->setData(self::BASE_TO_QUOTE_RATE, round($baseToQuoteRate, 4));
    }

    /**
     * @inheritdoc
     */
    public function getCustomerTaxvat() {
        return $this->getData(self::CUSTOMER_TAXVAT);
    }	
    /**
     * @inheritdoc
     */
    public function setCustomerTaxvat($customerTaxvat) {
        return $this->setData(self::CUSTOMER_TAXVAT, $customerTaxvat);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerGender() {
        return $this->getData(self::CUSTOMER_GENDER);
    }	
    /**
     * @inheritdoc
     */
    public function setCustomerGender($customerGender) {
        return $this->setData(self::CUSTOMER_GENDER, $customerGender);
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
    public function getIsChanged() {
        return $this->getData(self::IS_CHANGED);
    }	
    /**
     * @inheritdoc
     */
    public function setIsChanged($isChanged) {
        return $this->setData(self::IS_CHANGED, $isChanged);
    }

    /**
     * @inheritdoc
     */
    public function getTriggerRecollect() {
        return $this->getData(self::TRIGGER_RECOLLECT);
    }	
    /**
     * @inheritdoc
     */
    public function setTriggerRecollect($triggerRecollect) {
        return $this->setData(self::TRIGGER_RECOLLECT, $triggerRecollect);
    }

    /**
     * @inheritdoc
     */
    public function getExtShippingInfo() {
        return $this->getData(self::EXT_SHIPPING_INFO);
    }	
    /**
     * @inheritdoc
     */
    public function setExtShippingInfo($extShippingInfo) {
        return $this->setData(self::EXT_SHIPPING_INFO, $extShippingInfo);
    }

    /**
     * @inheritdoc
     */
    public function getIsPersistent() {
        return $this->getData(self::IS_PERSISTENT);
    }	
    /**
     * @inheritdoc
     */
    public function setIsPersistent($isPersistent) {
        return $this->setData(self::IS_PERSISTENT, $isPersistent);
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
     * @inheritdoc
     */
    public function getAddresses(){
        return $this->getData(self::ADDRESSES);
    }
    /**
     * @inheritdoc
     */
    public function setAddresses($addresses){
        return $this->setData(self::ADDRESSES, $addresses);
    }

    /**
     * @inheritdoc
     */
    public function getItems(){
        return $this->getData(self::ITEMS);
    }
    /**
     * @inheritdoc
     */
    public function setItems($items){
        return $this->setData(self::ITEMS, $items);
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
        \Magestore\Webpos\Api\Data\Checkout\QuoteExtensionInterface $extensionAttributes
    ){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}