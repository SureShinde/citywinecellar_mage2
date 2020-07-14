<?php

namespace Magestore\Webpos\Api\Data\Checkout;

interface QuoteInterface extends \Magento\Framework\Api\ExtensibleDataInterface {
    const ENTITY_ID = 'entity_id';
    const STORE_ID = 'store_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const CONVERTED_AT = 'converted_at';
    const IS_ACTIVE = 'is_active';
    const IS_VIRTUAL = 'is_virtual';
    const IS_MULTI_SHIPPING = 'is_multi_shipping';
    const ITEMS_COUNT = 'items_count';
    const ITEMS_QTY = 'items_qty';
    const ORIG_ORDER_ID = 'orig_order_id';
    const STORE_TO_BASE_RATE = 'store_to_base_rate';
    const STORE_TO_QUOTE_RATE = 'store_to_quote_rate';
    const BASE_CURRENCY_CODE = 'base_currency_code';
    const STORE_CURRENCY_CODE = 'store_currency_code';
    const QUOTE_CURRENCY_CODE = 'quote_currency_code';
    const GRAND_TOTAL = 'grand_total';
    const BASE_GRAND_TOTAL = 'base_grand_total';
    const CHECKOUT_METHOD = 'checkout_method';
    const CUSTOMER_ID = 'customer_id';
    const CUSTOMER_TAX_CLASS_ID = 'customer_tax_class_id';
    const CUSTOMER_GROUP_ID = 'customer_group_id';
    const CUSTOMER_EMAIL = 'customer_email';
    const CUSTOMER_PREFIX = 'customer_prefix';
    const CUSTOMER_FIRSTNAME = 'customer_firstname';
    const CUSTOMER_MIDDLENAME = 'customer_middlename';
    const CUSTOMER_LASTNAME = 'customer_lastname';
    const CUSTOMER_SUFFIX = 'customer_suffix';
    const CUSTOMER_DOB = 'customer_dob';
    const CUSTOMER_NOTE = 'customer_note';
    const CUSTOMER_NOTE_NOTIFY = 'customer_note_notify';
    const CUSTOMER_IS_GUEST = 'customer_is_guest';
    const REMOTE_IP = 'remote_ip';
    const APPLIED_RULE_IDS = 'applied_rule_ids';
    const RESERVED_ORDER_ID = 'reserved_order_id';
    const PASSWORD_HASH = 'password_hash';
    const COUPON_CODE = 'coupon_code';
    const GLOBAL_CURRENCY_CODE = 'global_currency_code';
    const BASE_TO_GLOBAL_RATE = 'base_to_global_rate';
    const BASE_TO_QUOTE_RATE = 'base_to_quote_rate';
    const CUSTOMER_TAXVAT = 'customer_taxvat';
    const CUSTOMER_GENDER = 'customer_gender';
    const SUBTOTAL = 'subtotal';
    const BASE_SUBTOTAL = 'base_subtotal';
    const SUBTOTAL_WITH_DISCOUNT = 'subtotal_with_discount';
    const BASE_SUBTOTAL_WITH_DISCOUNT = 'base_subtotal_with_discount';
    const IS_CHANGED = 'is_changed';
    const TRIGGER_RECOLLECT = 'trigger_recollect';
    const EXT_SHIPPING_INFO = 'ext_shipping_info';
    const IS_PERSISTENT = 'is_persistent';
    const GIFT_MESSAGE_ID = 'gift_message_id';
    const ADDRESSES = 'addresses';
    const ITEMS = 'items';


    /**
     * Get Entity Id
     *
     * @return int|null
     */
    public function getEntityId();	
    /**
     * Set Entity Id
     *
     * @param int|null $entityId
     * @return QuoteInterface
     */
    public function setEntityId($entityId);

    /**
     * Get Store Id
     *
     * @return int|null
     */
    public function getStoreId();	
    /**
     * Set Store Id
     *
     * @param int|null $storeId
     * @return QuoteInterface
     */
    public function setStoreId($storeId);

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
     * @return QuoteInterface
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
     * @return QuoteInterface
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get Converted At
     *
     * @return string|null
     */
    public function getConvertedAt();	
    /**
     * Set Converted At
     *
     * @param string|null $convertedAt
     * @return QuoteInterface
     */
    public function setConvertedAt($convertedAt);

