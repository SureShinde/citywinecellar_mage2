<?php

namespace Magestore\Webpos\Api\Data\Checkout\Quote;

interface ItemInterface extends \Magento\Framework\Api\ExtensibleDataInterface {
    const ITEM_ID = 'item_id';
    const QUOTE_ID = 'quote_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const PRODUCT_ID = 'product_id';
    const STORE_ID = 'store_id';
    const PARENT_ITEM_ID = 'parent_item_id';
    const IS_VIRTUAL = 'is_virtual';
    const SKU = 'sku';
    const NAME = 'name';
    const DESCRIPTION = 'description';
    const APPLIED_RULE_IDS = 'applied_rule_ids';
    const ADDITIONAL_DATA = 'additional_data';
    const IS_QTY_DECIMAL = 'is_qty_decimal';
    const NO_DISCOUNT = 'no_discount';
    const WEIGHT = 'weight';
    const QTY = 'qty';
    const PRICE = 'price';
    const BASE_PRICE = 'base_price';
    const CUSTOM_PRICE = 'custom_price';
    const DISCOUNT_PERCENT = 'discount_percent';
    const DISCOUNT_AMOUNT = 'discount_amount';
    const BASE_DISCOUNT_AMOUNT = 'base_discount_amount';
    const TAX_PERCENT = 'tax_percent';
    const TAX_AMOUNT = 'tax_amount';
    const BASE_TAX_AMOUNT = 'base_tax_amount';
    const ROW_TOTAL = 'row_total';
    const BASE_ROW_TOTAL = 'base_row_total';
    const ROW_TOTAL_WITH_DISCOUNT = 'row_total_with_discount';
    const ROW_WEIGHT = 'row_weight';
    const PRODUCT_TYPE = 'product_type';
    const BASE_TAX_BEFORE_DISCOUNT = 'base_tax_before_discount';
    const TAX_BEFORE_DISCOUNT = 'tax_before_discount';
    const ORIGINAL_CUSTOM_PRICE = 'original_custom_price';
    const REDIRECT_URL = 'redirect_url';
    const BASE_COST = 'base_cost';
    const PRICE_INCL_TAX = 'price_incl_tax';
    const BASE_PRICE_INCL_TAX = 'base_price_incl_tax';
    const ROW_TOTAL_INCL_TAX = 'row_total_incl_tax';
    const BASE_ROW_TOTAL_INCL_TAX = 'base_row_total_incl_tax';
    const DISCOUNT_TAX_COMPENSATION_AMOUNT = 'discount_tax_compensation_amount';
    const BASE_DISCOUNT_TAX_COMPENSATION_AMOUNT = 'base_discount_tax_compensation_amount';
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
    const GIFT_MESSAGE_ID = 'gift_message_id';
    const TMP_ITEM_ID = 'tmp_item_id';


    /**
     * Get Item Id
     *
     * @return float|null
     */
    public function getItemId();	
    /**
     * Set Item Id
     *
     * @param float|null $itemId
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
     */
    public function setItemId($itemId);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
     */
    public function setProductId($productId);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
     */
    public function setStoreId($storeId);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
     */
    public function setParentItemId($parentItemId);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
     */
    public function setNoDiscount($noDiscount);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
     */
    public function setWeight($weight);

    /**
     * Get Qty
     *
     * @return float|null
     */
    public function getQty();	
    /**
     * Set Qty
     *
     * @param float|null $qty
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
     */
    public function setQty($qty);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
     */
    public function setBasePrice($basePrice);

    /**
     * Get Custom Price
     *
     * @return float|null
     */
    public function getCustomPrice();	
    /**
     * Set Custom Price
     *
     * @param float|null $customPrice
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
     */
    public function setCustomPrice($customPrice);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
     */
    public function setBaseDiscountAmount($baseDiscountAmount);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
     */
    public function setBaseTaxAmount($baseTaxAmount);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
     */
    public function setBaseRowTotal($baseRowTotal);

    /**
     * Get Row Total With Discount
     *
     * @return float|null
     */
    public function getRowTotalWithDiscount();	
    /**
     * Set Row Total With Discount
     *
     * @param float|null $rowTotalWithDiscount
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
     */
    public function setRowTotalWithDiscount($rowTotalWithDiscount);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
     */
    public function setRowWeight($rowWeight);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
     */
    public function setProductType($productType);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
     */
    public function setTaxBeforeDiscount($taxBeforeDiscount);

    /**
     * Get Original Custom Price
     *
     * @return float|null
     */
    public function getOriginalCustomPrice();	
    /**
     * Set Original Custom Price
     *
     * @param float|null $originalCustomPrice
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
     */
    public function setOriginalCustomPrice($originalCustomPrice);

    /**
     * Get Redirect Url
     *
     * @return string|null
     */
    public function getRedirectUrl();	
    /**
     * Set Redirect Url
     *
     * @param string|null $redirectUrl
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
     */
    public function setRedirectUrl($redirectUrl);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
     */
    public function setBaseCost($baseCost);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
     */
    public function setBaseDiscountTaxCompensationAmount($baseDiscountTaxCompensationAmount);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
     */
    public function setFreeShipping($freeShipping);

    /**
     * Get Weee Tax Applied
     *
     * @return string|null
     */
    public function getWeeeTaxApplied();	
    /**
     * Set Weee Tax Applied
     *
     * @param string|null $weeeTaxApplied
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
     */
    public function setBaseWeeeTaxRowDisposition($baseWeeeTaxRowDisposition);

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
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
     */
    public function setGiftMessageId($giftMessageId);

    /**
     * Get Tmp Item Id
     *
     * @return string|float|null
     */
    public function getTmpItemId();	
    /**
     * Set Tmp Item Id
     *
     * @param string|float|null $tmpItemId
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
     */
    public function setTmpItemId($tmpItemId);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\Quote\ItemExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\Quote\ItemExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Checkout\Quote\ItemExtensionInterface $extensionAttributes
    );
}