<?php

namespace Magestore\Webpos\Model\Checkout\Order;

use Magento\Sales\Model\Order\ItemFactory;
use Magento\Framework\App\ResourceConnection;

class Item extends \Magento\Framework\Model\AbstractModel
    implements \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
{
    /**
     * @var ItemFactory
     */
    protected $orderItemFactory;
    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $helper;
    /**
     * @var string
     */
    protected $magentoVersion;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\Order\ItemFactory $itemFactory,
        \Magestore\Webpos\Helper\Data $helper,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->orderItemFactory = $itemFactory;
        $this->helper = $helper;
        $this->magentoVersion = $productMetadata->getVersion();
    }

    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magestore\Webpos\Model\ResourceModel\Sales\Order\Item::class);
    }

    /**
     * @inheritdoc
     */
    public function getItemId()
    {
        return $this->getData(self::ITEM_ID);
    }

    /**
     * @inheritdoc
     */
    public function setItemId($itemId)
    {
        return $this->setData(self::ITEM_ID, $itemId);
    }

    /**
     * @inheritdoc
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * @inheritdoc
     */
    public function getParentItemId()
    {
        return $this->getData(self::PARENT_ITEM_ID);
    }

    /**
     * @inheritdoc
     */
    public function setParentItemId($parentItemId)
    {
        return $this->setData(self::PARENT_ITEM_ID, $parentItemId);
    }

    /**
     * @inheritdoc
     */
    public function getQuoteItemId()
    {
        return $this->getData(self::QUOTE_ITEM_ID);
    }

    /**
     * @inheritdoc
     */
    public function setQuoteItemId($quoteItemId)
    {
        return $this->setData(self::QUOTE_ITEM_ID, $quoteItemId);
    }

    /**
     * @inheritdoc
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * @inheritdoc
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * @inheritdoc
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * @inheritdoc
     */
    public function getProductType()
    {
        return $this->getData(self::PRODUCT_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setProductType($productType)
    {
        return $this->setData(self::PRODUCT_TYPE, $productType);
    }

    /**
     * @inheritdoc
     */
    public function getProductOptions()
    {
        $productOptions = $this->getData(self::PRODUCT_OPTIONS);
        if (version_compare($this->magentoVersion, '2.2.0', '<')) {
            if (is_string($productOptions)) {
                try {
                    $productOptions = unserialize($productOptions);
                    if (isset($productOptions['bundle_selection_attributes'])) {
                        $productOptions['bundle_selection_attributes'] =
                            unserialize($productOptions['bundle_selection_attributes']);
                    }
                } catch (\Exception $e) {
                    $productOptions = $productOptions;
                }
            }
        }
        if (is_array($productOptions))
            return json_encode($productOptions);
        return $this->getData(self::PRODUCT_OPTIONS);
    }

    /**
     * @inheritdoc
     */
    public function setProductOptions($productOptions)
    {
        if (version_compare($this->magentoVersion, '2.2.0', '<')) {
            $productOptions = json_decode($productOptions, true);
            if (isset($productOptions['bundle_selection_attributes'])) {
                $productOptions['bundle_selection_attributes'] =
                    serialize(json_decode($productOptions['bundle_selection_attributes'], true));
            }
//            $productOptions = serialize($productOptions);
        }
        return $this->setData(self::PRODUCT_OPTIONS, $productOptions);
    }

    /**
     * @inheritdoc
     */
    public function getWeight()
    {
        return $this->getData(self::WEIGHT);
    }

    /**
     * @inheritdoc
     */
    public function setWeight($weight)
    {
        return $this->setData(self::WEIGHT, round($weight, 4));
    }

    /**
     * @inheritdoc
     */
    public function getIsVirtual()
    {
        return $this->getData(self::IS_VIRTUAL);
    }

    /**
     * @inheritdoc
     */
    public function setIsVirtual($isVirtual)
    {
        return $this->setData(self::IS_VIRTUAL, $isVirtual);
    }

    /**
     * @inheritdoc
     */
    public function getSku()
    {
        return $this->getData(self::SKU);
    }

    /**
     * @inheritdoc
     */
    public function setSku($sku)
    {
        return $this->setData(self::SKU, $sku);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @inheritdoc
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @inheritdoc
     */
    public function getAppliedRuleIds()
    {
        return $this->getData(self::APPLIED_RULE_IDS);
    }

    /**
     * @inheritdoc
     */
    public function setAppliedRuleIds($appliedRuleIds)
    {
        return $this->setData(self::APPLIED_RULE_IDS, $appliedRuleIds);
    }

    /**
     * @inheritdoc
     */
    public function getAdditionalData()
    {
        return $this->getData(self::ADDITIONAL_DATA);
    }

    /**
     * @inheritdoc
     */
    public function setAdditionalData($additionalData)
    {
        return $this->setData(self::ADDITIONAL_DATA, $additionalData);
    }

    /**
     * @inheritdoc
     */
    public function getIsQtyDecimal()
    {
        return $this->getData(self::IS_QTY_DECIMAL);
    }

    /**
     * @inheritdoc
     */
    public function setIsQtyDecimal($isQtyDecimal)
    {
        return $this->setData(self::IS_QTY_DECIMAL, $isQtyDecimal);
    }

    /**
     * @inheritdoc
     */
    public function getNoDiscount()
    {
        return $this->getData(self::NO_DISCOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setNoDiscount($noDiscount)
    {
        return $this->setData(self::NO_DISCOUNT, $noDiscount);
    }

    /**
     * @inheritdoc
     */
    public function getQtyBackordered()
    {
        return $this->getData(self::QTY_BACKORDERED);
    }

    /**
     * @inheritdoc
     */
    public function setQtyBackordered($qtyBackordered)
    {
        return $this->setData(self::QTY_BACKORDERED, round($qtyBackordered, 4));
    }

    /**
     * @inheritdoc
     */
    public function getQtyCanceled()
    {
        return $this->getData(self::QTY_CANCELED);
    }

    /**
     * @inheritdoc
     */
    public function setQtyCanceled($qtyCanceled)
    {
        return $this->setData(self::QTY_CANCELED, round($qtyCanceled, 4));
    }

    /**
     * @inheritdoc
     */
    public function getQtyInvoiced()
    {
        return $this->getData(self::QTY_INVOICED);
    }

    /**
     * @inheritdoc
     */
    public function setQtyInvoiced($qtyInvoiced)
    {
        return $this->setData(self::QTY_INVOICED, round($qtyInvoiced, 4));
    }

    /**
     * @inheritdoc
     */
    public function getQtyOrdered()
    {
        return $this->getData(self::QTY_ORDERED);
    }

    /**
     * @inheritdoc
     */
    public function setQtyOrdered($qtyOrdered)
    {
        return $this->setData(self::QTY_ORDERED, round($qtyOrdered, 4));
    }

    /**
     * @inheritdoc
     */
    public function getQtyRefunded()
    {
        return $this->getData(self::QTY_REFUNDED);
    }

    /**
     * @inheritdoc
     */
    public function setQtyRefunded($qtyRefunded)
    {
        return $this->setData(self::QTY_REFUNDED, round($qtyRefunded, 4));
    }

    /**
     * @inheritdoc
     */
    public function getQtyShipped()
    {
        return $this->getData(self::QTY_SHIPPED);
    }

    /**
     * @inheritdoc
     */
    public function setQtyShipped($qtyShipped)
    {
        return $this->setData(self::QTY_SHIPPED, round($qtyShipped, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseCost()
    {
        return $this->getData(self::BASE_COST);
    }

    /**
     * @inheritdoc
     */
    public function setBaseCost($baseCost)
    {
        return $this->setData(self::BASE_COST, round($baseCost, 4));
    }

    /**
     * @inheritdoc
     */
    public function getPrice()
    {
        return $this->getData(self::PRICE);
    }

    /**
     * @inheritdoc
     */
    public function setPrice($price)
    {
        return $this->setData(self::PRICE, round($price, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBasePrice()
    {
        return $this->getData(self::BASE_PRICE);
    }

    /**
     * @inheritdoc
     */
    public function setBasePrice($basePrice)
    {
        return $this->setData(self::BASE_PRICE, round($basePrice, 4));
    }

    /**
     * @inheritdoc
     */
    public function getOriginalPrice()
    {
        return $this->getData(self::ORIGINAL_PRICE);
    }

    /**
     * @inheritdoc
     */
    public function setOriginalPrice($originalPrice)
    {
        return $this->setData(self::ORIGINAL_PRICE, round($originalPrice, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseOriginalPrice()
    {
        return $this->getData(self::BASE_ORIGINAL_PRICE);
    }

    /**
     * @inheritdoc
     */
    public function setBaseOriginalPrice($baseOriginalPrice)
    {
        return $this->setData(self::BASE_ORIGINAL_PRICE, round($baseOriginalPrice, 4));
    }

    /**
     * @inheritdoc
     */
    public function getPosOriginalPriceExclTax()
    {
        return $this->getData(self::POS_ORIGINAL_PRICE_EXCL_TAX);
    }

    /**
     * @inheritdoc
     */
    public function setPosOriginalPriceExclTax($posOriginalPriceExclTax)
    {
        return $this->setData(self::POS_ORIGINAL_PRICE_EXCL_TAX, round($posOriginalPriceExclTax, 4));
    }

    /**
     * @inheritdoc
     */
    public function getPosBaseOriginalPriceExclTax()
    {
        return $this->getData(self::POS_BASE_ORIGINAL_PRICE_EXCL_TAX);
    }

    /**
     * @inheritdoc
     */
    public function setPosBaseOriginalPriceExclTax($posBaseOriginalPriceExclTax)
    {
        return $this->setData(self::POS_BASE_ORIGINAL_PRICE_EXCL_TAX, round($posBaseOriginalPriceExclTax, 4));
    }

    /**
     * @inheritdoc
     */
    public function getPosOriginalPriceInclTax()
    {
        return $this->getData(self::POS_ORIGINAL_PRICE_INCL_TAX);
    }

    /**
     * @inheritdoc
     */
    public function setPosOriginalPriceInclTax($posOriginalPriceInclTax)
    {
        return $this->setData(self::POS_ORIGINAL_PRICE_INCL_TAX, round($posOriginalPriceInclTax, 4));
    }

    /**
     * @inheritdoc
     */
    public function getPosBaseOriginalPriceInclTax()
    {
        return $this->getData(self::POS_BASE_ORIGINAL_PRICE_INCL_TAX);
    }

    /**
     * @inheritdoc
     */
    public function setPosBaseOriginalPriceInclTax($posBaseOriginalPriceInclTax)
    {
        return $this->setData(self::POS_BASE_ORIGINAL_PRICE_INCL_TAX, round($posBaseOriginalPriceInclTax, 4));
    }

    /**
     * @inheritdoc
     */
    public function getTaxPercent()
    {
        return $this->getData(self::TAX_PERCENT);
    }

    /**
     * @inheritdoc
     */
    public function setTaxPercent($taxPercent)
    {
        return $this->setData(self::TAX_PERCENT, round($taxPercent, 4));
    }

    /**
     * @inheritdoc
     */
    public function getTaxAmount()
    {
        return $this->getData(self::TAX_AMOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setTaxAmount($taxAmount)
    {
        return $this->setData(self::TAX_AMOUNT, round($taxAmount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseTaxAmount()
    {
        return $this->getData(self::BASE_TAX_AMOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setBaseTaxAmount($baseTaxAmount)
    {
        return $this->setData(self::BASE_TAX_AMOUNT, round($baseTaxAmount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getTaxInvoiced()
    {
        return $this->getData(self::TAX_INVOICED);
    }

    /**
     * @inheritdoc
     */
    public function setTaxInvoiced($taxInvoiced)
    {
        return $this->setData(self::TAX_INVOICED, round($taxInvoiced, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseTaxInvoiced()
    {
        return $this->getData(self::BASE_TAX_INVOICED);
    }

    /**
     * @inheritdoc
     */
    public function setBaseTaxInvoiced($baseTaxInvoiced)
    {
        return $this->setData(self::BASE_TAX_INVOICED, round($baseTaxInvoiced, 4));
    }

    /**
     * @inheritdoc
     */
    public function getDiscountPercent()
    {
        return $this->getData(self::DISCOUNT_PERCENT);
    }

    /**
     * @inheritdoc
     */
    public function setDiscountPercent($discountPercent)
    {
        return $this->setData(self::DISCOUNT_PERCENT, round($discountPercent, 4));
    }

    /**
     * @inheritdoc
     */
    public function getDiscountAmount()
    {
        return $this->getData(self::DISCOUNT_AMOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setDiscountAmount($discountAmount)
    {
        return $this->setData(self::DISCOUNT_AMOUNT, round($discountAmount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseDiscountAmount()
    {
        return $this->getData(self::BASE_DISCOUNT_AMOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setBaseDiscountAmount($baseDiscountAmount)
    {
        return $this->setData(self::BASE_DISCOUNT_AMOUNT, round($baseDiscountAmount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getDiscountInvoiced()
    {
        return $this->getData(self::DISCOUNT_INVOICED);
    }

    /**
     * @inheritdoc
     */
    public function setDiscountInvoiced($discountInvoiced)
    {
        return $this->setData(self::DISCOUNT_INVOICED, round($discountInvoiced, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseDiscountInvoiced()
    {
        return $this->getData(self::BASE_DISCOUNT_INVOICED);
    }

    /**
     * @inheritdoc
     */
    public function setBaseDiscountInvoiced($baseDiscountInvoiced)
    {
        return $this->setData(self::BASE_DISCOUNT_INVOICED, round($baseDiscountInvoiced, 4));
    }

    /**
     * @inheritdoc
     */
    public function getAmountRefunded()
    {
        return $this->getData(self::AMOUNT_REFUNDED);
    }

    /**
     * @inheritdoc
     */
    public function setAmountRefunded($amountRefunded)
    {
        return $this->setData(self::AMOUNT_REFUNDED, round($amountRefunded, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseAmountRefunded()
    {
        return $this->getData(self::BASE_AMOUNT_REFUNDED);
    }

    /**
     * @inheritdoc
     */
    public function setBaseAmountRefunded($baseAmountRefunded)
    {
        return $this->setData(self::BASE_AMOUNT_REFUNDED, round($baseAmountRefunded, 4));
    }

    /**
     * @inheritdoc
     */
    public function getRowTotal()
    {
        return $this->getData(self::ROW_TOTAL);
    }

    /**
     * @inheritdoc
     */
    public function setRowTotal($rowTotal)
    {
        return $this->setData(self::ROW_TOTAL, round($rowTotal, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseRowTotal()
    {
        return $this->getData(self::BASE_ROW_TOTAL);
    }

    /**
     * @inheritdoc
     */
    public function setBaseRowTotal($baseRowTotal)
    {
        return $this->setData(self::BASE_ROW_TOTAL, round($baseRowTotal, 4));
    }

    /**
     * @inheritdoc
     */
    public function getRowInvoiced()
    {
        return $this->getData(self::ROW_INVOICED);
    }

    /**
     * @inheritdoc
     */
    public function setRowInvoiced($rowInvoiced)
    {
        return $this->setData(self::ROW_INVOICED, round($rowInvoiced, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseRowInvoiced()
    {
        return $this->getData(self::BASE_ROW_INVOICED);
    }

    /**
     * @inheritdoc
     */
    public function setBaseRowInvoiced($baseRowInvoiced)
    {
        return $this->setData(self::BASE_ROW_INVOICED, round($baseRowInvoiced, 4));
    }

    /**
     * @inheritdoc
     */
    public function getRowWeight()
    {
        return $this->getData(self::ROW_WEIGHT);
    }

    /**
     * @inheritdoc
     */
    public function setRowWeight($rowWeight)
    {
        return $this->setData(self::ROW_WEIGHT, round($rowWeight, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseTaxBeforeDiscount()
    {
        return $this->getData(self::BASE_TAX_BEFORE_DISCOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setBaseTaxBeforeDiscount($baseTaxBeforeDiscount)
    {
        return $this->setData(self::BASE_TAX_BEFORE_DISCOUNT, round($baseTaxBeforeDiscount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getTaxBeforeDiscount()
    {
        return $this->getData(self::TAX_BEFORE_DISCOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setTaxBeforeDiscount($taxBeforeDiscount)
    {
        return $this->setData(self::TAX_BEFORE_DISCOUNT, round($taxBeforeDiscount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getExtOrderItemId()
    {
        return $this->getData(self::EXT_ORDER_ITEM_ID);
    }

    /**
     * @inheritdoc
     */
    public function setExtOrderItemId($extOrderItemId)
    {
        return $this->setData(self::EXT_ORDER_ITEM_ID, $extOrderItemId);
    }

    /**
     * @inheritdoc
     */
    public function getLockedDoInvoice()
    {
        return $this->getData(self::LOCKED_DO_INVOICE);
    }

    /**
     * @inheritdoc
     */
    public function setLockedDoInvoice($lockedDoInvoice)
    {
        return $this->setData(self::LOCKED_DO_INVOICE, $lockedDoInvoice);
    }

    /**
     * @inheritdoc
     */
    public function getLockedDoShip()
    {
        return $this->getData(self::LOCKED_DO_SHIP);
    }

    /**
     * @inheritdoc
     */
    public function setLockedDoShip($lockedDoShip)
    {
        return $this->setData(self::LOCKED_DO_SHIP, $lockedDoShip);
    }

    /**
     * @inheritdoc
     */
    public function getPriceInclTax()
    {
        return $this->getData(self::PRICE_INCL_TAX);
    }

    /**
     * @inheritdoc
     */
    public function setPriceInclTax($priceInclTax)
    {
        return $this->setData(self::PRICE_INCL_TAX, round($priceInclTax, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBasePriceInclTax()
    {
        return $this->getData(self::BASE_PRICE_INCL_TAX);
    }

    /**
     * @inheritdoc
     */
    public function setBasePriceInclTax($basePriceInclTax)
    {
        return $this->setData(self::BASE_PRICE_INCL_TAX, round($basePriceInclTax, 4));
    }

    /**
     * @inheritdoc
     */
    public function getRowTotalInclTax()
    {
        return $this->getData(self::ROW_TOTAL_INCL_TAX);
    }

    /**
     * @inheritdoc
     */
    public function setRowTotalInclTax($rowTotalInclTax)
    {
        return $this->setData(self::ROW_TOTAL_INCL_TAX, round($rowTotalInclTax, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseRowTotalInclTax()
    {
        return $this->getData(self::BASE_ROW_TOTAL_INCL_TAX);
    }

    /**
     * @inheritdoc
     */
    public function setBaseRowTotalInclTax($baseRowTotalInclTax)
    {
        return $this->setData(self::BASE_ROW_TOTAL_INCL_TAX, round($baseRowTotalInclTax, 4));
    }

    /**
     * @inheritdoc
     */
    public function getDiscountTaxCompensationAmount()
    {
        return $this->getData(self::DISCOUNT_TAX_COMPENSATION_AMOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setDiscountTaxCompensationAmount($discountTaxCompensationAmount)
    {
        return $this->setData(self::DISCOUNT_TAX_COMPENSATION_AMOUNT, round($discountTaxCompensationAmount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseDiscountTaxCompensationAmount()
    {
        return $this->getData(self::BASE_DISCOUNT_TAX_COMPENSATION_AMOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setBaseDiscountTaxCompensationAmount($baseDiscountTaxCompensationAmount)
    {
        return $this->setData(self::BASE_DISCOUNT_TAX_COMPENSATION_AMOUNT, round($baseDiscountTaxCompensationAmount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getDiscountTaxCompensationInvoiced()
    {
        return $this->getData(self::DISCOUNT_TAX_COMPENSATION_INVOICED);
    }

    /**
     * @inheritdoc
     */
    public function setDiscountTaxCompensationInvoiced($discountTaxCompensationInvoiced)
    {
        return $this->setData(self::DISCOUNT_TAX_COMPENSATION_INVOICED, round($discountTaxCompensationInvoiced, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseDiscountTaxCompensationInvoiced()
    {
        return $this->getData(self::BASE_DISCOUNT_TAX_COMPENSATION_INVOICED);
    }

    /**
     * @inheritdoc
     */
    public function setBaseDiscountTaxCompensationInvoiced($baseDiscountTaxCompensationInvoiced)
    {
        return $this->setData(self::BASE_DISCOUNT_TAX_COMPENSATION_INVOICED, round($baseDiscountTaxCompensationInvoiced, 4));
    }

    /**
     * @inheritdoc
     */
    public function getDiscountTaxCompensationRefunded()
    {
        return $this->getData(self::DISCOUNT_TAX_COMPENSATION_REFUNDED);
    }

    /**
     * @inheritdoc
     */
    public function setDiscountTaxCompensationRefunded($discountTaxCompensationRefunded)
    {
        return $this->setData(self::DISCOUNT_TAX_COMPENSATION_REFUNDED, round($discountTaxCompensationRefunded, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseDiscountTaxCompensationRefunded()
    {
        return $this->getData(self::BASE_DISCOUNT_TAX_COMPENSATION_REFUNDED);
    }

    /**
     * @inheritdoc
     */
    public function setBaseDiscountTaxCompensationRefunded($baseDiscountTaxCompensationRefunded)
    {
        return $this->setData(self::BASE_DISCOUNT_TAX_COMPENSATION_REFUNDED, round($baseDiscountTaxCompensationRefunded, 4));
    }

    /**
     * @inheritdoc
     */
    public function getTaxCanceled()
    {
        return $this->getData(self::TAX_CANCELED);
    }

    /**
     * @inheritdoc
     */
    public function setTaxCanceled($taxCanceled)
    {
        return $this->setData(self::TAX_CANCELED, round($taxCanceled, 4));
    }

    /**
     * @inheritdoc
     */
    public function getDiscountTaxCompensationCanceled()
    {
        return $this->getData(self::DISCOUNT_TAX_COMPENSATION_CANCELED);
    }

    /**
     * @inheritdoc
     */
    public function setDiscountTaxCompensationCanceled($discountTaxCompensationCanceled)
    {
        return $this->setData(self::DISCOUNT_TAX_COMPENSATION_CANCELED, round($discountTaxCompensationCanceled, 4));
    }

    /**
     * @inheritdoc
     */
    public function getTaxRefunded()
    {
        return $this->getData(self::TAX_REFUNDED);
    }

    /**
     * @inheritdoc
     */
    public function setTaxRefunded($taxRefunded)
    {
        return $this->setData(self::TAX_REFUNDED, round($taxRefunded, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseTaxRefunded()
    {
        return $this->getData(self::BASE_TAX_REFUNDED);
    }

    /**
     * @inheritdoc
     */
    public function setBaseTaxRefunded($baseTaxRefunded)
    {
        return $this->setData(self::BASE_TAX_REFUNDED, round($baseTaxRefunded, 4));
    }

    /**
     * @inheritdoc
     */
    public function getDiscountRefunded()
    {
        return $this->getData(self::DISCOUNT_REFUNDED);
    }

    /**
     * @inheritdoc
     */
    public function setDiscountRefunded($discountRefunded)
    {
        return $this->setData(self::DISCOUNT_REFUNDED, round($discountRefunded, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseDiscountRefunded()
    {
        return $this->getData(self::BASE_DISCOUNT_REFUNDED);
    }

    /**
     * @inheritdoc
     */
    public function setBaseDiscountRefunded($baseDiscountRefunded)
    {
        return $this->setData(self::BASE_DISCOUNT_REFUNDED, round($baseDiscountRefunded, 4));
    }

    /**
     * @inheritdoc
     */
    public function getGiftMessageId()
    {
        return $this->getData(self::GIFT_MESSAGE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setGiftMessageId($giftMessageId)
    {
        return $this->setData(self::GIFT_MESSAGE_ID, $giftMessageId);
    }

    /**
     * @inheritdoc
     */
    public function getGiftMessageAvailable()
    {
        return $this->getData(self::GIFT_MESSAGE_AVAILABLE);
    }

    /**
     * @inheritdoc
     */
    public function setGiftMessageAvailable($giftMessageAvailable)
    {
        return $this->setData(self::GIFT_MESSAGE_AVAILABLE, $giftMessageAvailable);
    }

    /**
     * @inheritdoc
     */
    public function getFreeShipping()
    {
        return $this->getData(self::FREE_SHIPPING);
    }

    /**
     * @inheritdoc
     */
    public function setFreeShipping($freeShipping)
    {
        return $this->setData(self::FREE_SHIPPING, $freeShipping);
    }

    /**
     * @inheritdoc
     */
    public function getWeeeTaxApplied()
    {
        return $this->getData(self::WEEE_TAX_APPLIED);
    }

    /**
     * @inheritdoc
     */
    public function setWeeeTaxApplied($weeeTaxApplied)
    {
        return $this->setData(self::WEEE_TAX_APPLIED, $weeeTaxApplied);
    }

    /**
     * @inheritdoc
     */
    public function getWeeeTaxAppliedAmount()
    {
        return $this->getData(self::WEEE_TAX_APPLIED_AMOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setWeeeTaxAppliedAmount($weeeTaxAppliedAmount)
    {
        return $this->setData(self::WEEE_TAX_APPLIED_AMOUNT, round($weeeTaxAppliedAmount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getWeeeTaxAppliedRowAmount()
    {
        return $this->getData(self::WEEE_TAX_APPLIED_ROW_AMOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setWeeeTaxAppliedRowAmount($weeeTaxAppliedRowAmount)
    {
        return $this->setData(self::WEEE_TAX_APPLIED_ROW_AMOUNT, round($weeeTaxAppliedRowAmount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getWeeeTaxDisposition()
    {
        return $this->getData(self::WEEE_TAX_DISPOSITION);
    }

    /**
     * @inheritdoc
     */
    public function setWeeeTaxDisposition($weeeTaxDisposition)
    {
        return $this->setData(self::WEEE_TAX_DISPOSITION, round($weeeTaxDisposition, 4));
    }

    /**
     * @inheritdoc
     */
    public function getWeeeTaxRowDisposition()
    {
        return $this->getData(self::WEEE_TAX_ROW_DISPOSITION);
    }

    /**
     * @inheritdoc
     */
    public function setWeeeTaxRowDisposition($weeeTaxRowDisposition)
    {
        return $this->setData(self::WEEE_TAX_ROW_DISPOSITION, round($weeeTaxRowDisposition, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseWeeeTaxAppliedAmount()
    {
        return $this->getData(self::BASE_WEEE_TAX_APPLIED_AMOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setBaseWeeeTaxAppliedAmount($baseWeeeTaxAppliedAmount)
    {
        return $this->setData(self::BASE_WEEE_TAX_APPLIED_AMOUNT, round($baseWeeeTaxAppliedAmount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseWeeeTaxAppliedRowAmnt()
    {
        return $this->getData(self::BASE_WEEE_TAX_APPLIED_ROW_AMNT);
    }

    /**
     * @inheritdoc
     */
    public function setBaseWeeeTaxAppliedRowAmnt($baseWeeeTaxAppliedRowAmnt)
    {
        return $this->setData(self::BASE_WEEE_TAX_APPLIED_ROW_AMNT, round($baseWeeeTaxAppliedRowAmnt, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseWeeeTaxDisposition()
    {
        return $this->getData(self::BASE_WEEE_TAX_DISPOSITION);
    }

    /**
     * @inheritdoc
     */
    public function setBaseWeeeTaxDisposition($baseWeeeTaxDisposition)
    {
        return $this->setData(self::BASE_WEEE_TAX_DISPOSITION, round($baseWeeeTaxDisposition, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseWeeeTaxRowDisposition()
    {
        return $this->getData(self::BASE_WEEE_TAX_ROW_DISPOSITION);
    }

    /**
     * @inheritdoc
     */
    public function setBaseWeeeTaxRowDisposition($baseWeeeTaxRowDisposition)
    {
        return $this->setData(self::BASE_WEEE_TAX_ROW_DISPOSITION, round($baseWeeeTaxRowDisposition, 4));
    }

    /**
     * @inheritdoc
     */
    public function getMagestoreBaseDiscount()
    {
        return $this->getData(self::MAGESTORE_BASE_DISCOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setMagestoreBaseDiscount($magestoreBaseDiscount)
    {
        return $this->setData(self::MAGESTORE_BASE_DISCOUNT, $magestoreBaseDiscount);
    }

    /**
     * @inheritdoc
     */
    public function getMagestoreDiscount()
    {
        return $this->getData(self::MAGESTORE_DISCOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setMagestoreDiscount($magestoreDiscount)
    {
        return $this->setData(self::MAGESTORE_DISCOUNT, $magestoreDiscount);
    }

    /**
     * @inheritdoc
     */
    public function getGiftVoucherDiscount()
    {
        return $this->getData(self::GIFT_VOUCHER_DISCOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setGiftVoucherDiscount($giftVoucherDiscount)
    {
        return $this->setData(self::GIFT_VOUCHER_DISCOUNT, $giftVoucherDiscount);
    }

    /**
     * @inheritdoc
     */
    public function getBaseGiftVoucherDiscount()
    {
        return $this->getData(self::BASE_GIFT_VOUCHER_DISCOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setBaseGiftVoucherDiscount($baseGiftVoucherDiscount)
    {
        return $this->setData(self::BASE_GIFT_VOUCHER_DISCOUNT, $baseGiftVoucherDiscount);
    }

    /**
     * @inheritdoc
     */
    public function getSearchString()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productRepository = $objectManager
            ->get('\Magento\Catalog\Model\ProductRepository');
        $helper = $objectManager
            ->get('\Magestore\Webpos\Helper\Data');
        $searchAttribute = $helper->getStoreConfig('webpos/product_search/barcode');
        if ($searchAttribute != 'sku' && $this->getSku() != 'pos_custom_sale') {
            $product = $productRepository->getById($this->getProductId());
            return $product->getData($searchAttribute);
        }
        return null;
    }

    /**
     * Get temp item id
     * @return float|null
     */
    public function getTmpItemId()
    {
        return $this->getData(self::TMP_ITEM_ID);
    }

    /**
     * get reason for custom price
     * @return string
     */
    public function getOsPosCustomPriceReason()
    {
        return $this->getData(self::OS_POS_CUSTOM_PRICE_REASON);
    }

    /**
     * @param $osPosCustomPriceReason
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setOsPosCustomPriceReason($osPosCustomPriceReason)
    {
        return $this->setData(self::OS_POS_CUSTOM_PRICE_REASON, $osPosCustomPriceReason);
    }

    /**
     * Return checking of what shipment
     * type was for this product
     *
     * @return bool
     */
    public function isShowShipped()
    {
        if ($this->getProductType() == \Magento\Bundle\Model\Product\Type::TYPE_CODE) {
            return !$this->orderItemFactory->create()->load($this->getItemId())->isShipSeparately();
        } else if ($this->getParentItemId()) {
            $parentItem = $this->orderItemFactory->create()->load($this->getParentItemId());
            if ($parentItem->getProductType() == \Magento\Bundle\Model\Product\Type::TYPE_CODE && !$parentItem->isShipSeparately()) {
                return false;
            }
        }
        return true;
    }

    /**
     * used = invoiced - refund - redeem
     * @return float|null
     */
    public function getGiftCardQtyUsed()
    {
        if ($this->hasData(self::GIFT_CARD_QTY_USED)) {
            return $this->getData(self::GIFT_CARD_QTY_USED);
        }

        if (!$this->helper->isEnabledGiftCard()) {
            return null;
        }

        if ($this->getProductType() !== \Magestore\Giftvoucher\Model\Product\Type\Giftvoucher::GIFT_CARD_TYPE) {
            return null;
        }

        if (!$this->getItemId()) return null;

        $refundOrderItemService = $this->helper->getObjectManager()
            ->get('Magestore\Giftvoucher\Api\Sales\RefundOrderItemServiceInterface');
        $maximumQtyRefund = $refundOrderItemService->getGiftCardQtyToRefund($this);

        $this->setData(self::GIFT_CARD_QTY_USED, $this->getQtyOrdered() - $this->getQtyRefunded() - $maximumQtyRefund);
        return $this->getData(self::GIFT_CARD_QTY_USED);
    }

    /**
     * @param float|null $qty
     * @return $this|\Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setGiftCardQtyUsed($qty)
    {
        return $this->setData(self::GIFT_CARD_QTY_USED, $qty);
    }

    /**
     * @inheritdoc
     */
    public function getGiftcodesApplied()
    {
        return $this->getData(self::GIFTCODES_APPLIED);
    }

    /**
     * @inheritdoc
     */
    public function setGiftcodesApplied($giftcodesApplied)
    {
        return $this->setData(self::GIFTCODES_APPLIED, $giftcodesApplied);
    }

    /**
     * Get pos custom price
     * @return float|null
     */
    public function getPosCustomPrice()
    {
        return $this->getData(self::POS_CUSTOM_PRICE);
    }

    /**
     * Set pos custom price
     * @param float|null $posCustomPrice
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface
     */
    public function setPosCustomPrice($posCustomPrice)
    {
        return $this->setData(self::POS_CUSTOM_PRICE, $posCustomPrice);
    }
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\ItemExtensionInterface|null
     * @since 102.0.0
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\Order\ItemExtensionInterface $extensionAttributes
     * @return $this
     * @since 102.0.0
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Checkout\Order\ItemExtensionInterface $extensionAttributes
    )
    {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }

    /**
     * @inheritdoc
     */
    public function getAppliedTaxes()
    {
        if(!$this->getData(self::APPLIED_TAXES)) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            /** @var \Magento\Framework\App\ResourceConnection $resource */
            $resource = $objectManager->create('\Magento\Framework\App\ResourceConnection');
            /** @var \Magento\Framework\DB\Adapter\AdapterInterface $connection */
            $connection = $resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);

            $sql = $connection->select()
                ->from(['main_table' => $resource->getTableName('sales_order_tax_item')], '*');
            $sql->where('item_id = ?', $this->getId());
            $sql->join(
                ['sales_order_tax' => $resource->getTableName('sales_order_tax')],
                'sales_order_tax.tax_id = main_table.tax_id AND sales_order_tax.order_id = ' . $this->getOrderId(),
                ['tax_code' => 'sales_order_tax.code']
            );
            $data = $connection->fetchAll($sql);
            $this->setData(self::APPLIED_TAXES, $data);
        }
        return $this->getData(self::APPLIED_TAXES);
    }

    /**
     * @inheritdoc
     */
    public function setAppliedTaxes($appliedTaxes)
    {
        return $this->setData(self::APPLIED_TAXES, $appliedTaxes);
    }
}