    /**
     * Get Is Active
     *
     * @return int|null
     */
    public function getIsActive();	
    /**
     * Set Is Active
     *
     * @param int|null $isActive
     * @return QuoteInterface
     */
    public function setIsActive($isActive);

    /**
     * Get Is Virtual
     *
     * @return int|null
     */
    public function getIsVirtual();	
    /**
     * Set Is Virtual
     *
     * @param int|null $isVirtual
     * @return QuoteInterface
     */
    public function setIsVirtual($isVirtual);

    /**
     * Get Is Multi Shipping
     *
     * @return int|null
     */
    public function getIsMultiShipping();	
    /**
     * Set Is Multi Shipping
     *
     * @param int|null $isMultiShipping
     * @return QuoteInterface
     */
    public function setIsMultiShipping($isMultiShipping);

    /**
     * Get Items Count
     *
     * @return int|null
     */
    public function getItemsCount();	
    /**
     * Set Items Count
     *
     * @param int|null $itemsCount
     * @return QuoteInterface
     */
    public function setItemsCount($itemsCount);

    /**
     * Get Items Qty
     *
     * @return float|null
     */
    public function getItemsQty();	
    /**
     * Set Items Qty
     *
     * @param float|null $itemsQty
     * @return QuoteInterface
     */
    public function setItemsQty($itemsQty);

    /**
     * Get Orig Order Id
     *
     * @return int|null
     */
    public function getOrigOrderId();	
    /**
     * Set Orig Order Id
     *
     * @param int|null $origOrderId
     * @return QuoteInterface
     */
    public function setOrigOrderId($origOrderId);

    /**
     * Get Store To Base Rate
     *
     * @return float|null
     */
    public function getStoreToBaseRate();	
    /**
     * Set Store To Base Rate
     *
     * @param float|null $storeToBaseRate
     * @return QuoteInterface
     */
    public function setStoreToBaseRate($storeToBaseRate);

    /**
     * Get Store To Quote Rate
     *
     * @return float|null
     */
    public function getStoreToQuoteRate();	
    /**
     * Set Store To Quote Rate
     *
     * @param float|null $storeToQuoteRate
     * @return QuoteInterface
     */
    public function setStoreToQuoteRate($storeToQuoteRate);

    /**
     * Get Base Currency Code
     *
     * @return string|null
     */
    public function getBaseCurrencyCode();	
    /**
     * Set Base Currency Code
     *
     * @param string|null $baseCurrencyCode
     * @return QuoteInterface
     */
    public function setBaseCurrencyCode($baseCurrencyCode);

    /**
     * Get Store Currency Code
     *
     * @return string|null
     */
    public function getStoreCurrencyCode();	
    /**
     * Set Store Currency Code
     *
     * @param string|null $storeCurrencyCode
     * @return QuoteInterface
     */
    public function setStoreCurrencyCode($storeCurrencyCode);

    /**
     * Get Quote Currency Code
     *
     * @return string|null
     */
    public function getQuoteCurrencyCode();	
    /**
     * Set Quote Currency Code
     *
     * @param string|null $quoteCurrencyCode
     * @return QuoteInterface
     */
    public function setQuoteCurrencyCode($quoteCurrencyCode);

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
     * @return QuoteInterface
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
     * @return QuoteInterface
     */
    public function setBaseGrandTotal($baseGrandTotal);

    /**
     * Get Checkout Method
     *
     * @return string|null
     */
    public function getCheckoutMethod();	
    /**
     * Set Checkout Method
     *
     * @param string|null $checkoutMethod
     * @return QuoteInterface
     */
    public function setCheckoutMethod($checkoutMethod);

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
     * @return QuoteInterface
     */
    public function setCustomerId($customerId);

    /**
     * Get Customer Tax Class Id
     *
     * @return int|null
     */
    public function getCustomerTaxClassId();	
    /**
     * Set Customer Tax Class Id
     *
     * @param int|null $customerTaxClassId
     * @return QuoteInterface
     */
    public function setCustomerTaxClassId($customerTaxClassId);

