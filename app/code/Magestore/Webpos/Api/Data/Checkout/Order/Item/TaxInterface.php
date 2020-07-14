<?php

namespace Magestore\Webpos\Api\Data\Checkout\Order\Item;

interface TaxInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const TAX_ITEM_ID = 'tax_item_id';
    const TAX_ID = 'tax_id';
    const TAX_CODE = 'tax_code';
    const ITEM_ID = 'item_id';
    const TAX_PERCENT = 'tax_percent';
    const AMOUNT = 'amount';
    const BASE_AMOUNT = 'base_amount';
    const REAL_AMOUNT = 'real_amount';
    const REAL_BASE_AMOUNT = 'real_base_amount';
    const ASSOCIATED_ITEM_ID = 'associated_item_id';
    const TAXABLE_ITEM_TYPE = 'taxable_item_type';


    /**
     * Get Tax Item Id
     *
     * @return int|null
     */
    public function getTaxItemId();	
    /**
     * Set Tax Item Id
     *
     * @param int|null $taxItemId
     * @return $this
     */
    public function setTaxItemId($taxItemId);

    /**
     * Get Tax Id
     *
     * @return int|null
     */
    public function getTaxId();	
    /**
     * Set Tax Id
     *
     * @param int|null $taxId
     * @return $this
     */
    public function setTaxId($taxId);

    /**
     * Get Tax code
     *
     * @return string|null
     */
    public function getTaxCode();
    /**
     * Set Tax code
     *
     * @param string|null $taxCode
     * @return $this
     */
    public function setTaxCode($taxCode);

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
     * @return $this
     */
    public function setItemId($itemId);

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
     * @return $this
     */
    public function setTaxPercent($taxPercent);

    /**
     * Get Amount
     *
     * @return float|null
     */
    public function getAmount();	
    /**
     * Set Amount
     *
     * @param float|null $amount
     * @return $this
     */
    public function setAmount($amount);

    /**
     * Get Base Amount
     *
     * @return float|null
     */
    public function getBaseAmount();	
    /**
     * Set Base Amount
     *
     * @param float|null $baseAmount
     * @return $this
     */
    public function setBaseAmount($baseAmount);

    /**
     * Get Real Amount
     *
     * @return float|null
     */
    public function getRealAmount();	
    /**
     * Set Real Amount
     *
     * @param float|null $realAmount
     * @return $this
     */
    public function setRealAmount($realAmount);

    /**
     * Get Real Base Amount
     *
     * @return float|null
     */
    public function getRealBaseAmount();	
    /**
     * Set Real Base Amount
     *
     * @param float|null $realBaseAmount
     * @return $this
     */
    public function setRealBaseAmount($realBaseAmount);

    /**
     * Get Associated Item Id
     *
     * @return int|null
     */
    public function getAssociatedItemId();	
    /**
     * Set Associated Item Id
     *
     * @param int|null $associatedItemId
     * @return $this
     */
    public function setAssociatedItemId($associatedItemId);

    /**
     * Get Taxable Item Type
     *
     * @return string|null
     */
    public function getTaxableItemType();	
    /**
     * Set Taxable Item Type
     *
     * @param string|null $taxableItemType
     * @return $this
     */
    public function setTaxableItemType($taxableItemType);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\Item\TaxExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\Order\Item\TaxExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Checkout\Order\Item\TaxExtensionInterface $extensionAttributes
    );
}