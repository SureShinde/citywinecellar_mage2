<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Checkout;

interface OrderInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ENTITY_ID = 'entity_id';
    const STATE = 'state';
    const STATUS = 'status';
    const COUPON_CODE = 'coupon_code';
    const PROTECT_CODE = 'protect_code';
    const SHIPPING_DESCRIPTION = 'shipping_description';
    const IS_VIRTUAL = 'is_virtual';
    const STORE_ID = 'store_id';
    const CUSTOMER_ID = 'customer_id';
    const BASE_DISCOUNT_AMOUNT = 'base_discount_amount';
    const BASE_DISCOUNT_CANCELED = 'base_discount_canceled';
    const BASE_DISCOUNT_INVOICED = 'base_discount_invoiced';
    const BASE_DISCOUNT_REFUNDED = 'base_discount_refunded';
    const BASE_GRAND_TOTAL = 'base_grand_total';
    const BASE_SHIPPING_AMOUNT = 'base_shipping_amount';
    const BASE_SHIPPING_CANCELED = 'base_shipping_canceled';
    const BASE_SHIPPING_INVOICED = 'base_shipping_invoiced';
    const BASE_SHIPPING_REFUNDED = 'base_shipping_refunded';
    const BASE_SHIPPING_TAX_AMOUNT = 'base_shipping_tax_amount';
    const BASE_SHIPPING_TAX_REFUNDED = 'base_shipping_tax_refunded';
    const BASE_SUBTOTAL = 'base_subtotal';
    const BASE_SUBTOTAL_CANCELED = 'base_subtotal_canceled';
    const BASE_SUBTOTAL_INVOICED = 'base_subtotal_invoiced';
    const BASE_SUBTOTAL_REFUNDED = 'base_subtotal_refunded';
    const BASE_TAX_AMOUNT = 'base_tax_amount';
    const BASE_TAX_CANCELED = 'base_tax_canceled';
    const BASE_TAX_INVOICED = 'base_tax_invoiced';
    const BASE_TAX_REFUNDED = 'base_tax_refunded';
    const BASE_TO_GLOBAL_RATE = 'base_to_global_rate';
    const BASE_TO_ORDER_RATE = 'base_to_order_rate';
    const BASE_TOTAL_CANCELED = 'base_total_canceled';
    const BASE_TOTAL_INVOICED = 'base_total_invoiced';
    const BASE_TOTAL_INVOICED_COST = 'base_total_invoiced_cost';
    const BASE_TOTAL_OFFLINE_REFUNDED = 'base_total_offline_refunded';
    const BASE_TOTAL_ONLINE_REFUNDED = 'base_total_online_refunded';
    const BASE_TOTAL_PAID = 'base_total_paid';
    const BASE_TOTAL_QTY_ORDERED = 'base_total_qty_ordered';
    const BASE_TOTAL_REFUNDED = 'base_total_refunded';
    const DISCOUNT_AMOUNT = 'discount_amount';
    const DISCOUNT_CANCELED = 'discount_canceled';
    const DISCOUNT_INVOICED = 'discount_invoiced';
    const DISCOUNT_REFUNDED = 'discount_refunded';
    const GRAND_TOTAL = 'grand_total';
    const SHIPPING_AMOUNT = 'shipping_amount';
    const SHIPPING_CANCELED = 'shipping_canceled';
    const SHIPPING_INVOICED = 'shipping_invoiced';
    const SHIPPING_REFUNDED = 'shipping_refunded';
    const SHIPPING_TAX_AMOUNT = 'shipping_tax_amount';
    const SHIPPING_TAX_REFUNDED = 'shipping_tax_refunded';
    const STORE_TO_BASE_RATE = 'store_to_base_rate';
    const STORE_TO_ORDER_RATE = 'store_to_order_rate';
    const SUBTOTAL = 'subtotal';
    const SUBTOTAL_CANCELED = 'subtotal_canceled';
    const SUBTOTAL_INVOICED = 'subtotal_invoiced';
    const SUBTOTAL_REFUNDED = 'subtotal_refunded';
    const TAX_AMOUNT = 'tax_amount';
    const TAX_CANCELED = 'tax_canceled';
    const TAX_INVOICED = 'tax_invoiced';
    const TAX_REFUNDED = 'tax_refunded';
    const TOTAL_CANCELED = 'total_canceled';
    const TOTAL_INVOICED = 'total_invoiced';
    const TOTAL_OFFLINE_REFUNDED = 'total_offline_refunded';
    const TOTAL_ONLINE_REFUNDED = 'total_online_refunded';
    const TOTAL_PAID = 'total_paid';
    const TOTAL_QTY_ORDERED = 'total_qty_ordered';
    const TOTAL_REFUNDED = 'total_refunded';
    const CAN_SHIP_PARTIALLY = 'can_ship_partially';
    const CAN_SHIP_PARTIALLY_ITEM = 'can_ship_partially_item';
    const CUSTOMER_IS_GUEST = 'customer_is_guest';
    const CUSTOMER_NOTE_NOTIFY = 'customer_note_notify';
    const BILLING_ADDRESS_ID = 'billing_address_id';
    const CUSTOMER_GROUP_ID = 'customer_group_id';
    const EDIT_INCREMENT = 'edit_increment';
    const EMAIL_SENT = 'email_sent';
    const SEND_EMAIL = 'send_email';
    const FORCED_SHIPMENT_WITH_INVOICE = 'forced_shipment_with_invoice';
    const PAYMENT_AUTH_EXPIRATION = 'payment_auth_expiration';
    const QUOTE_ADDRESS_ID = 'quote_address_id';
    const QUOTE_ID = 'quote_id';
    const SHIPPING_ADDRESS_ID = 'shipping_address_id';
    const ADJUSTMENT_NEGATIVE = 'adjustment_negative';
    const ADJUSTMENT_POSITIVE = 'adjustment_positive';
    const BASE_ADJUSTMENT_NEGATIVE = 'base_adjustment_negative';
    const BASE_ADJUSTMENT_POSITIVE = 'base_adjustment_positive';
    const BASE_SHIPPING_DISCOUNT_AMOUNT = 'base_shipping_discount_amount';
    const BASE_SUBTOTAL_INCL_TAX = 'base_subtotal_incl_tax';
    const BASE_TOTAL_DUE = 'base_total_due';
    const PAYMENT_AUTHORIZATION_AMOUNT = 'payment_authorization_amount';
    const SHIPPING_DISCOUNT_AMOUNT = 'shipping_discount_amount';
    const SUBTOTAL_INCL_TAX = 'subtotal_incl_tax';
    const TOTAL_DUE = 'total_due';
    const WEIGHT = 'weight';
    const CUSTOMER_DOB = 'customer_dob';
    const INCREMENT_ID = 'increment_id';
    const APPLIED_RULE_IDS = 'applied_rule_ids';
    const BASE_CURRENCY_CODE = 'base_currency_code';
    const CUSTOMER_EMAIL = 'customer_email';
    const CUSTOMER_FIRSTNAME = 'customer_firstname';
    const CUSTOMER_LASTNAME = 'customer_lastname';
    const CUSTOMER_MIDDLENAME = 'customer_middlename';
    const CUSTOMER_PREFIX = 'customer_prefix';
    const CUSTOMER_SUFFIX = 'customer_suffix';
    const CUSTOMER_TAXVAT = 'customer_taxvat';
    const DISCOUNT_DESCRIPTION = 'discount_description';
    const EXT_CUSTOMER_ID = 'ext_customer_id';
    const EXT_ORDER_ID = 'ext_order_id';
    const GLOBAL_CURRENCY_CODE = 'global_currency_code';
    const HOLD_BEFORE_STATE = 'hold_before_state';
    const HOLD_BEFORE_STATUS = 'hold_before_status';
    const ORDER_CURRENCY_CODE = 'order_currency_code';
    const ORIGINAL_INCREMENT_ID = 'original_increment_id';
    const RELATION_CHILD_ID = 'relation_child_id';
    const RELATION_CHILD_REAL_ID = 'relation_child_real_id';
    const RELATION_PARENT_ID = 'relation_parent_id';
    const RELATION_PARENT_REAL_ID = 'relation_parent_real_id';
    const REMOTE_IP = 'remote_ip';
    const SHIPPING_METHOD = 'shipping_method';
    const STORE_CURRENCY_CODE = 'store_currency_code';
    const STORE_NAME = 'store_name';
    const X_FORWARDED_FOR = 'x_forwarded_for';
    const CUSTOMER_NOTE = 'customer_note';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const TOTAL_ITEM_COUNT = 'total_item_count';
    const CUSTOMER_GENDER = 'customer_gender';
    const DISCOUNT_TAX_COMPENSATION_AMOUNT = 'discount_tax_compensation_amount';
    const BASE_DISCOUNT_TAX_COMPENSATION_AMOUNT = 'base_discount_tax_compensation_amount';
    const SHIPPING_DISCOUNT_TAX_COMPENSATION_AMOUNT = 'shipping_discount_tax_compensation_amount';
    const BASE_SHIPPING_DISCOUNT_TAX_COMPENSATION_AMNT = 'base_shipping_discount_tax_compensation_amnt';
    const DISCOUNT_TAX_COMPENSATION_INVOICED = 'discount_tax_compensation_invoiced';
    const BASE_DISCOUNT_TAX_COMPENSATION_INVOICED = 'base_discount_tax_compensation_invoiced';
    const DISCOUNT_TAX_COMPENSATION_REFUNDED = 'discount_tax_compensation_refunded';
    const BASE_DISCOUNT_TAX_COMPENSATION_REFUNDED = 'base_discount_tax_compensation_refunded';
    const SHIPPING_INCL_TAX = 'shipping_incl_tax';
    const BASE_SHIPPING_INCL_TAX = 'base_shipping_incl_tax';
    const COUPON_RULE_NAME = 'coupon_rule_name';
    const GIFT_MESSAGE_ID = 'gift_message_id';
    const ITEMS = 'items';
    const ADDRESSES = 'addresses';
    const PAYMENT = 'payment';
    const POS_ID = 'pos_id';
    const POS_LOCATION_ID = 'pos_location_id';
    const POS_STAFF_ID = 'pos_staff_id';
    const POS_STAFF_NAME = 'pos_staff_name';
    const POS_DELIVERY_DATE = 'pos_delivery_date';
    const SEARCH_STRING = 'search_string';
    const STATUS_HISTORIES = 'status_histories';
    const POS_FULFILL_ONLINE = 'pos_fulfill_online';
    const POS_CHANGE = 'pos_change';
    const BASE_POS_CHANGE = 'base_pos_change';
    const PAYPAL_IPN_CUSTOMER_NOTIFIED = 'paypal_ipn_customer_notified';
    const MAGESTORE_BASE_DISCOUNT_FOR_SHIPPING = 'magestore_base_discount_for_shipping';
    const MAGESTORE_DISCOUNT_FOR_SHIPPING = 'magestore_discount_for_shipping';
    const MAGESTORE_BASE_DISCOUNT = 'magestore_base_discount';
    const MAGESTORE_DISCOUNT = 'magestore_discount';
    const WAREHOUSE_ID = 'warehouse_id';
    const CODES_DISCOUNT = 'codes_discount';
    const CODES_BASE_DISCOUNT = 'codes_base_discount';
    const GIFT_VOUCHER_GIFT_CODES_DISCOUNT = 'gift_voucher_gift_codes_discount';
    const GIFT_VOUCHER_GIFT_CODES = 'gift_voucher_gift_codes';
    const GIFT_VOUCHER_DISCOUNT = 'gift_voucher_discount';
    const BASE_GIFT_VOUCHER_DISCOUNT = 'base_gift_voucher_discount';
    const GIFTVOUCHER_DISCOUNT_FOR_SHIPPING = 'giftvoucher_discount_for_shipping';
    const BASE_GIFTVOUCHER_DISCOUNT_FOR_SHIPPING = 'base_giftvoucher_discount_for_shipping';
    const POS_PRE_TOTAL_PAID = 'pos_pre_total_paid';
    const POS_BASE_PRE_TOTAL_PAID = 'pos_base_pre_total_paid';
    const GIFTCODES_APPLIED_DISCOUNT_FOR_SHIPPING = 'giftcodes_applied_discount_for_shipping';
    const APPLIED_TAXES = '_applied_taxes';

    const OS_POS_CUSTOM_DISCOUNT_REASON = 'os_pos_custom_discount_reason';
    const OS_POS_CUSTOM_DISCOUNT_TYPE = 'os_pos_custom_discount_type';
    const OS_POS_CUSTOM_DISCOUNT_AMOUNT = 'os_pos_custom_discount_amount';

    const TMP_CUSTOMER_ID = 'tmp_customer_id';

    const SEARCH_ATTRIBUTES = [
        'increment_id',
    ];

    const SEARCH_BY_PRODUCT_ATTRIBUTES = [
        'name',
        'sku'
    ];

    const SEARCH_BY_CUSTOMER_ATTRIBUTES = [
        'email',
        'telephone',
        'firstname',
        'middlename',
        'lastname',
    ];

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
     * @return OrderInterface
     */
    public function setEntityId($entityId);

    /**
     * Get State
     *
     * @return string|null
     */
    public function getState();
    /**
     * Set State
     *
     * @param string|null $state
     * @return OrderInterface
     */
    public function setState($state);

    /**
     * Get Status
     *
     * @return string|null
     */
    public function getStatus();
    /**
     * Set Status
     *
     * @param string|null $status
     * @return OrderInterface
     */
    public function setStatus($status);

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
     * @return OrderInterface
     */
    public function setCouponCode($couponCode);

    /**
     * Get Protect Code
     *
     * @return string|null
     */
    public function getProtectCode();
    /**
     * Set Protect Code
     *
     * @param string|null $protectCode
     * @return OrderInterface
     */
    public function setProtectCode($protectCode);

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
     * @return OrderInterface
     */
    public function setShippingDescription($shippingDescription);

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
     * @return OrderInterface
     */
    public function setIsVirtual($isVirtual);

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
     * @return OrderInterface
     */
    public function setStoreId($storeId);

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
     * @return OrderInterface
     */
    public function setCustomerId($customerId);

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
     * @return OrderInterface
     */
    public function setBaseDiscountAmount($baseDiscountAmount);

    /**
     * Get Base Discount Canceled
     *
     * @return float|null
     */
    public function getBaseDiscountCanceled();
    /**
     * Set Base Discount Canceled
     *
     * @param float|null $baseDiscountCanceled
     * @return OrderInterface
     */
    public function setBaseDiscountCanceled($baseDiscountCanceled);

    /**
     * Get Base Discount Invoiced
     *
     * @return float|null
     */
    public function getBaseDiscountInvoiced();
    /**
     * Set Base Discount Invoiced
     *
     * @param float|null $baseDiscountInvoiced
     * @return OrderInterface
     */
    public function setBaseDiscountInvoiced($baseDiscountInvoiced);

    /**
     * Get Base Discount Refunded
     *
     * @return float|null
     */
    public function getBaseDiscountRefunded();
    /**
     * Set Base Discount Refunded
     *
     * @param float|null $baseDiscountRefunded
     * @return OrderInterface
     */
    public function setBaseDiscountRefunded($baseDiscountRefunded);

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
     * @return OrderInterface
     */
    public function setBaseGrandTotal($baseGrandTotal);

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
     * @return OrderInterface
     */
    public function setBaseShippingAmount($baseShippingAmount);

    /**
     * Get Base Shipping Canceled
     *
     * @return float|null
     */
    public function getBaseShippingCanceled();
    /**
     * Set Base Shipping Canceled
     *
     * @param float|null $baseShippingCanceled
     * @return OrderInterface
     */
    public function setBaseShippingCanceled($baseShippingCanceled);

    /**
     * Get Base Shipping Invoiced
     *
     * @return float|null
     */
    public function getBaseShippingInvoiced();
    /**
     * Set Base Shipping Invoiced
     *
     * @param float|null $baseShippingInvoiced
     * @return OrderInterface
     */
    public function setBaseShippingInvoiced($baseShippingInvoiced);

    /**
     * Get Base Shipping Refunded
     *
     * @return float|null
     */
    public function getBaseShippingRefunded();
    /**
     * Set Base Shipping Refunded
     *
     * @param float|null $baseShippingRefunded
     * @return OrderInterface
     */
    public function setBaseShippingRefunded($baseShippingRefunded);

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
     * @return OrderInterface
     */
    public function setBaseShippingTaxAmount($baseShippingTaxAmount);

    /**
     * Get Base Shipping Tax Refunded
     *
     * @return float|null
     */
    public function getBaseShippingTaxRefunded();
    /**
     * Set Base Shipping Tax Refunded
     *
     * @param float|null $baseShippingTaxRefunded
     * @return OrderInterface
     */
    public function setBaseShippingTaxRefunded($baseShippingTaxRefunded);

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
     * @return OrderInterface
     */
    public function setBaseSubtotal($baseSubtotal);

    /**
     * Get Base Subtotal Canceled
     *
     * @return float|null
     */
    public function getBaseSubtotalCanceled();
    /**
     * Set Base Subtotal Canceled
     *
     * @param float|null $baseSubtotalCanceled
     * @return OrderInterface
     */
    public function setBaseSubtotalCanceled($baseSubtotalCanceled);

    /**
     * Get Base Subtotal Invoiced
     *
     * @return float|null
     */
    public function getBaseSubtotalInvoiced();
    /**
     * Set Base Subtotal Invoiced
     *
     * @param float|null $baseSubtotalInvoiced
     * @return OrderInterface
     */
    public function setBaseSubtotalInvoiced($baseSubtotalInvoiced);

    /**
     * Get Base Subtotal Refunded
     *
     * @return float|null
     */
    public function getBaseSubtotalRefunded();
    /**
     * Set Base Subtotal Refunded
     *
     * @param float|null $baseSubtotalRefunded
     * @return OrderInterface
     */
    public function setBaseSubtotalRefunded($baseSubtotalRefunded);

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
     * @return OrderInterface
     */
    public function setBaseTaxAmount($baseTaxAmount);

    /**
     * Get Base Tax Canceled
     *
     * @return float|null
     */
    public function getBaseTaxCanceled();
    /**
     * Set Base Tax Canceled
     *
     * @param float|null $baseTaxCanceled
     * @return OrderInterface
     */
    public function setBaseTaxCanceled($baseTaxCanceled);

    /**
     * Get Base Tax Invoiced
     *
     * @return float|null
     */
    public function getBaseTaxInvoiced();
    /**
     * Set Base Tax Invoiced
     *
     * @param float|null $baseTaxInvoiced
     * @return OrderInterface
     */
    public function setBaseTaxInvoiced($baseTaxInvoiced);

    /**
     * Get Base Tax Refunded
     *
     * @return float|null
     */
    public function getBaseTaxRefunded();
    /**
     * Set Base Tax Refunded
     *
     * @param float|null $baseTaxRefunded
     * @return OrderInterface
     */
    public function setBaseTaxRefunded($baseTaxRefunded);

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
     * @return OrderInterface
     */
    public function setBaseToGlobalRate($baseToGlobalRate);

    /**
     * Get Base To Order Rate
     *
     * @return float|null
     */
    public function getBaseToOrderRate();
    /**
     * Set Base To Order Rate
     *
     * @param float|null $baseToOrderRate
     * @return OrderInterface
     */
    public function setBaseToOrderRate($baseToOrderRate);

    /**
     * Get Base Total Canceled
     *
     * @return float|null
     */
    public function getBaseTotalCanceled();
    /**
     * Set Base Total Canceled
     *
     * @param float|null $baseTotalCanceled
     * @return OrderInterface
     */
    public function setBaseTotalCanceled($baseTotalCanceled);

    /**
     * Get Base Total Invoiced
     *
     * @return float|null
     */
    public function getBaseTotalInvoiced();
    /**
     * Set Base Total Invoiced
     *
     * @param float|null $baseTotalInvoiced
     * @return OrderInterface
     */
    public function setBaseTotalInvoiced($baseTotalInvoiced);

    /**
     * Get Base Total Invoiced Cost
     *
     * @return float|null
     */
    public function getBaseTotalInvoicedCost();
    /**
     * Set Base Total Invoiced Cost
     *
     * @param float|null $baseTotalInvoicedCost
     * @return OrderInterface
     */
    public function setBaseTotalInvoicedCost($baseTotalInvoicedCost);

    /**
     * Get Base Total Offline Refunded
     *
     * @return float|null
     */
    public function getBaseTotalOfflineRefunded();
    /**
     * Set Base Total Offline Refunded
     *
     * @param float|null $baseTotalOfflineRefunded
     * @return OrderInterface
     */
    public function setBaseTotalOfflineRefunded($baseTotalOfflineRefunded);

    /**
     * Get Base Total Online Refunded
     *
     * @return float|null
     */
    public function getBaseTotalOnlineRefunded();
    /**
     * Set Base Total Online Refunded
     *
     * @param float|null $baseTotalOnlineRefunded
     * @return OrderInterface
     */
    public function setBaseTotalOnlineRefunded($baseTotalOnlineRefunded);

    /**
     * Get Base Total Paid
     *
     * @return float|null
     */
    public function getBaseTotalPaid();
    /**
     * Set Base Total Paid
     *
     * @param float|null $baseTotalPaid
     * @return OrderInterface
     */
    public function setBaseTotalPaid($baseTotalPaid);

    /**
     * Get Base Total Qty Ordered
     *
     * @return float|null
     */
    public function getBaseTotalQtyOrdered();
    /**
     * Set Base Total Qty Ordered
     *
     * @param float|null $baseTotalQtyOrdered
     * @return OrderInterface
     */
    public function setBaseTotalQtyOrdered($baseTotalQtyOrdered);

    /**
     * Get Base Total Refunded
     *
     * @return float|null
     */
    public function getBaseTotalRefunded();
    /**
     * Set Base Total Refunded
     *
     * @param float|null $baseTotalRefunded
     * @return OrderInterface
     */
    public function setBaseTotalRefunded($baseTotalRefunded);

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
     * @return OrderInterface
     */
    public function setDiscountAmount($discountAmount);

    /**
     * Get Discount Canceled
     *
     * @return float|null
     */
    public function getDiscountCanceled();
    /**
     * Set Discount Canceled
     *
     * @param float|null $discountCanceled
     * @return OrderInterface
     */
    public function setDiscountCanceled($discountCanceled);

    /**
     * Get Discount Invoiced
     *
     * @return float|null
     */
    public function getDiscountInvoiced();
    /**
     * Set Discount Invoiced
     *
     * @param float|null $discountInvoiced
     * @return OrderInterface
     */
    public function setDiscountInvoiced($discountInvoiced);

    /**
     * Get Discount Refunded
     *
     * @return float|null
     */
    public function getDiscountRefunded();
    /**
     * Set Discount Refunded
     *
     * @param float|null $discountRefunded
     * @return OrderInterface
     */
    public function setDiscountRefunded($discountRefunded);

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
     * @return OrderInterface
     */
    public function setGrandTotal($grandTotal);

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
     * @return OrderInterface
     */
    public function setShippingAmount($shippingAmount);

    /**
     * Get Shipping Canceled
     *
     * @return float|null
     */
    public function getShippingCanceled();
    /**
     * Set Shipping Canceled
     *
     * @param float|null $shippingCanceled
     * @return OrderInterface
     */
    public function setShippingCanceled($shippingCanceled);

    /**
     * Get Shipping Invoiced
     *
     * @return float|null
     */
    public function getShippingInvoiced();
    /**
     * Set Shipping Invoiced
     *
     * @param float|null $shippingInvoiced
     * @return OrderInterface
     */
    public function setShippingInvoiced($shippingInvoiced);

    /**
     * Get Shipping Refunded
     *
     * @return float|null
     */
    public function getShippingRefunded();
    /**
     * Set Shipping Refunded
     *
     * @param float|null $shippingRefunded
     * @return OrderInterface
     */
    public function setShippingRefunded($shippingRefunded);

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
     * @return OrderInterface
     */
    public function setShippingTaxAmount($shippingTaxAmount);

    /**
     * Get Shipping Tax Refunded
     *
     * @return float|null
     */
    public function getShippingTaxRefunded();
    /**
     * Set Shipping Tax Refunded
     *
     * @param float|null $shippingTaxRefunded
     * @return OrderInterface
     */
    public function setShippingTaxRefunded($shippingTaxRefunded);

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
     * @return OrderInterface
     */
    public function setStoreToBaseRate($storeToBaseRate);

    /**
     * Get Store To Order Rate
     *
     * @return float|null
     */
    public function getStoreToOrderRate();
    /**
     * Set Store To Order Rate
     *
     * @param float|null $storeToOrderRate
     * @return OrderInterface
     */
    public function setStoreToOrderRate($storeToOrderRate);

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
     * @return OrderInterface
     */
    public function setSubtotal($subtotal);

    /**
     * Get Subtotal Canceled
     *
     * @return float|null
     */
    public function getSubtotalCanceled();
    /**
     * Set Subtotal Canceled
     *
     * @param float|null $subtotalCanceled
     * @return OrderInterface
     */
    public function setSubtotalCanceled($subtotalCanceled);

    /**
     * Get Subtotal Invoiced
     *
     * @return float|null
     */
    public function getSubtotalInvoiced();
    /**
     * Set Subtotal Invoiced
     *
     * @param float|null $subtotalInvoiced
     * @return OrderInterface
     */
    public function setSubtotalInvoiced($subtotalInvoiced);

    /**
     * Get Subtotal Refunded
     *
     * @return float|null
     */
    public function getSubtotalRefunded();
    /**
     * Set Subtotal Refunded
     *
     * @param float|null $subtotalRefunded
     * @return OrderInterface
     */
    public function setSubtotalRefunded($subtotalRefunded);

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
     * @return OrderInterface
     */
    public function setTaxAmount($taxAmount);

    /**
     * Get Tax Canceled
     *
     * @return float|null
     */
    public function getTaxCanceled();
    /**
     * Set Tax Canceled
     *
     * @param float|null $taxCanceled
     * @return OrderInterface
     */
    public function setTaxCanceled($taxCanceled);

    /**
     * Get Tax Invoiced
     *
     * @return float|null
     */
    public function getTaxInvoiced();
    /**
     * Set Tax Invoiced
     *
     * @param float|null $taxInvoiced
     * @return OrderInterface
     */
    public function setTaxInvoiced($taxInvoiced);

    /**
     * Get Tax Refunded
     *
     * @return float|null
     */
    public function getTaxRefunded();
    /**
     * Set Tax Refunded
     *
     * @param float|null $taxRefunded
     * @return OrderInterface
     */
    public function setTaxRefunded($taxRefunded);

    /**
     * Get Total Canceled
     *
     * @return float|null
     */
    public function getTotalCanceled();
    /**
     * Set Total Canceled
     *
     * @param float|null $totalCanceled
     * @return OrderInterface
     */
    public function setTotalCanceled($totalCanceled);

    /**
     * Get Total Invoiced
     *
     * @return float|null
     */
    public function getTotalInvoiced();
    /**
     * Set Total Invoiced
     *
     * @param float|null $totalInvoiced
     * @return OrderInterface
     */
    public function setTotalInvoiced($totalInvoiced);

    /**
     * Get Total Offline Refunded
     *
     * @return float|null
     */
    public function getTotalOfflineRefunded();
    /**
     * Set Total Offline Refunded
     *
     * @param float|null $totalOfflineRefunded
     * @return OrderInterface
     */
    public function setTotalOfflineRefunded($totalOfflineRefunded);

    /**
     * Get Total Online Refunded
     *
     * @return float|null
     */
    public function getTotalOnlineRefunded();
    /**
     * Set Total Online Refunded
     *
     * @param float|null $totalOnlineRefunded
     * @return OrderInterface
     */
    public function setTotalOnlineRefunded($totalOnlineRefunded);

    /**
     * Get Total Paid
     *
     * @return float|null
     */
    public function getTotalPaid();
    /**
     * Set Total Paid
     *
     * @param float|null $totalPaid
     * @return OrderInterface
     */
    public function setTotalPaid($totalPaid);

    /**
     * Get Total Qty Ordered
     *
     * @return float|null
     */
    public function getTotalQtyOrdered();
    /**
     * Set Total Qty Ordered
     *
     * @param float|null $totalQtyOrdered
     * @return OrderInterface
     */
    public function setTotalQtyOrdered($totalQtyOrdered);

    /**
     * Get Total Refunded
     *
     * @return float|null
     */
    public function getTotalRefunded();
    /**
     * Set Total Refunded
     *
     * @param float|null $totalRefunded
     * @return OrderInterface
     */
    public function setTotalRefunded($totalRefunded);

    /**
     * Get Can Ship Partially
     *
     * @return int|null
     */
    public function getCanShipPartially();
    /**
     * Set Can Ship Partially
     *
     * @param int|null $canShipPartially
     * @return OrderInterface
     */
    public function setCanShipPartially($canShipPartially);

    /**
     * Get Can Ship Partially Item
     *
     * @return int|null
     */
    public function getCanShipPartiallyItem();
    /**
     * Set Can Ship Partially Item
     *
     * @param int|null $canShipPartiallyItem
     * @return OrderInterface
     */
    public function setCanShipPartiallyItem($canShipPartiallyItem);

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
     * @return OrderInterface
     */
    public function setCustomerIsGuest($customerIsGuest);

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
     * @return OrderInterface
     */
    public function setCustomerNoteNotify($customerNoteNotify);

    /**
     * Get Billing Address Id
     *
     * @return int|null
     */
    public function getBillingAddressId();
    /**
     * Set Billing Address Id
     *
     * @param int|null $billingAddressId
     * @return OrderInterface
     */
    public function setBillingAddressId($billingAddressId);

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
     * @return OrderInterface
     */
    public function setCustomerGroupId($customerGroupId);

    /**
     * Get Edit Increment
     *
     * @return int|null
     */
    public function getEditIncrement();
    /**
     * Set Edit Increment
     *
     * @param int|null $editIncrement
     * @return OrderInterface
     */
    public function setEditIncrement($editIncrement);

    /**
     * Get Email Sent
     *
     * @return int|null
     */
    public function getEmailSent();
    /**
     * Set Email Sent
     *
     * @param int|null $emailSent
     * @return OrderInterface
     */
    public function setEmailSent($emailSent);

    /**
     * Get Send Email
     *
     * @return int|null
     */
    public function getSendEmail();
    /**
     * Set Send Email
     *
     * @param int|null $sendEmail
     * @return OrderInterface
     */
    public function setSendEmail($sendEmail);

    /**
     * Get Forced Shipment With Invoice
     *
     * @return int|null
     */
    public function getForcedShipmentWithInvoice();
    /**
     * Set Forced Shipment With Invoice
     *
     * @param int|null $forcedShipmentWithInvoice
     * @return OrderInterface
     */
    public function setForcedShipmentWithInvoice($forcedShipmentWithInvoice);

    /**
     * Get Payment Auth Expiration
     *
     * @return int|null
     */
    public function getPaymentAuthExpiration();
    /**
     * Set Payment Auth Expiration
     *
     * @param int|null $paymentAuthExpiration
     * @return OrderInterface
     */
    public function setPaymentAuthExpiration($paymentAuthExpiration);

    /**
     * Get Quote Address Id
     *
     * @return int|null
     */
    public function getQuoteAddressId();
    /**
     * Set Quote Address Id
     *
     * @param int|null $quoteAddressId
     * @return OrderInterface
     */
    public function setQuoteAddressId($quoteAddressId);

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
     * @return OrderInterface
     */
    public function setQuoteId($quoteId);

    /**
     * Get Shipping Address Id
     *
     * @return int|null
     */
    public function getShippingAddressId();
    /**
     * Set Shipping Address Id
     *
     * @param int|null $shippingAddressId
     * @return OrderInterface
     */
    public function setShippingAddressId($shippingAddressId);

    /**
     * Get Adjustment Negative
     *
     * @return float|null
     */
    public function getAdjustmentNegative();
    /**
     * Set Adjustment Negative
     *
     * @param float|null $adjustmentNegative
     * @return OrderInterface
     */
    public function setAdjustmentNegative($adjustmentNegative);

    /**
     * Get Adjustment Positive
     *
     * @return float|null
     */
    public function getAdjustmentPositive();
    /**
     * Set Adjustment Positive
     *
     * @param float|null $adjustmentPositive
     * @return OrderInterface
     */
    public function setAdjustmentPositive($adjustmentPositive);

    /**
     * Get Base Adjustment Negative
     *
     * @return float|null
     */
    public function getBaseAdjustmentNegative();
    /**
     * Set Base Adjustment Negative
     *
     * @param float|null $baseAdjustmentNegative
     * @return OrderInterface
     */
    public function setBaseAdjustmentNegative($baseAdjustmentNegative);

    /**
     * Get Base Adjustment Positive
     *
     * @return float|null
     */
    public function getBaseAdjustmentPositive();
    /**
     * Set Base Adjustment Positive
     *
     * @param float|null $baseAdjustmentPositive
     * @return OrderInterface
     */
    public function setBaseAdjustmentPositive($baseAdjustmentPositive);

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
     * @return OrderInterface
     */
    public function setBaseShippingDiscountAmount($baseShippingDiscountAmount);

    /**
     * Get Base Subtotal Incl Tax
     *
     * @return float|null
     */
    public function getBaseSubtotalInclTax();
    /**
     * Set Base Subtotal Incl Tax
     *
     * @param float|null $baseSubtotalInclTax
     * @return OrderInterface
     */
    public function setBaseSubtotalInclTax($baseSubtotalInclTax);

    /**
     * Get Base Total Due
     *
     * @return float|null
     */
    public function getBaseTotalDue();
    /**
     * Set Base Total Due
     *
     * @param float|null $baseTotalDue
     * @return OrderInterface
     */
    public function setBaseTotalDue($baseTotalDue);

    /**
     * Get Payment Authorization Amount
     *
     * @return float|null
     */
    public function getPaymentAuthorizationAmount();
    /**
     * Set Payment Authorization Amount
     *
     * @param float|null $paymentAuthorizationAmount
     * @return OrderInterface
     */
    public function setPaymentAuthorizationAmount($paymentAuthorizationAmount);

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
     * @return OrderInterface
     */
    public function setShippingDiscountAmount($shippingDiscountAmount);

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
     * @return OrderInterface
     */
    public function setSubtotalInclTax($subtotalInclTax);

    /**
     * Get Total Due
     *
     * @return float|null
     */
    public function getTotalDue();
    /**
     * Set Total Due
     *
     * @param float|null $totalDue
     * @return OrderInterface
     */
    public function setTotalDue($totalDue);

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
     * @return OrderInterface
     */
    public function setWeight($weight);

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
     * @return OrderInterface
     */
    public function setCustomerDob($customerDob);

    /**
     * Get Increment Id
     *
     * @return string|null
     */
    public function getIncrementId();
    /**
     * Set Increment Id
     *
     * @param string|null $incrementId
     * @return OrderInterface
     */
    public function setIncrementId($incrementId);

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
     * @return OrderInterface
     */
    public function setAppliedRuleIds($appliedRuleIds);

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
     * @return OrderInterface
     */
    public function setBaseCurrencyCode($baseCurrencyCode);

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
     * @return OrderInterface
     */
    public function setCustomerEmail($customerEmail);

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
     * @return OrderInterface
     */
    public function setCustomerFirstname($customerFirstname);

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
     * @return OrderInterface
     */
    public function setCustomerLastname($customerLastname);

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
     * @return OrderInterface
     */
    public function setCustomerMiddlename($customerMiddlename);

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
     * @return OrderInterface
     */
    public function setCustomerPrefix($customerPrefix);

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
     * @return OrderInterface
     */
    public function setCustomerSuffix($customerSuffix);

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
     * @return OrderInterface
     */
    public function setCustomerTaxvat($customerTaxvat);

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
     * @return OrderInterface
     */
    public function setDiscountDescription($discountDescription);

    /**
     * Get Ext Customer Id
     *
     * @return string|null
     */
    public function getExtCustomerId();
    /**
     * Set Ext Customer Id
     *
     * @param string|null $extCustomerId
     * @return OrderInterface
     */
    public function setExtCustomerId($extCustomerId);

    /**
     * Get Ext Order Id
     *
     * @return string|null
     */
    public function getExtOrderId();
    /**
     * Set Ext Order Id
     *
     * @param string|null $extOrderId
     * @return OrderInterface
     */
    public function setExtOrderId($extOrderId);

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
     * @return OrderInterface
     */
    public function setGlobalCurrencyCode($globalCurrencyCode);

    /**
     * Get Hold Before State
     *
     * @return string|null
     */
    public function getHoldBeforeState();
    /**
     * Set Hold Before State
     *
     * @param string|null $holdBeforeState
     * @return OrderInterface
     */
    public function setHoldBeforeState($holdBeforeState);

    /**
     * Get Hold Before Status
     *
     * @return string|null
     */
    public function getHoldBeforeStatus();
    /**
     * Set Hold Before Status
     *
     * @param string|null $holdBeforeStatus
     * @return OrderInterface
     */
    public function setHoldBeforeStatus($holdBeforeStatus);

    /**
     * Get Order Currency Code
     *
     * @return string|null
     */
    public function getOrderCurrencyCode();
    /**
     * Set Order Currency Code
     *
     * @param string|null $orderCurrencyCode
     * @return OrderInterface
     */
    public function setOrderCurrencyCode($orderCurrencyCode);

    /**
     * Get Original Increment Id
     *
     * @return string|null
     */
    public function getOriginalIncrementId();
    /**
     * Set Original Increment Id
     *
     * @param string|null $originalIncrementId
     * @return OrderInterface
     */
    public function setOriginalIncrementId($originalIncrementId);

    /**
     * Get Relation Child Id
     *
     * @return string|null
     */
    public function getRelationChildId();
    /**
     * Set Relation Child Id
     *
     * @param string|null $relationChildId
     * @return OrderInterface
     */
    public function setRelationChildId($relationChildId);

    /**
     * Get Relation Child Real Id
     *
     * @return string|null
     */
    public function getRelationChildRealId();
    /**
     * Set Relation Child Real Id
     *
     * @param string|null $relationChildRealId
     * @return OrderInterface
     */
    public function setRelationChildRealId($relationChildRealId);

    /**
     * Get Relation Parent Id
     *
     * @return string|null
     */
    public function getRelationParentId();
    /**
     * Set Relation Parent Id
     *
     * @param string|null $relationParentId
     * @return OrderInterface
     */
    public function setRelationParentId($relationParentId);

    /**
     * Get Relation Parent Real Id
     *
     * @return string|null
     */
    public function getRelationParentRealId();
    /**
     * Set Relation Parent Real Id
     *
     * @param string|null $relationParentRealId
     * @return OrderInterface
     */
    public function setRelationParentRealId($relationParentRealId);

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
     * @return OrderInterface
     */
    public function setRemoteIp($remoteIp);

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
     * @return OrderInterface
     */
    public function setShippingMethod($shippingMethod);

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
     * @return OrderInterface
     */
    public function setStoreCurrencyCode($storeCurrencyCode);

    /**
     * Get Store Name
     *
     * @return string|null
     */
    public function getStoreName();
    /**
     * Set Store Name
     *
     * @param string|null $storeName
     * @return OrderInterface
     */
    public function setStoreName($storeName);

    /**
     * Get X Forwarded For
     *
     * @return string|null
     */
    public function getXForwardedFor();
    /**
     * Set X Forwarded For
     *
     * @param string|null $xForwardedFor
     * @return OrderInterface
     */
    public function setXForwardedFor($xForwardedFor);

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
     * @return OrderInterface
     */
    public function setCustomerNote($customerNote);

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
     * @return OrderInterface
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
     * @return OrderInterface
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get Total Item Count
     *
     * @return int|null
     */
    public function getTotalItemCount();
    /**
     * Set Total Item Count
     *
     * @param int|null $totalItemCount
     * @return OrderInterface
     */
    public function setTotalItemCount($totalItemCount);

    /**
     * Get Customer Gender
     *
     * @return int|null
     */
    public function getCustomerGender();
    /**
     * Set Customer Gender
     *
     * @param int|null $customerGender
     * @return OrderInterface
     */
    public function setCustomerGender($customerGender);

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
     * @return OrderInterface
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
     * @return OrderInterface
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
     * @return OrderInterface
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
     * @return OrderInterface
     */
    public function setBaseShippingDiscountTaxCompensationAmnt($baseShippingDiscountTaxCompensationAmnt);

    /**
     * Get Discount Tax Compensation Invoiced
     *
     * @return float|null
     */
    public function getDiscountTaxCompensationInvoiced();
    /**
     * Set Discount Tax Compensation Invoiced
     *
     * @param float|null $discountTaxCompensationInvoiced
     * @return OrderInterface
     */
    public function setDiscountTaxCompensationInvoiced($discountTaxCompensationInvoiced);

    /**
     * Get Base Discount Tax Compensation Invoiced
     *
     * @return float|null
     */
    public function getBaseDiscountTaxCompensationInvoiced();
    /**
     * Set Base Discount Tax Compensation Invoiced
     *
     * @param float|null $baseDiscountTaxCompensationInvoiced
     * @return OrderInterface
     */
    public function setBaseDiscountTaxCompensationInvoiced($baseDiscountTaxCompensationInvoiced);

    /**
     * Get Discount Tax Compensation Refunded
     *
     * @return float|null
     */
    public function getDiscountTaxCompensationRefunded();
    /**
     * Set Discount Tax Compensation Refunded
     *
     * @param float|null $discountTaxCompensationRefunded
     * @return OrderInterface
     */
    public function setDiscountTaxCompensationRefunded($discountTaxCompensationRefunded);

    /**
     * Get Base Discount Tax Compensation Refunded
     *
     * @return float|null
     */
    public function getBaseDiscountTaxCompensationRefunded();
    /**
     * Set Base Discount Tax Compensation Refunded
     *
     * @param float|null $baseDiscountTaxCompensationRefunded
     * @return OrderInterface
     */
    public function setBaseDiscountTaxCompensationRefunded($baseDiscountTaxCompensationRefunded);

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
     * @return OrderInterface
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
     * @return OrderInterface
     */
    public function setBaseShippingInclTax($baseShippingInclTax);

    /**
     * Get Coupon Rule Name
     *
     * @return string|null
     */
    public function getCouponRuleName();
    /**
     * Set Coupon Rule Name
     *
     * @param string|null $couponRuleName
     * @return OrderInterface
     */
    public function setCouponRuleName($couponRuleName);

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
     * @return OrderInterface
     */
    public function setGiftMessageId($giftMessageId);
    /**
     * Get pos id
     *
     * @return int|null
     */
    public function getPosId();
    /**
     * Set pos id
     *
     * @param int $posId
     * @return OrderInterface
     */
    public function setPosId($posId);

    /**
     * Get pos location id
     *
     * @return int|null
     */
    public function getPosLocationId();
    /**
     * Set pos location id
     *
     * @param int $posLocationId
     * @return OrderInterface
     */
    public function setPosLocationId($posLocationId);

    /**
     * Get Items
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface[]|null
     */
    public function getItems();
    /**
     * Set Items
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface[] $items
     * @return OrderInterface
     */
    public function setItems($items);

    /**
     * Get Items
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface[]|null
     */
    public function getAllItems();

    /**
     * Get Addresses
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\AddressInterface[]|null
     */
    public function getAddresses();
    /**
     * Set Addresses
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\Order\AddressInterface[] $addresses
     * @return OrderInterface
     */
    public function setAddresses($addresses);

    /**
     * Get Payment
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\PaymentInterface[]|null
     */
    public function getPayments();
    /**
     * Set Payment
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\Order\PaymentInterface[]|null $payment
     * @return OrderInterface
     */
    public function setPayments($payment);

    /**
     * Get pos delivery date
     *
     * @return string|null
     */
    public function getPosDeliveryDate();
    /**
     * Set pos delivery date
     *
     * @param string $posDeliveryDate
     * @return OrderInterface
     */
    public function setPosDeliveryDate($posDeliveryDate);

    /**
     * Get pos staff id
     *
     * @return int|null
     */
    public function getPosStaffId();
    /**
     * Set pos staff id
     *
     * @param int $posStaffId
     * @return OrderInterface
     */
    public function setPosStaffId($posStaffId);

    /**
     * Get pos staff name
     *
     * @return string|null
     */
    public function getPosStaffName();
    /**
     * Set pos staff name
     *
     * @param string $posStaffName
     * @return OrderInterface
     */
    public function setPosStaffName($posStaffName);

    /**
     * Get pos fulfill online
     *
     * @return int|null
     */
    public function getPosFulfillOnline();
    /**
     * Set pos pos full fill online
     *
     * @param int|null $posFulfillOnline
     * @return OrderInterface
     */
    public function setPosFulfillOnline($posFulfillOnline);

    /**
     * Get search string
     *
     * @return string|null
     */
    public function getSearchString();
    /**
     * Set search string
     *
     * @param string $searchString
     * @return OrderInterface
     */
    public function setSearchString($searchString);

    /**
     * Get status histories
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\CommentInterface[]|null
     */
    public function getStatusHistories();
    /**
     * Set status histories
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\Order\CommentInterface[]|null $statusHistories
     * @return OrderInterface
     */
    public function setStatusHistories($statusHistories);

    /**
     * Get Pos Change
     *
     * @return float|null
     */
    public function getPosChange();
    /**
     * Set Pos Change
     *
     * @param float|null $posChange
     * @return OrderInterface
     */
    public function setPosChange($posChange);

    /**
     * Get Base Pos Change
     *
     * @return float|null
     */
    public function getBasePosChange();
    /**
     * Set Base Pos Change
     *
     * @param float|null $basePosChange
     * @return OrderInterface
     */
    public function setBasePosChange($basePosChange);

    /**
     * Get Paypal Ipn Customer Notified
     *
     * @return int|null
     */
    public function getPaypalIpnCustomerNotified();
    /**
     * Set Paypal Ipn Customer Notified
     *
     * @param int|null $paypalIpnCustomerNotified
     * @return OrderInterface
     */
    public function setPaypalIpnCustomerNotified($paypalIpnCustomerNotified);

    /**
     * Get Gift Card Codes Discount
     *
     * @return string|null
     */
    public function getCodesDiscount();

    /**
     * Set Gift Card Codes Discount
     *
     * @param string|null $codesDiscount
     * @return OrderInterface
     */
    public function setCodesDiscount($codesDiscount);

    /**
     * Get Gift Card Codes Base Discount
     *
     * @return string|null
     */
    public function getCodesBaseDiscount();

    /**
     * Set Gift Card Codes Discount
     *
     * @param string|null $codesBaseDiscount
     * @return OrderInterface
     */
    public function setCodesBaseDiscount($codesBaseDiscount);

    /**
     * Get Gift Card Gift Codes Discount
     *
     * @return string|null
     */
    public function getGiftVoucherGiftCodesDiscount();

    /**
     * Set Gift Card Gift Codes Discount
     *
     * @param string|null $giftVoucherGiftCodesDiscount
     * @return OrderInterface
     */
    public function setGiftVoucherGiftCodesDiscount($giftVoucherGiftCodesDiscount);

    /**
     * Get Gift Card Gift Codes
     *
     * @return string|null
     */
    public function getGiftVoucherGiftCodes();

    /**
     * Set Gift Card Gift Codes
     *
     * @param string|null $giftVoucherGiftCodes
     * @return OrderInterface
     */
    public function setGiftVoucherGiftCodes($giftVoucherGiftCodes);

    /**
     * Get Gift Card Discount
     *
     * @return float|null
     */
    public function getGiftVoucherDiscount();

    /**
     * Set Gift Card Discount
     *
     * @param float|null $giftVoucherDiscount
     * @return OrderInterface
     */
    public function setGiftVoucherDiscount($giftVoucherDiscount);

    /**
     * Get Gift Card Base Discount
     *
     * @return float|null
     */
    public function getBaseGiftVoucherDiscount();

    /**
     * Set Gift Card Base Discount
     *
     * @param float|null $baseGiftVoucherDiscount
     * @return OrderInterface
     */
    public function setBaseGiftVoucherDiscount($baseGiftVoucherDiscount);

    /**
     * Get Gift Card Discount For Shipping
     *
     * @return float|null
     */
    public function getGiftvoucherDiscountForShipping();

    /**
     * Set Gift Card Discount For Shipping
     *
     * @param float|null $giftvoucherDiscountForShipping
     * @return OrderInterface
     */
    public function setGiftvoucherDiscountForShipping($giftvoucherDiscountForShipping);

    /**
     * Get Gift Card Base Discount For Shipping
     *
     * @return float|null
     */
    public function getBaseGiftvoucherDiscountForShipping();

    /**
     * Set Gift Card Base Discount For Shipping
     *
     * @param float|null $baseGiftvoucherDiscountForShipping
     * @return OrderInterface
     */
    public function setBaseGiftvoucherDiscountForShipping($baseGiftvoucherDiscountForShipping);

    /**
     * Get Pos Pre Total Paid
     *
     * @return float|null
     */
    public function getPosPreTotalPaid();
    /**
     * Set Pos Pre Total Paid
     *
     * @param float|null $posPreTotalPaid
     * @return OrderInterface
     */
    public function setPosPreTotalPaid($posPreTotalPaid);

    /**
     * Get Pos Base Pre Total Paid
     *
     * @return float|null
     */
    public function getPosBasePreTotalPaid();
    /**
     * Set Pos Base Pre Total Paid
     *
     * @param float|null $posBasePreTotalPaid
     * @return OrderInterface
     */
    public function setPosBasePreTotalPaid($posBasePreTotalPaid);

    /**
     * Get Gift Card Gift Codes Available Refund
     *
     * @return string|null
     */
    public function getGiftVoucherGiftCodesAvailableRefund();

    /**
     * Get Magestore Base Discount For Shipping
     *
     * @return float|null
     */
    public function getMagestoreBaseDiscountForShipping();
    /**
     * Set Magestore Base Discount For Shipping
     *
     * @param float|null $magestoreBaseDiscountForShipping
     * @return OrderInterface
     */
    public function setMagestoreBaseDiscountForShipping($magestoreBaseDiscountForShipping);

    /**
     * Get Magestore Discount For Shipping
     *
     * @return float|null
     */
    public function getMagestoreDiscountForShipping();
    /**
     * Set Magestore Discount For Shipping
     *
     * @param float|null $magestoreDiscountForShipping
     * @return OrderInterface
     */
    public function setMagestoreDiscountForShipping($magestoreDiscountForShipping);

    /**
     * Get Magestore Base Discount
     *
     * @return float|null
     */
    public function getMagestoreBaseDiscount();
    /**
     * Set Magestore Base Discount
     *
     * @param float|null $magestoreBaseDiscount
     * @return OrderInterface
     */
    public function setMagestoreBaseDiscount($magestoreBaseDiscount);

    /**
     * Get Magestore Discount
     *
     * @return float|null
     */
    public function getMagestoreDiscount();
    /**
     * Set Magestore Discount
     *
     * @param float|null $magestoreDiscount
     * @return OrderInterface
     */
    public function setMagestoreDiscount($magestoreDiscount);

    /**
     * Get warehouse id
     *
     * @return int|null
     */
    public function getWarehouseId();
    /**
     * Set warehouse id
     *
     * @param int $warehouseId
     * @return OrderInterface
     */
    public function setWarehouseId($warehouseId);

    /**
     * Get Giftcodes applied discount for shipping
     *
     * @return string|null
     */
    public function getGiftcodesAppliedDiscountForShipping();

    /**
     * Set Giftcodes applied discount for shipping
     *
     * @param int $giftcodesAppliedDiscountForShipping
     * @return OrderInterface
     */
    public function setGiftcodesAppliedDiscountForShipping($giftcodesAppliedDiscountForShipping);

    /**
     * Get reason for custom discount
     *
     * @return string
     */
    public function getOsPosCustomDiscountReason();

    /**
     * Set os pos custom discount reason
     *
     * @param string $osPosCustomDiscountReason
     * @return OrderInterface
     */
    public function setOsPosCustomDiscountReason($osPosCustomDiscountReason);

    /**
     * Get type for custom discount type
     *
     * @return string
     */
    public function getOsPosCustomDiscountType();

    /**
     * Set os pos custom discount type
     *
     * @param string $osPosCustomDiscountType
     * @return OrderInterface
     */
    public function setOsPosCustomDiscountType($osPosCustomDiscountType);

    /**
     * Get amount for custom discount
     *
     * @return string
     */
    public function getOsPosCustomDiscountAmount();

    /**
     * Set os pos custom discount amount
     *
     * @param string $osPosCustomDiscountAmount
     * @return OrderInterface
     */
    public function setOsPosCustomDiscountAmount($osPosCustomDiscountAmount);
    /**
     * Get applied taxes
     *
     * @return \Magestore\Webpos\Api\Data\Tax\TaxDetailInterface[]
     */
    public function getAppliedTaxes();

    /**
     * Set applied taxes
     *
     * @param \Magestore\Webpos\Api\Data\Tax\TaxDetailInterface[] $appliedTaxes
     * @return OrderInterface
     */
    public function setAppliedTaxes($appliedTaxes);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\OrderExtensionInterface|null
     * @since 102.0.0
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\OrderExtensionInterface $extensionAttributes
     * @return $this
     * @since 102.0.0
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Checkout\OrderExtensionInterface $extensionAttributes
    );

    /**
     * Get temp customer id
     *
     * @return string|null
     */
    public function getTmpCustomerId();

    /**
     * Set temp customer id
     *
     * @param string|null $tmpCustomerId
     * @return $this
     */
    public function setTmpCustomerId($tmpCustomerId);
}