    /**
     * Get Customer Group Id
     *
     * @return int|null
     */
    public function getCustomerGroupId();	
    /**
     * Set Customer Group Id
     *
     * @param int|null $customerGroupId
     * @return QuoteInterface
     */
    public function setCustomerGroupId($customerGroupId);

    /**
     * Get Customer Email
     *
     * @return string|null
     */
    public function getCustomerEmail();	
    /**
     * Set Customer Email
     *
     * @param string|null $customerEmail
     * @return QuoteInterface
     */
    public function setCustomerEmail($customerEmail);

    /**
     * Get Customer Prefix
     *
     * @return string|null
     */
    public function getCustomerPrefix();	
    /**
     * Set Customer Prefix
     *
     * @param string|null $customerPrefix
     * @return QuoteInterface
     */
    public function setCustomerPrefix($customerPrefix);

    /**
     * Get Customer Firstname
     *
     * @return string|null
     */
    public function getCustomerFirstname();	
    /**
     * Set Customer Firstname
     *
     * @param string|null $customerFirstname
     * @return QuoteInterface
     */
    public function setCustomerFirstname($customerFirstname);

    /**
     * Get Customer Middlename
     *
     * @return string|null
     */
    public function getCustomerMiddlename();	
    /**
     * Set Customer Middlename
     *
     * @param string|null $customerMiddlename
     * @return QuoteInterface
     */
    public function setCustomerMiddlename($customerMiddlename);

    /**
     * Get Customer Lastname
     *
     * @return string|null
     */
    public function getCustomerLastname();	
    /**
     * Set Customer Lastname
     *
     * @param string|null $customerLastname
     * @return QuoteInterface
     */
    public function setCustomerLastname($customerLastname);

    /**
     * Get Customer Suffix
     *
     * @return string|null
     */
    public function getCustomerSuffix();	
    /**
     * Set Customer Suffix
     *
     * @param string|null $customerSuffix
     * @return QuoteInterface
     */
    public function setCustomerSuffix($customerSuffix);

    /**
     * Get Customer Dob
     *
     * @return string|null
     */
    public function getCustomerDob();	
    /**
     * Set Customer Dob
     *
     * @param string|null $customerDob
     * @return QuoteInterface
     */
    public function setCustomerDob($customerDob);

    /**
     * Get Customer Note
     *
     * @return string|null
     */
    public function getCustomerNote();	
    /**
     * Set Customer Note
     *
     * @param string|null $customerNote
     * @return QuoteInterface
     */
    public function setCustomerNote($customerNote);

    /**
     * Get Customer Note Notify
     *
     * @return int|null
     */
    public function getCustomerNoteNotify();	
    /**
     * Set Customer Note Notify
     *
     * @param int|null $customerNoteNotify
     * @return QuoteInterface
     */
    public function setCustomerNoteNotify($customerNoteNotify);

    /**
     * Get Customer Is Guest
     *
     * @return int|null
     */
    public function getCustomerIsGuest();	
    /**
     * Set Customer Is Guest
     *
     * @param int|null $customerIsGuest
     * @return QuoteInterface
     */
    public function setCustomerIsGuest($customerIsGuest);

    /**
     * Get Remote Ip
     *
     * @return string|null
     */
    public function getRemoteIp();	
    /**
     * Set Remote Ip
     *
     * @param string|null $remoteIp
     * @return QuoteInterface
     */
    public function setRemoteIp($remoteIp);

    /**
     * Get Applied Rule Ids
     *
     * @return string|null
     */
    public function getAppliedRuleIds();	
    /**
     * Set Applied Rule Ids
     *
     * @param string|null $appliedRuleIds
     * @return QuoteInterface
     */
    public function setAppliedRuleIds($appliedRuleIds);

    /**
     * Get Reserved Order Id
     *
     * @return string|null
     */
    public function getReservedOrderId();	
    /**
     * Set Reserved Order Id
     *
     * @param string|null $reservedOrderId
     * @return QuoteInterface
     */
    public function setReservedOrderId($reservedOrderId);

