<?php

namespace Magestore\Webpos\Api\Data\Checkout\Order;

interface ItemInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ITEM_ID = 'item_id';
    const ORDER_ID = 'order_id';
    const PARENT_ITEM_ID = 'parent_item_id';
    const QUOTE_ITEM_ID = 'quote_item_id';
    const STORE_ID = 'store_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const PRODUCT_ID = 'product_id';
    const PRODUCT_TYPE = 'product_type';
    const PRODUCT_OPTIONS = 'product_options';
    const WEIGHT = 'weight';
    const IS_VIRTUAL = 'is_virtual';
    const SKU = 'sku';
    const NAME = 'name';
    const DESCRIPTION = 'description';
    const APPLIED_RULE_IDS = 'applied_rule_ids';
    const ADDITIONAL_DATA = 'additional_data';
    const IS_QTY_DECIMAL = 'is_qty_decimal';
    const NO_DISCOUNT = 'no_discount';
    const QTY_BACKORDERED = 'qty_backordered';
    const QTY_CANCELED = 'qty_canceled';
    const QTY_INVOICED = 'qty_invoiced';
    const QTY_ORDERED = 'qty_ordered';
    const QTY_REFUNDED = 'qty_refunded';
    const QTY_SHIPPED = 'qty_shipped';
    const BASE_COST = 'base_cost';
    const PRICE = 'price';
    const BASE_PRICE = 'base_price';
    const ORIGINAL_PRICE = 'original_price';
    const BASE_ORIGINAL_PRICE = 'base_original_price';
    const POS_BASE_ORIGINAL_PRICE_EXCL_TAX = 'pos_base_original_price_excl_tax';
    const POS_ORIGINAL_PRICE_EXCL_TAX = 'pos_original_price_excl_tax';
    const POS_BASE_ORIGINAL_PRICE_INCL_TAX = 'pos_base_original_price_incl_tax';
    const POS_ORIGINAL_PRICE_INCL_TAX = 'pos_original_price_incl_tax';
    const TAX_PERCENT = 'tax_percent';
    const TAX_AMOUNT = 'tax_amount';
    const BASE_TAX_AMOUNT = 'base_tax_amount';
    const TAX_INVOICED = 'tax_invoiced';
    const BASE_TAX_INVOICED = 'base_tax_invoiced';
    const DISCOUNT_PERCENT = 'discount_percent';
    const DISCOUNT_AMOUNT = 'discount_amount';
    const BASE_DISCOUNT_AMOUNT = 'base_discount_amount';
    const DISCOUNT_INVOICED = 'discount_invoiced';
    const BASE_DISCOUNT_INVOICED = 'base_discount_invoiced';
    const AMOUNT_REFUNDED = 'amount_refunded';
    const BASE_AMOUNT_REFUNDED = 'base_amount_refunded';
    const ROW_TOTAL = 'row_total';
    const BASE_ROW_TOTAL = 'base_row_total';
    const ROW_INVOICED = 'row_invoiced';
    const BASE_ROW_INVOICED = 'base_row_invoiced';
    const ROW_WEIGHT = 'row_weight';
    const BASE_TAX_BEFORE_DISCOUNT = 'base_tax_before_discount';
    const TAX_BEFORE_DISCOUNT = 'tax_before_discount';
    const EXT_ORDER_ITEM_ID = 'ext_order_item_id';
    const LOCKED_DO_INVOICE = 'locked_do_invoice';
    const LOCKED_DO_SHIP = 'locked_do_ship';
    const PRICE_INCL_TAX = 'price_incl_tax';
    const BASE_PRICE_INCL_TAX = 'base_price_incl_tax';
    const ROW_TOTAL_INCL_TAX = 'row_total_incl_tax';
    const BASE_ROW_TOTAL_INCL_TAX = 'base_row_total_incl_tax';
    const DISCOUNT_TAX_COMPENSATION_AMOUNT = 'discount_tax_compensation_amount';
    const BASE_DISCOUNT_TAX_COMPENSATION_AMOUNT = 'base_discount_tax_compensation_amount';
    const DISCOUNT_TAX_COMPENSATION_INVOICED = 'discount_tax_compensation_invoiced';
    const BASE_DISCOUNT_TAX_COMPENSATION_INVOICED = 'base_discount_tax_compensation_invoiced';
    const DISCOUNT_TAX_COMPENSATION_REFUNDED = 'discount_tax_compensation_refunded';
    const BASE_DISCOUNT_TAX_COMPENSATION_REFUNDED = 'base_discount_tax_compensation_refunded';
    const TAX_CANCELED = 'tax_canceled';
    const DISCOUNT_TAX_COMPENSATION_CANCELED = 'discount_tax_compensation_canceled';
    const TAX_REFUNDED = 'tax_refunded';
    const BASE_TAX_REFUNDED = 'base_tax_refunded';
    const DISCOUNT_REFUNDED = 'discount_refunded';
    const BASE_DISCOUNT_REFUNDED = 'base_discount_refunded';
    const GIFT_MESSAGE_ID = 'gift_message_id';
    const GIFT_MESSAGE_AVAILABLE = 'gift_message_available';
    const FREE_SHIPPING = 'free_shipping';
    const WEEE_TAX_APPLIED = 'weee_tax_applied';
    const WEEE_TAX_APPLIED_AMOUNT = 'weee_tax_applied_amount';
    const WEEE_TAX_APPLIED_ROW_AMOUNT = 'weee_tax_applied_row_amount';
    const WEEE_TAX_DISPOSITION = 'weee_tax_disposition';
    const WEEE_TAX_ROW_DISPOSITION = 'weee_tax_row_disposition';
    const BASE_WEEE_TAX_APPLIED_AMOUNT = 'base_weee_tax_applied_amount';
    const BASE_WEEE_TAX_APPLIED_ROW_AMNT = 'base_weee_tax_applied_row_amnt';
    const BASE_WEEE_TAX_DISPOSITION = 'base_weee_tax_disposition';
    const BASE_WEEE_TAX_ROW_DISPOSITION = 'base_weee_tax_row_disposition';
    const SEARCH_STRING = 'search_string';
    const TMP_ITEM_ID = 'tmp_item_id';
    const MAGESTORE_BASE_DISCOUNT = 'magestore_base_discount';
    const MAGESTORE_DISCOUNT = 'magestore_discount';
    const GIFT_VOUCHER_DISCOUNT = 'gift_voucher_discount';
    const BASE_GIFT_VOUCHER_DISCOUNT = 'base_gift_voucher_discount';
    const OS_POS_CUSTOM_PRICE_REASON = 'os_pos_custom_price_reason';
    const GIFT_CARD_QTY_USED = 'gift_card_qty_used';
    const GIFTCODES_APPLIED = 'giftcodes_applied';
    const POS_CUSTOM_PRICE = 'pos_custom_price';
    const APPLIED_TAXES = '_applied_taxes';

    /**
     * Get Item Id
     *
     * @return int|null
     */
    public function getItemId();

    /**
     * Set Item Id
     *
     * @param int|null $itemId
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setItemId($itemId);

    /**
     * Get Order Id
     *
     * @return int|null
     */
    public function getOrderId();

    /**
     * Set Order Id
     *
     * @param int|null $orderId
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setOrderId($orderId);

    /**
     * Get Parent Item Id
     *
     * @return int|null
     */
    public function getParentItemId();

    /**
     * Set Parent Item Id
     *
     * @param int|null $parentItemId
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setParentItemId($parentItemId);

    /**
     * Get Quote Item Id
     *
     * @return int|null
     */
    public function getQuoteItemId();

    /**
     * Set Quote Item Id
     *
     * @param int|null $quoteItemId
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setQuoteItemId($quoteItemId);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get Product Id
     *
     * @return int|null
     */
    public function getProductId();

    /**
     * Set Product Id
     *
     * @param int|null $productId
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setProductId($productId);

    /**
     * Get Product Type
     *
     * @return string|null
     */
    public function getProductType();

    /**
     * Set Product Type
     *
     * @param string|null $productType
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setProductType($productType);

    /**
     * Get Product Options
     *
     * @return string|null
     */
    public function getProductOptions();

    /**
     * Set Product Options
     *
     * @param string|null $productOptions
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setProductOptions($productOptions);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setWeight($weight);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setIsVirtual($isVirtual);

    /**
     * Get Sku
     *
     * @return string|null
     */
    public function getSku();

    /**
     * Set Sku
     *
     * @param string|null $sku
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setSku($sku);

    /**
     * Get Name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Set Name
     *
     * @param string|null $name
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setName($name);

    /**
     * Get Description
     *
     * @return string|null
     */
    public function getDescription();

    /**
     * Set Description
     *
     * @param string|null $description
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setDescription($description);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setAppliedRuleIds($appliedRuleIds);

    /**
     * Get Additional Data
     *
     * @return string|null
     */
    public function getAdditionalData();

    /**
     * Set Additional Data
     *
     * @param string|null $additionalData
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setAdditionalData($additionalData);

    /**
     * Get Is Qty Decimal
     *
     * @return int|null
     */
    public function getIsQtyDecimal();

    /**
     * Set Is Qty Decimal
     *
     * @param int|null $isQtyDecimal
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setIsQtyDecimal($isQtyDecimal);

    /**
     * Get No Discount
     *
     * @return int|null
     */
    public function getNoDiscount();

    /**
     * Set No Discount
     *
     * @param int|null $noDiscount
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setNoDiscount($noDiscount);

    /**
     * Get Qty Backordered
     *
     * @return float|null
     */
    public function getQtyBackordered();

    /**
     * Set Qty Backordered
     *
     * @param float|null $qtyBackordered
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setQtyBackordered($qtyBackordered);

    /**
     * Get Qty Canceled
     *
     * @return float|null
     */
    public function getQtyCanceled();

    /**
     * Set Qty Canceled
     *
     * @param float|null $qtyCanceled
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setQtyCanceled($qtyCanceled);

    /**
     * Get Qty Invoiced
     *
     * @return float|null
     */
    public function getQtyInvoiced();

    /**
     * Set Qty Invoiced
     *
     * @param float|null $qtyInvoiced
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setQtyInvoiced($qtyInvoiced);

    /**
     * Get Qty Ordered
     *
     * @return float|null
     */
    public function getQtyOrdered();

    /**
     * Set Qty Ordered
     *
     * @param float|null $qtyOrdered
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setQtyOrdered($qtyOrdered);

    /**
     * Get Qty Refunded
     *
     * @return float|null
     */
    public function getQtyRefunded();

    /**
     * Set Qty Refunded
     *
     * @param float|null $qtyRefunded
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setQtyRefunded($qtyRefunded);

    /**
     * Get Qty Shipped
     *
     * @return float|null
     */
    public function getQtyShipped();

    /**
     * Set Qty Shipped
     *
     * @param float|null $qtyShipped
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setQtyShipped($qtyShipped);

    /**
     * Get Base Cost
     *
     * @return float|null
     */
    public function getBaseCost();

    /**
     * Set Base Cost
     *
     * @param float|null $baseCost
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setBaseCost($baseCost);

    /**
     * Get Price
     *
     * @return float|null
     */
    public function getPrice();

    /**
     * Set Price
     *
     * @param float|null $price
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setPrice($price);

    /**
     * Get Base Price
     *
     * @return float|null
     */
    public function getBasePrice();

    /**
     * Set Base Price
     *
     * @param float|null $basePrice
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setBasePrice($basePrice);

    /**
     * Get Original Price
     *
     * @return float|null
     */
    public function getOriginalPrice();

    /**
     * Set Original Price
     *
     * @param float|null $originalPrice
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setOriginalPrice($originalPrice);

    /**
     * Get Base Original Price
     *
     * @return float|null
     */
    public function getBaseOriginalPrice();

    /**
     * Set Base Original Price
     *
     * @param float|null $baseOriginalPrice
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setBaseOriginalPrice($baseOriginalPrice);

    /**
     * Get Original Price Excl Tax
     *
     * @return float|null
     */
    public function getPosOriginalPriceExclTax();

    /**
     * Set Original Price Excl Tax
     *
     * @param float|null $originalPriceExclTax
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setPosOriginalPriceExclTax($originalPriceExclTax);

    /**
     * Get Base Original Price Excl Tax
     *
     * @return float|null
     */
    public function getPosBaseOriginalPriceExclTax();

    /**
     * Set Base Original Price Excl Tax
     *
     * @param float|null $posBaseOriginalPriceExclTax
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setPosBaseOriginalPriceExclTax($posBaseOriginalPriceExclTax);

    /**
     * Get Original Price Incl Tax
     *
     * @return float|null
     */
    public function getPosOriginalPriceInclTax();

    /**
     * Set Original Price Incl Tax
     *
     * @param float|null $originalPriceInclTax
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setPosOriginalPriceInclTax($originalPriceInclTax);

    /**
     * Get Base Original Price Incl Tax
     *
     * @return float|null
     */
    public function getPosBaseOriginalPriceInclTax();

    /**
     * Set Base Original Price Incl Tax
     *
     * @param float|null $posBaseOriginalPriceInclTax
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setPosBaseOriginalPriceInclTax($posBaseOriginalPriceInclTax);

    /**
     * Get Tax Percent
     *
     * @return float|null
     */
    public function getTaxPercent();

    /**
     * Set Tax Percent
     *
     * @param float|null $taxPercent
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setTaxPercent($taxPercent);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setBaseTaxAmount($baseTaxAmount);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setTaxInvoiced($taxInvoiced);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setBaseTaxInvoiced($baseTaxInvoiced);

    /**
     * Get Discount Percent
     *
     * @return float|null
     */
    public function getDiscountPercent();

    /**
     * Set Discount Percent
     *
     * @param float|null $discountPercent
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setDiscountPercent($discountPercent);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setBaseDiscountAmount($baseDiscountAmount);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setDiscountInvoiced($discountInvoiced);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setBaseDiscountInvoiced($baseDiscountInvoiced);

    /**
     * Get Amount Refunded
     *
     * @return float|null
     */
    public function getAmountRefunded();

    /**
     * Set Amount Refunded
     *
     * @param float|null $amountRefunded
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setAmountRefunded($amountRefunded);

    /**
     * Get Base Amount Refunded
     *
     * @return float|null
     */
    public function getBaseAmountRefunded();

    /**
     * Set Base Amount Refunded
     *
     * @param float|null $baseAmountRefunded
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setBaseAmountRefunded($baseAmountRefunded);

    /**
     * Get Row Total
     *
     * @return float|null
     */
    public function getRowTotal();

    /**
     * Set Row Total
     *
     * @param float|null $rowTotal
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setRowTotal($rowTotal);

    /**
     * Get Base Row Total
     *
     * @return float|null
     */
    public function getBaseRowTotal();

    /**
     * Set Base Row Total
     *
     * @param float|null $baseRowTotal
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setBaseRowTotal($baseRowTotal);

    /**
     * Get Row Invoiced
     *
     * @return float|null
     */
    public function getRowInvoiced();

    /**
     * Set Row Invoiced
     *
     * @param float|null $rowInvoiced
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setRowInvoiced($rowInvoiced);

    /**
     * Get Base Row Invoiced
     *
     * @return float|null
     */
    public function getBaseRowInvoiced();

    /**
     * Set Base Row Invoiced
     *
     * @param float|null $baseRowInvoiced
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setBaseRowInvoiced($baseRowInvoiced);

    /**
     * Get Row Weight
     *
     * @return float|null
     */
    public function getRowWeight();

    /**
     * Set Row Weight
     *
     * @param float|null $rowWeight
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setRowWeight($rowWeight);

    /**
     * Get Base Tax Before Discount
     *
     * @return float|null
     */
    public function getBaseTaxBeforeDiscount();

    /**
     * Set Base Tax Before Discount
     *
     * @param float|null $baseTaxBeforeDiscount
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setBaseTaxBeforeDiscount($baseTaxBeforeDiscount);

    /**
     * Get Tax Before Discount
     *
     * @return float|null
     */
    public function getTaxBeforeDiscount();

    /**
     * Set Tax Before Discount
     *
     * @param float|null $taxBeforeDiscount
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setTaxBeforeDiscount($taxBeforeDiscount);

    /**
     * Get Ext Order Item Id
     *
     * @return string|null
     */
    public function getExtOrderItemId();

    /**
     * Set Ext Order Item Id
     *
     * @param string|null $extOrderItemId
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setExtOrderItemId($extOrderItemId);

    /**
     * Get Locked Do Invoice
     *
     * @return int|null
     */
    public function getLockedDoInvoice();

    /**
     * Set Locked Do Invoice
     *
     * @param int|null $lockedDoInvoice
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setLockedDoInvoice($lockedDoInvoice);

    /**
     * Get Locked Do Ship
     *
     * @return int|null
     */
    public function getLockedDoShip();

    /**
     * Set Locked Do Ship
     *
     * @param int|null $lockedDoShip
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setLockedDoShip($lockedDoShip);

    /**
     * Get Price Incl Tax
     *
     * @return float|null
     */
    public function getPriceInclTax();

    /**
     * Set Price Incl Tax
     *
     * @param float|null $priceInclTax
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setPriceInclTax($priceInclTax);

    /**
     * Get Base Price Incl Tax
     *
     * @return float|null
     */
    public function getBasePriceInclTax();

    /**
     * Set Base Price Incl Tax
     *
     * @param float|null $basePriceInclTax
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setBasePriceInclTax($basePriceInclTax);

    /**
     * Get Row Total Incl Tax
     *
     * @return float|null
     */
    public function getRowTotalInclTax();

    /**
     * Set Row Total Incl Tax
     *
     * @param float|null $rowTotalInclTax
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setRowTotalInclTax($rowTotalInclTax);

    /**
     * Get Base Row Total Incl Tax
     *
     * @return float|null
     */
    public function getBaseRowTotalInclTax();

    /**
     * Set Base Row Total Incl Tax
     *
     * @param float|null $baseRowTotalInclTax
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setBaseRowTotalInclTax($baseRowTotalInclTax);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setBaseDiscountTaxCompensationAmount($baseDiscountTaxCompensationAmount);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setBaseDiscountTaxCompensationRefunded($baseDiscountTaxCompensationRefunded);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setTaxCanceled($taxCanceled);

    /**
     * Get Discount Tax Compensation Canceled
     *
     * @return float|null
     */
    public function getDiscountTaxCompensationCanceled();

    /**
     * Set Discount Tax Compensation Canceled
     *
     * @param float|null $discountTaxCompensationCanceled
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setDiscountTaxCompensationCanceled($discountTaxCompensationCanceled);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setTaxRefunded($taxRefunded);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setBaseTaxRefunded($baseTaxRefunded);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setDiscountRefunded($discountRefunded);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setBaseDiscountRefunded($baseDiscountRefunded);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setGiftMessageId($giftMessageId);

    /**
     * Get Gift Message Available
     *
     * @return int|null
     */
    public function getGiftMessageAvailable();

    /**
     * Set Gift Message Available
     *
     * @param int|null $giftMessageAvailable
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setGiftMessageAvailable($giftMessageAvailable);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setFreeShipping($freeShipping);

    /**
     * Get Weee Tax Applied
     *
     * @return string
     */
    public function getWeeeTaxApplied();

    /**
     * Set Weee Tax Applied
     *
     * @param string $weeeTaxApplied
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setWeeeTaxApplied($weeeTaxApplied);

    /**
     * Get Weee Tax Applied Amount
     *
     * @return float|null
     */
    public function getWeeeTaxAppliedAmount();

    /**
     * Set Weee Tax Applied Amount
     *
     * @param float|null $weeeTaxAppliedAmount
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setWeeeTaxAppliedAmount($weeeTaxAppliedAmount);

    /**
     * Get Weee Tax Applied Row Amount
     *
     * @return float|null
     */
    public function getWeeeTaxAppliedRowAmount();

    /**
     * Set Weee Tax Applied Row Amount
     *
     * @param float|null $weeeTaxAppliedRowAmount
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setWeeeTaxAppliedRowAmount($weeeTaxAppliedRowAmount);

    /**
     * Get Weee Tax Disposition
     *
     * @return float|null
     */
    public function getWeeeTaxDisposition();

    /**
     * Set Weee Tax Disposition
     *
     * @param float|null $weeeTaxDisposition
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setWeeeTaxDisposition($weeeTaxDisposition);

    /**
     * Get Weee Tax Row Disposition
     *
     * @return float|null
     */
    public function getWeeeTaxRowDisposition();

    /**
     * Set Weee Tax Row Disposition
     *
     * @param float|null $weeeTaxRowDisposition
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setWeeeTaxRowDisposition($weeeTaxRowDisposition);

    /**
     * Get Base Weee Tax Applied Amount
     *
     * @return float|null
     */
    public function getBaseWeeeTaxAppliedAmount();

    /**
     * Set Base Weee Tax Applied Amount
     *
     * @param float|null $baseWeeeTaxAppliedAmount
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setBaseWeeeTaxAppliedAmount($baseWeeeTaxAppliedAmount);

    /**
     * Get Base Weee Tax Applied Row Amnt
     *
     * @return float|null
     */
    public function getBaseWeeeTaxAppliedRowAmnt();

    /**
     * Set Base Weee Tax Applied Row Amnt
     *
     * @param float|null $baseWeeeTaxAppliedRowAmnt
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setBaseWeeeTaxAppliedRowAmnt($baseWeeeTaxAppliedRowAmnt);

    /**
     * Get Base Weee Tax Disposition
     *
     * @return float|null
     */
    public function getBaseWeeeTaxDisposition();

    /**
     * Set Base Weee Tax Disposition
     *
     * @param float|null $baseWeeeTaxDisposition
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setBaseWeeeTaxDisposition($baseWeeeTaxDisposition);

    /**
     * Get Base Weee Tax Row Disposition
     *
     * @return float|null
     */
    public function getBaseWeeeTaxRowDisposition();

    /**
     * Set Base Weee Tax Row Disposition
     *
     * @param float|null $baseWeeeTaxRowDisposition
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setBaseWeeeTaxRowDisposition($baseWeeeTaxRowDisposition);

    /**
     * Get search string
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface|null
     */
    public function getSearchString();

    /**
     * Get temp item id
     * @return float|null
     */
    public function getTmpItemId();

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
     * @return ItemInterface
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
     * @return ItemInterface
     */
    public function setBaseGiftVoucherDiscount($baseGiftVoucherDiscount);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setMagestoreDiscount($magestoreDiscount);

    /**
     * get reason for custom price
     * @return string
     */
    public function getOsPosCustomPriceReason();

    /**
     * @param $osPosCustomPriceReason
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setOsPosCustomPriceReason($osPosCustomPriceReason);

    /**
     * get gift card qty used
     * @return float|null
     */
    public function getGiftCardQtyUsed();

    /**
     * set gift card qty used
     * @param float|null $qty
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setGiftCardQtyUsed($qty);


    /**
     * get giftcodes_applied
     * @return string|null
     */
    public function getGiftcodesApplied();

    /**
     * set giftcodes_applied
     * @param string|null $giftcodesApplied
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setGiftcodesApplied($giftcodesApplied);

    /**
     * Get pos custom price
     * @return float|null
     */
    public function getPosCustomPrice();

    /**
     * Set pos custom price
     * @param float|null $posCustomPrice
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setPosCustomPrice($posCustomPrice);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemExtensionInterface|null
     * @since 102.0.0
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\Order\ItemExtensionInterface $extensionAttributes
     * @return $this
     * @since 102.0.0
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Checkout\Order\ItemExtensionInterface $extensionAttributes
    );

    /**
     * get AppliedTaxes
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\Item\TaxInterface[]|[]
     */
    public function getAppliedTaxes();

    /**
     * set AppliedTaxes
     * @param \Magestore\Webpos\Api\Data\Checkout\Order\Item\TaxInterface[]|[] $appliedTaxes
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setAppliedTaxes($appliedTaxes);
}
