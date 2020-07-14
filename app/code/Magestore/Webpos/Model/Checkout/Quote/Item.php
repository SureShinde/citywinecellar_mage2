<?php

namespace Magestore\Webpos\Model\Checkout\Quote;

class Item extends \Magento\Framework\DataObject implements \Magestore\Webpos\Api\Data\Checkout\Quote\ItemInterface
{

    /**
     * @inheritdoc
     */
    public function getItemId() {
        return $this->getData(self::ITEM_ID);
    }	
    /**
     * @inheritdoc
     */
    public function setItemId($itemId) {
        return $this->setData(self::ITEM_ID, $itemId);
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
    public function getProductId() {
        return $this->getData(self::PRODUCT_ID);
    }	
    /**
     * @inheritdoc
     */
    public function setProductId($productId) {
        return $this->setData(self::PRODUCT_ID, $productId);
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
    public function getParentItemId() {
        return $this->getData(self::PARENT_ITEM_ID);
    }	
    /**
     * @inheritdoc
     */
    public function setParentItemId($parentItemId) {
        return $this->setData(self::PARENT_ITEM_ID, $parentItemId);
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
    public function getSku() {
        return $this->getData(self::SKU);
    }	
    /**
     * @inheritdoc
     */
    public function setSku($sku) {
        return $this->setData(self::SKU, $sku);
    }

    /**
     * @inheritdoc
     */
    public function getName() {
        return $this->getData(self::NAME);
    }	
    /**
     * @inheritdoc
     */
    public function setName($name) {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritdoc
     */
    public function getDescription() {
        return $this->getData(self::DESCRIPTION);
    }	
    /**
     * @inheritdoc
     */
    public function setDescription($description) {
        return $this->setData(self::DESCRIPTION, $description);
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
    public function getAdditionalData() {
        return $this->getData(self::ADDITIONAL_DATA);
    }	
    /**
     * @inheritdoc
     */
    public function setAdditionalData($additionalData) {
        return $this->setData(self::ADDITIONAL_DATA, $additionalData);
    }

    /**
     * @inheritdoc
     */
    public function getIsQtyDecimal() {
        return $this->getData(self::IS_QTY_DECIMAL);
    }	
    /**
     * @inheritdoc
     */
    public function setIsQtyDecimal($isQtyDecimal) {
        return $this->setData(self::IS_QTY_DECIMAL, $isQtyDecimal);
    }

    /**
     * @inheritdoc
     */
    public function getNoDiscount() {
        return $this->getData(self::NO_DISCOUNT);
    }	
    /**
     * @inheritdoc
     */
    public function setNoDiscount($noDiscount) {
        return $this->setData(self::NO_DISCOUNT, $noDiscount);
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
    public function getQty() {
        return $this->getData(self::QTY);
    }	
    /**
     * @inheritdoc
     */
    public function setQty($qty) {
        return $this->setData(self::QTY, round($qty, 4));
    }

    /**
     * @inheritdoc
     */
    public function getPrice() {
        return $this->getData(self::PRICE);
    }	
    /**
     * @inheritdoc
     */
    public function setPrice($price) {
        return $this->setData(self::PRICE, round($price, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBasePrice() {
        return $this->getData(self::BASE_PRICE);
    }	
    /**
     * @inheritdoc
     */
    public function setBasePrice($basePrice) {
        return $this->setData(self::BASE_PRICE, round($basePrice, 4));
    }

    /**
     * @inheritdoc
     */
    public function getCustomPrice() {
        return $this->getData(self::CUSTOM_PRICE);
    }	
    /**
     * @inheritdoc
     */
    public function setCustomPrice($customPrice) {
        return $this->setData(self::CUSTOM_PRICE, round($customPrice, 4));
    }

    /**
     * @inheritdoc
     */
    public function getDiscountPercent() {
        return $this->getData(self::DISCOUNT_PERCENT);
    }	
    /**
     * @inheritdoc
     */
    public function setDiscountPercent($discountPercent) {
        return $this->setData(self::DISCOUNT_PERCENT, round($discountPercent, 4));
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
    public function getTaxPercent() {
        return $this->getData(self::TAX_PERCENT);
    }	
    /**
     * @inheritdoc
     */
    public function setTaxPercent($taxPercent) {
        return $this->setData(self::TAX_PERCENT, round($taxPercent, 4));
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
    public function getRowTotal() {
        return $this->getData(self::ROW_TOTAL);
    }	
    /**
     * @inheritdoc
     */
    public function setRowTotal($rowTotal) {
        return $this->setData(self::ROW_TOTAL, round($rowTotal, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseRowTotal() {
        return $this->getData(self::BASE_ROW_TOTAL);
    }	
    /**
     * @inheritdoc
     */
    public function setBaseRowTotal($baseRowTotal) {
        return $this->setData(self::BASE_ROW_TOTAL, round($baseRowTotal, 4));
    }

    /**
     * @inheritdoc
     */
    public function getRowTotalWithDiscount() {
        return $this->getData(self::ROW_TOTAL_WITH_DISCOUNT);
    }	
    /**
     * @inheritdoc
     */
    public function setRowTotalWithDiscount($rowTotalWithDiscount) {
        return $this->setData(self::ROW_TOTAL_WITH_DISCOUNT, round($rowTotalWithDiscount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getRowWeight() {
        return $this->getData(self::ROW_WEIGHT);
    }	
    /**
     * @inheritdoc
     */
    public function setRowWeight($rowWeight) {
        return $this->setData(self::ROW_WEIGHT, round($rowWeight, 4));
    }

    /**
     * @inheritdoc
     */
    public function getProductType() {
        return $this->getData(self::PRODUCT_TYPE);
    }	
    /**
     * @inheritdoc
     */
    public function setProductType($productType) {
        return $this->setData(self::PRODUCT_TYPE, $productType);
    }

    /**
     * @inheritdoc
     */
    public function getBaseTaxBeforeDiscount() {
        return $this->getData(self::BASE_TAX_BEFORE_DISCOUNT);
    }	
    /**
     * @inheritdoc
     */
    public function setBaseTaxBeforeDiscount($baseTaxBeforeDiscount) {
        return $this->setData(self::BASE_TAX_BEFORE_DISCOUNT, round($baseTaxBeforeDiscount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getTaxBeforeDiscount() {
        return $this->getData(self::TAX_BEFORE_DISCOUNT);
    }	
    /**
     * @inheritdoc
     */
    public function setTaxBeforeDiscount($taxBeforeDiscount) {
        return $this->setData(self::TAX_BEFORE_DISCOUNT, round($taxBeforeDiscount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getOriginalCustomPrice() {
        return $this->getData(self::ORIGINAL_CUSTOM_PRICE);
    }	
    /**
     * @inheritdoc
     */
    public function setOriginalCustomPrice($originalCustomPrice) {
        return $this->setData(self::ORIGINAL_CUSTOM_PRICE, round($originalCustomPrice, 4));
    }

    /**
     * @inheritdoc
     */
    public function getRedirectUrl() {
        return $this->getData(self::REDIRECT_URL);
    }	
    /**
     * @inheritdoc
     */
    public function setRedirectUrl($redirectUrl) {
        return $this->setData(self::REDIRECT_URL, $redirectUrl);
    }

    /**
     * @inheritdoc
     */
    public function getBaseCost() {
        return $this->getData(self::BASE_COST);
    }	
    /**
     * @inheritdoc
     */
    public function setBaseCost($baseCost) {
        return $this->setData(self::BASE_COST, round($baseCost, 4));
    }

    /**
     * @inheritdoc
     */
    public function getPriceInclTax() {
        return $this->getData(self::PRICE_INCL_TAX);
    }	
    /**
     * @inheritdoc
     */
    public function setPriceInclTax($priceInclTax) {
        return $this->setData(self::PRICE_INCL_TAX, round($priceInclTax, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBasePriceInclTax() {
        return $this->getData(self::BASE_PRICE_INCL_TAX);
    }	
    /**
     * @inheritdoc
     */
    public function setBasePriceInclTax($basePriceInclTax) {
        return $this->setData(self::BASE_PRICE_INCL_TAX, round($basePriceInclTax, 4));
    }

    /**
     * @inheritdoc
     */
    public function getRowTotalInclTax() {
        return $this->getData(self::ROW_TOTAL_INCL_TAX);
    }	
    /**
     * @inheritdoc
     */
    public function setRowTotalInclTax($rowTotalInclTax) {
        return $this->setData(self::ROW_TOTAL_INCL_TAX, round($rowTotalInclTax, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseRowTotalInclTax() {
        return $this->getData(self::BASE_ROW_TOTAL_INCL_TAX);
    }	
    /**
     * @inheritdoc
     */
    public function setBaseRowTotalInclTax($baseRowTotalInclTax) {
        return $this->setData(self::BASE_ROW_TOTAL_INCL_TAX, round($baseRowTotalInclTax, 4));
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
    public function getWeeeTaxApplied() {
        return $this->getData(self::WEEE_TAX_APPLIED);
    }	
    /**
     * @inheritdoc
     */
    public function setWeeeTaxApplied($weeeTaxApplied) {
        return $this->setData(self::WEEE_TAX_APPLIED, $weeeTaxApplied);
    }

    /**
     * @inheritdoc
     */
    public function getWeeeTaxAppliedAmount() {
        return $this->getData(self::WEEE_TAX_APPLIED_AMOUNT);
    }	
    /**
     * @inheritdoc
     */
    public function setWeeeTaxAppliedAmount($weeeTaxAppliedAmount) {
        return $this->setData(self::WEEE_TAX_APPLIED_AMOUNT, round($weeeTaxAppliedAmount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getWeeeTaxAppliedRowAmount() {
        return $this->getData(self::WEEE_TAX_APPLIED_ROW_AMOUNT);
    }	
    /**
     * @inheritdoc
     */
    public function setWeeeTaxAppliedRowAmount($weeeTaxAppliedRowAmount) {
        return $this->setData(self::WEEE_TAX_APPLIED_ROW_AMOUNT, round($weeeTaxAppliedRowAmount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getWeeeTaxDisposition() {
        return $this->getData(self::WEEE_TAX_DISPOSITION);
    }	
    /**
     * @inheritdoc
     */
    public function setWeeeTaxDisposition($weeeTaxDisposition) {
        return $this->setData(self::WEEE_TAX_DISPOSITION, round($weeeTaxDisposition, 4));
    }

    /**
     * @inheritdoc
     */
    public function getWeeeTaxRowDisposition() {
        return $this->getData(self::WEEE_TAX_ROW_DISPOSITION);
    }	
    /**
     * @inheritdoc
     */
    public function setWeeeTaxRowDisposition($weeeTaxRowDisposition) {
        return $this->setData(self::WEEE_TAX_ROW_DISPOSITION, round($weeeTaxRowDisposition, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseWeeeTaxAppliedAmount() {
        return $this->getData(self::BASE_WEEE_TAX_APPLIED_AMOUNT);
    }	
    /**
     * @inheritdoc
     */
    public function setBaseWeeeTaxAppliedAmount($baseWeeeTaxAppliedAmount) {
        return $this->setData(self::BASE_WEEE_TAX_APPLIED_AMOUNT, round($baseWeeeTaxAppliedAmount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseWeeeTaxAppliedRowAmnt() {
        return $this->getData(self::BASE_WEEE_TAX_APPLIED_ROW_AMNT);
    }	
    /**
     * @inheritdoc
     */
    public function setBaseWeeeTaxAppliedRowAmnt($baseWeeeTaxAppliedRowAmnt) {
        return $this->setData(self::BASE_WEEE_TAX_APPLIED_ROW_AMNT, round($baseWeeeTaxAppliedRowAmnt, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseWeeeTaxDisposition() {
        return $this->getData(self::BASE_WEEE_TAX_DISPOSITION);
    }	
    /**
     * @inheritdoc
     */
    public function setBaseWeeeTaxDisposition($baseWeeeTaxDisposition) {
        return $this->setData(self::BASE_WEEE_TAX_DISPOSITION, round($baseWeeeTaxDisposition, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseWeeeTaxRowDisposition() {
        return $this->getData(self::BASE_WEEE_TAX_ROW_DISPOSITION);
    }	
    /**
     * @inheritdoc
     */
    public function setBaseWeeeTaxRowDisposition($baseWeeeTaxRowDisposition) {
        return $this->setData(self::BASE_WEEE_TAX_ROW_DISPOSITION, round($baseWeeeTaxRowDisposition, 4));
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
    public function getTmpItemId() {
        return $this->getData(self::TMP_ITEM_ID);
    }	
    /**
     * @inheritdoc
     */
    public function setTmpItemId($tmpItemId) {
        return $this->setData(self::TMP_ITEM_ID, $tmpItemId);
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
        \Magestore\Webpos\Api\Data\Checkout\Quote\ItemExtensionInterface $extensionAttributes
    ){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}