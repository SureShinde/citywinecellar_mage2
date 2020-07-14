<?php

namespace Magestore\Webpos\Model\Checkout\Order\Item;

class Tax extends \Magento\Framework\DataObject
    implements \Magestore\Webpos\Api\Data\Checkout\Order\Item\TaxInterface
{

    /**
     * @inheritdoc
     */
    public function getTaxItemId() {
        return $this->getData(self::TAX_ITEM_ID);
    }	
    /**
     * @inheritdoc
     */
    public function setTaxItemId($taxItemId) {
        return $this->setData(self::TAX_ITEM_ID, $taxItemId);
    }

    /**
     * @inheritdoc
     */
    public function getTaxId() {
        return $this->getData(self::TAX_ID);
    }	
    /**
     * @inheritdoc
     */
    public function setTaxId($taxId) {
        return $this->setData(self::TAX_ID, $taxId);
    }

    /**
     * @inheritdoc
     */
    public function getTaxCode(){
        return $this->getData(self::TAX_CODE);
    }
    /**
     * @inheritdoc
     */
    public function setTaxCode($taxCode){
        return $this->setData(self::TAX_CODE, $taxCode);
    }

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
    public function getTaxPercent() {
        return $this->getData(self::TAX_PERCENT);
    }	
    /**
     * @inheritdoc
     */
    public function setTaxPercent($taxPercent) {
        return $this->setData(self::TAX_PERCENT, $taxPercent);
    }

    /**
     * @inheritdoc
     */
    public function getAmount() {
        return $this->getData(self::AMOUNT);
    }	
    /**
     * @inheritdoc
     */
    public function setAmount($amount) {
        return $this->setData(self::AMOUNT, $amount);
    }

    /**
     * @inheritdoc
     */
    public function getBaseAmount() {
        return $this->getData(self::BASE_AMOUNT);
    }	
    /**
     * @inheritdoc
     */
    public function setBaseAmount($baseAmount) {
        return $this->setData(self::BASE_AMOUNT, $baseAmount);
    }

    /**
     * @inheritdoc
     */
    public function getRealAmount() {
        return $this->getData(self::REAL_AMOUNT);
    }	
    /**
     * @inheritdoc
     */
    public function setRealAmount($realAmount) {
        return $this->setData(self::REAL_AMOUNT, $realAmount);
    }

    /**
     * @inheritdoc
     */
    public function getRealBaseAmount() {
        return $this->getData(self::REAL_BASE_AMOUNT);
    }	
    /**
     * @inheritdoc
     */
    public function setRealBaseAmount($realBaseAmount) {
        return $this->setData(self::REAL_BASE_AMOUNT, $realBaseAmount);
    }

    /**
     * @inheritdoc
     */
    public function getAssociatedItemId() {
        return $this->getData(self::ASSOCIATED_ITEM_ID);
    }	
    /**
     * @inheritdoc
     */
    public function setAssociatedItemId($associatedItemId) {
        return $this->setData(self::ASSOCIATED_ITEM_ID, $associatedItemId);
    }

    /**
     * @inheritdoc
     */
    public function getTaxableItemType() {
        return $this->getData(self::TAXABLE_ITEM_TYPE);
    }	
    /**
     * @inheritdoc
     */
    public function setTaxableItemType($taxableItemType) {
        return $this->setData(self::TAXABLE_ITEM_TYPE, $taxableItemType);
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
        \Magestore\Webpos\Api\Data\Checkout\Order\Item\TaxExtensionInterface $extensionAttributes
    ){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}