    /**
     * Get Password Hash
     *
     * @return string|null
     */
    public function getPasswordHash();	
    /**
     * Set Password Hash
     *
     * @param string|null $passwordHash
     * @return QuoteInterface
     */
    public function setPasswordHash($passwordHash);

    /**
     * Get Coupon Code
     *
     * @return string|null
     */
    public function getCouponCode();	
    /**
     * Set Coupon Code
     *
     * @param string|null $couponCode
     * @return QuoteInterface
     */
    public function setCouponCode($couponCode);

    /**
     * Get Global Currency Code
     *
     * @return string|null
     */
    public function getGlobalCurrencyCode();	
    /**
     * Set Global Currency Code
     *
     * @param string|null $globalCurrencyCode
     * @return QuoteInterface
     */
    public function setGlobalCurrencyCode($globalCurrencyCode);

    /**
     * Get Base To Global Rate
     *
     * @return float|null
     */
    public function getBaseToGlobalRate();	
    /**
     * Set Base To Global Rate
     *
     * @param float|null $baseToGlobalRate
     * @return QuoteInterface
     */
    public function setBaseToGlobalRate($baseToGlobalRate);

    /**
     * Get Base To Quote Rate
     *
     * @return float|null
     */
    public function getBaseToQuoteRate();	
    /**
     * Set Base To Quote Rate
     *
     * @param float|null $baseToQuoteRate
     * @return QuoteInterface
     */
    public function setBaseToQuoteRate($baseToQuoteRate);

    /**
     * Get Customer Taxvat
     *
     * @return string|null
     */
    public function getCustomerTaxvat();	
    /**
     * Set Customer Taxvat
     *
     * @param string|null $customerTaxvat
     * @return QuoteInterface
     */
    public function setCustomerTaxvat($customerTaxvat);

    /**
     * Get Customer Gender
     *
     * @return string|null
     */
    public function getCustomerGender();	
    /**
     * Set Customer Gender
     *
     * @param string|null $customerGender
     * @return QuoteInterface
     */
    public function setCustomerGender($customerGender);

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
     * @return QuoteInterface
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
     * @return QuoteInterface
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
     * @return QuoteInterface
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
     * @return QuoteInterface
     */
    public function setBaseSubtotalWithDiscount($baseSubtotalWithDiscount);

    /**
     * Get Is Changed
     *
     * @return int|null
     */
    public function getIsChanged();	
    /**
     * Set Is Changed
     *
     * @param int|null $isChanged
     * @return QuoteInterface
     */
    public function setIsChanged($isChanged);

    /**
     * Get Trigger Recollect
     *
     * @return int|null
     */
    public function getTriggerRecollect();	
    /**
     * Set Trigger Recollect
     *
     * @param int|null $triggerRecollect
     * @return QuoteInterface
     */
    public function setTriggerRecollect($triggerRecollect);

    /**
     * Get Ext Shipping Info
     *
     * @return string|null
     */
    public function getExtShippingInfo();	
    /**
     * Set Ext Shipping Info
     *
     * @param string|null $extShippingInfo
     * @return QuoteInterface
     */
    public function setExtShippingInfo($extShippingInfo);

    /**
     * Get Is Persistent
     *
     * @return int|null
     */
    public function getIsPersistent();	
    /**
     * Set Is Persistent
     *
     * @param int|null $isPersistent
     * @return QuoteInterface
     */
    public function setIsPersistent($isPersistent);

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
     * @return QuoteInterface
     */
    public function setGiftMessageId($giftMessageId);

    /**
     * Get addresses
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\AddressInterface[]|null
     */
    public function getAddresses();
    /**
     * Set addresses
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\Quote\AddressInterface[]|null $addresses
     * @return QuoteInterface
     */
    public function setAddresses($addresses);

    /**
     * Get items
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface[]|null
     */
    public function getItems();
    /**
     * Set items
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface[]|null $items
     * @return QuoteInterface
     */
    public function setItems($items);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\QuoteExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\QuoteExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Checkout\QuoteExtensionInterface $extensionAttributes
    );
}