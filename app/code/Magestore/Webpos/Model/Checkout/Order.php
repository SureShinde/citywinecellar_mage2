<?php

namespace Magestore\Webpos\Model\Checkout;

use Magento\Framework\App\ObjectManager;
use Magestore\Webpos\Api\Data\Checkout\OrderInterface;
use Magestore\Webpos\Api\Data\Checkout\Order\PaymentInterface as PaymentInterface;

/**
 * Class Order
 *
 * @package Magestore\Webpos\Model\Checkout
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Order extends \Magento\Framework\Model\AbstractModel implements \Magestore\Webpos\Api\Data\Checkout\OrderInterface
{
    const EVENT_WEBPOS_GET_ORDER_PAYMENT_AFTER = 'webpos_get_order_payment_after';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(\Magestore\Webpos\Model\ResourceModel\Sales\Order::class);
    }

    /**
     * @inheritdoc
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * @inheritdoc
     */
    public function getState()
    {
        return $this->getData(self::STATE);
    }

    /**
     * @inheritdoc
     */
    public function setState($state)
    {
        return $this->setData(self::STATE, $state);
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritdoc
     */
    public function getCouponCode()
    {
        return $this->getData(self::COUPON_CODE);
    }

    /**
     * @inheritdoc
     */
    public function setCouponCode($couponCode)
    {
        return $this->setData(self::COUPON_CODE, $couponCode);
    }

    /**
     * @inheritdoc
     */
    public function getProtectCode()
    {
        return $this->getData(self::PROTECT_CODE);
    }

    /**
     * @inheritdoc
     */
    public function setProtectCode($protectCode)
    {
        return $this->setData(self::PROTECT_CODE, $protectCode);
    }

    /**
     * @inheritdoc
     */
    public function getShippingDescription()
    {
        return $this->getData(self::SHIPPING_DESCRIPTION);
    }

    /**
     * @inheritdoc
     */
    public function setShippingDescription($shippingDescription)
    {
        return $this->setData(self::SHIPPING_DESCRIPTION, $shippingDescription);
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
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
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
    public function getBaseDiscountCanceled()
    {
        return $this->getData(self::BASE_DISCOUNT_CANCELED);
    }

    /**
     * @inheritdoc
     */
    public function setBaseDiscountCanceled($baseDiscountCanceled)
    {
        return $this->setData(self::BASE_DISCOUNT_CANCELED, round($baseDiscountCanceled, 4));
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
    public function getBaseGrandTotal()
    {
        return $this->getData(self::BASE_GRAND_TOTAL);
    }

    /**
     * @inheritdoc
     */
    public function setBaseGrandTotal($baseGrandTotal)
    {
        return $this->setData(self::BASE_GRAND_TOTAL, round($baseGrandTotal, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseShippingAmount()
    {
        return $this->getData(self::BASE_SHIPPING_AMOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setBaseShippingAmount($baseShippingAmount)
    {
        return $this->setData(self::BASE_SHIPPING_AMOUNT, round($baseShippingAmount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseShippingCanceled()
    {
        return $this->getData(self::BASE_SHIPPING_CANCELED);
    }

    /**
     * @inheritdoc
     */
    public function setBaseShippingCanceled($baseShippingCanceled)
    {
        return $this->setData(self::BASE_SHIPPING_CANCELED, round($baseShippingCanceled, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseShippingInvoiced()
    {
        return $this->getData(self::BASE_SHIPPING_INVOICED);
    }

    /**
     * @inheritdoc
     */
    public function setBaseShippingInvoiced($baseShippingInvoiced)
    {
        return $this->setData(self::BASE_SHIPPING_INVOICED, round($baseShippingInvoiced, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseShippingRefunded()
    {
        return $this->getData(self::BASE_SHIPPING_REFUNDED);
    }

    /**
     * @inheritdoc
     */
    public function setBaseShippingRefunded($baseShippingRefunded)
    {
        return $this->setData(self::BASE_SHIPPING_REFUNDED, round($baseShippingRefunded, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseShippingTaxAmount()
    {
        return $this->getData(self::BASE_SHIPPING_TAX_AMOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setBaseShippingTaxAmount($baseShippingTaxAmount)
    {
        return $this->setData(self::BASE_SHIPPING_TAX_AMOUNT, round($baseShippingTaxAmount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseShippingTaxRefunded()
    {
        return $this->getData(self::BASE_SHIPPING_TAX_REFUNDED);
    }

    /**
     * @inheritdoc
     */
    public function setBaseShippingTaxRefunded($baseShippingTaxRefunded)
    {
        return $this->setData(self::BASE_SHIPPING_TAX_REFUNDED, round($baseShippingTaxRefunded, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseSubtotal()
    {
        return $this->getData(self::BASE_SUBTOTAL);
    }

    /**
     * @inheritdoc
     */
    public function setBaseSubtotal($baseSubtotal)
    {
        return $this->setData(self::BASE_SUBTOTAL, round($baseSubtotal, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseSubtotalCanceled()
    {
        return $this->getData(self::BASE_SUBTOTAL_CANCELED);
    }

    /**
     * @inheritdoc
     */
    public function setBaseSubtotalCanceled($baseSubtotalCanceled)
    {
        return $this->setData(self::BASE_SUBTOTAL_CANCELED, round($baseSubtotalCanceled, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseSubtotalInvoiced()
    {
        return $this->getData(self::BASE_SUBTOTAL_INVOICED);
    }

    /**
     * @inheritdoc
     */
    public function setBaseSubtotalInvoiced($baseSubtotalInvoiced)
    {
        return $this->setData(self::BASE_SUBTOTAL_INVOICED, round($baseSubtotalInvoiced, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseSubtotalRefunded()
    {
        return $this->getData(self::BASE_SUBTOTAL_REFUNDED);
    }

    /**
     * @inheritdoc
     */
    public function setBaseSubtotalRefunded($baseSubtotalRefunded)
    {
        return $this->setData(self::BASE_SUBTOTAL_REFUNDED, round($baseSubtotalRefunded, 4));
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
    public function getBaseTaxCanceled()
    {
        return $this->getData(self::BASE_TAX_CANCELED);
    }

    /**
     * @inheritdoc
     */
    public function setBaseTaxCanceled($baseTaxCanceled)
    {
        return $this->setData(self::BASE_TAX_CANCELED, round($baseTaxCanceled, 4));
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
    public function getBaseToGlobalRate()
    {
        return $this->getData(self::BASE_TO_GLOBAL_RATE);
    }

    /**
     * @inheritdoc
     */
    public function setBaseToGlobalRate($baseToGlobalRate)
    {
        return $this->setData(self::BASE_TO_GLOBAL_RATE, round($baseToGlobalRate, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseToOrderRate()
    {
        return $this->getData(self::BASE_TO_ORDER_RATE) ? $this->getData(self::BASE_TO_ORDER_RATE) : 1;
    }

    /**
     * @inheritdoc
     */
    public function setBaseToOrderRate($baseToOrderRate)
    {
        return $this->setData(self::BASE_TO_ORDER_RATE, round($baseToOrderRate, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseTotalCanceled()
    {
        return $this->getData(self::BASE_TOTAL_CANCELED);
    }

    /**
     * @inheritdoc
     */
    public function setBaseTotalCanceled($baseTotalCanceled)
    {
        return $this->setData(self::BASE_TOTAL_CANCELED, round($baseTotalCanceled, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseTotalInvoiced()
    {
        return $this->getData(self::BASE_TOTAL_INVOICED);
    }

    /**
     * @inheritdoc
     */
    public function setBaseTotalInvoiced($baseTotalInvoiced)
    {
        return $this->setData(self::BASE_TOTAL_INVOICED, round($baseTotalInvoiced, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseTotalInvoicedCost()
    {
        return $this->getData(self::BASE_TOTAL_INVOICED_COST);
    }

    /**
     * @inheritdoc
     */
    public function setBaseTotalInvoicedCost($baseTotalInvoicedCost)
    {
        return $this->setData(self::BASE_TOTAL_INVOICED_COST, round($baseTotalInvoicedCost, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseTotalOfflineRefunded()
    {
        return $this->getData(self::BASE_TOTAL_OFFLINE_REFUNDED);
    }

    /**
     * @inheritdoc
     */
    public function setBaseTotalOfflineRefunded($baseTotalOfflineRefunded)
    {
        return $this->setData(self::BASE_TOTAL_OFFLINE_REFUNDED, round($baseTotalOfflineRefunded, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseTotalOnlineRefunded()
    {
        return $this->getData(self::BASE_TOTAL_ONLINE_REFUNDED);
    }

    /**
     * @inheritdoc
     */
    public function setBaseTotalOnlineRefunded($baseTotalOnlineRefunded)
    {
        return $this->setData(self::BASE_TOTAL_ONLINE_REFUNDED, round($baseTotalOnlineRefunded, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseTotalPaid()
    {
        return $this->getData(self::BASE_TOTAL_PAID);
    }

    /**
     * @inheritdoc
     */
    public function setBaseTotalPaid($baseTotalPaid)
    {
        return $this->setData(self::BASE_TOTAL_PAID, round($baseTotalPaid, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseTotalQtyOrdered()
    {
        return $this->getData(self::BASE_TOTAL_QTY_ORDERED);
    }

    /**
     * @inheritdoc
     */
    public function setBaseTotalQtyOrdered($baseTotalQtyOrdered)
    {
        return $this->setData(self::BASE_TOTAL_QTY_ORDERED, round($baseTotalQtyOrdered, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseTotalRefunded()
    {
        return $this->getData(self::BASE_TOTAL_REFUNDED);
    }

    /**
     * @inheritdoc
     */
    public function setBaseTotalRefunded($baseTotalRefunded)
    {
        return $this->setData(self::BASE_TOTAL_REFUNDED, round($baseTotalRefunded, 4));
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
    public function getDiscountCanceled()
    {
        return $this->getData(self::DISCOUNT_CANCELED);
    }

    /**
     * @inheritdoc
     */
    public function setDiscountCanceled($discountCanceled)
    {
        return $this->setData(self::DISCOUNT_CANCELED, round($discountCanceled, 4));
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
    public function getGrandTotal()
    {
        return $this->getData(self::GRAND_TOTAL);
    }

    /**
     * @inheritdoc
     */
    public function setGrandTotal($grandTotal)
    {
        return $this->setData(self::GRAND_TOTAL, round($grandTotal, 4));
    }

    /**
     * @inheritdoc
     */
    public function getShippingAmount()
    {
        return $this->getData(self::SHIPPING_AMOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setShippingAmount($shippingAmount)
    {
        return $this->setData(self::SHIPPING_AMOUNT, round($shippingAmount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getShippingCanceled()
    {
        return $this->getData(self::SHIPPING_CANCELED);
    }

    /**
     * @inheritdoc
     */
    public function setShippingCanceled($shippingCanceled)
    {
        return $this->setData(self::SHIPPING_CANCELED, round($shippingCanceled, 4));
    }

    /**
     * @inheritdoc
     */
    public function getShippingInvoiced()
    {
        return $this->getData(self::SHIPPING_INVOICED);
    }

    /**
     * @inheritdoc
     */
    public function setShippingInvoiced($shippingInvoiced)
    {
        return $this->setData(self::SHIPPING_INVOICED, round($shippingInvoiced, 4));
    }

    /**
     * @inheritdoc
     */
    public function getShippingRefunded()
    {
        return $this->getData(self::SHIPPING_REFUNDED);
    }

    /**
     * @inheritdoc
     */
    public function setShippingRefunded($shippingRefunded)
    {
        return $this->setData(self::SHIPPING_REFUNDED, round($shippingRefunded, 4));
    }

    /**
     * @inheritdoc
     */
    public function getShippingTaxAmount()
    {
        return $this->getData(self::SHIPPING_TAX_AMOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setShippingTaxAmount($shippingTaxAmount)
    {
        return $this->setData(self::SHIPPING_TAX_AMOUNT, round($shippingTaxAmount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getShippingTaxRefunded()
    {
        return $this->getData(self::SHIPPING_TAX_REFUNDED);
    }

    /**
     * @inheritdoc
     */
    public function setShippingTaxRefunded($shippingTaxRefunded)
    {
        return $this->setData(self::SHIPPING_TAX_REFUNDED, round($shippingTaxRefunded, 4));
    }

    /**
     * @inheritdoc
     */
    public function getStoreToBaseRate()
    {
        return $this->getData(self::STORE_TO_BASE_RATE);
    }

    /**
     * @inheritdoc
     */
    public function setStoreToBaseRate($storeToBaseRate)
    {
        return $this->setData(self::STORE_TO_BASE_RATE, round($storeToBaseRate, 4));
    }

    /**
     * @inheritdoc
     */
    public function getStoreToOrderRate()
    {
        return $this->getData(self::STORE_TO_ORDER_RATE);
    }

    /**
     * @inheritdoc
     */
    public function setStoreToOrderRate($storeToOrderRate)
    {
        return $this->setData(self::STORE_TO_ORDER_RATE, round($storeToOrderRate, 4));
    }

    /**
     * @inheritdoc
     */
    public function getSubtotal()
    {
        return $this->getData(self::SUBTOTAL);
    }

    /**
     * @inheritdoc
     */
    public function setSubtotal($subtotal)
    {
        return $this->setData(self::SUBTOTAL, round($subtotal, 4));
    }

    /**
     * @inheritdoc
     */
    public function getSubtotalCanceled()
    {
        return $this->getData(self::SUBTOTAL_CANCELED);
    }

    /**
     * @inheritdoc
     */
    public function setSubtotalCanceled($subtotalCanceled)
    {
        return $this->setData(self::SUBTOTAL_CANCELED, round($subtotalCanceled, 4));
    }

    /**
     * @inheritdoc
     */
    public function getSubtotalInvoiced()
    {
        return $this->getData(self::SUBTOTAL_INVOICED);
    }

    /**
     * @inheritdoc
     */
    public function setSubtotalInvoiced($subtotalInvoiced)
    {
        return $this->setData(self::SUBTOTAL_INVOICED, round($subtotalInvoiced, 4));
    }

    /**
     * @inheritdoc
     */
    public function getSubtotalRefunded()
    {
        return $this->getData(self::SUBTOTAL_REFUNDED);
    }

    /**
     * @inheritdoc
     */
    public function setSubtotalRefunded($subtotalRefunded)
    {
        return $this->setData(self::SUBTOTAL_REFUNDED, round($subtotalRefunded, 4));
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
    public function getTotalCanceled()
    {
        return $this->getData(self::TOTAL_CANCELED);
    }

    /**
     * @inheritdoc
     */
    public function setTotalCanceled($totalCanceled)
    {
        return $this->setData(self::TOTAL_CANCELED, round($totalCanceled, 4));
    }

    /**
     * @inheritdoc
     */
    public function getTotalInvoiced()
    {
        return $this->getData(self::TOTAL_INVOICED);
    }

    /**
     * @inheritdoc
     */
    public function setTotalInvoiced($totalInvoiced)
    {
        return $this->setData(self::TOTAL_INVOICED, round($totalInvoiced, 4));
    }

    /**
     * @inheritdoc
     */
    public function getTotalOfflineRefunded()
    {
        return $this->getData(self::TOTAL_OFFLINE_REFUNDED);
    }

    /**
     * @inheritdoc
     */
    public function setTotalOfflineRefunded($totalOfflineRefunded)
    {
        return $this->setData(self::TOTAL_OFFLINE_REFUNDED, round($totalOfflineRefunded, 4));
    }

    /**
     * @inheritdoc
     */
    public function getTotalOnlineRefunded()
    {
        return $this->getData(self::TOTAL_ONLINE_REFUNDED);
    }

    /**
     * @inheritdoc
     */
    public function setTotalOnlineRefunded($totalOnlineRefunded)
    {
        return $this->setData(self::TOTAL_ONLINE_REFUNDED, round($totalOnlineRefunded, 4));
    }

    /**
     * @inheritdoc
     */
    public function getTotalPaid()
    {
        return $this->getData(self::TOTAL_PAID);
    }

    /**
     * @inheritdoc
     */
    public function setTotalPaid($totalPaid)
    {
        return $this->setData(self::TOTAL_PAID, round($totalPaid, 4));
    }

    /**
     * @inheritdoc
     */
    public function getTotalQtyOrdered()
    {
        return $this->getData(self::TOTAL_QTY_ORDERED);
    }

    /**
     * @inheritdoc
     */
    public function setTotalQtyOrdered($totalQtyOrdered)
    {
        return $this->setData(self::TOTAL_QTY_ORDERED, round($totalQtyOrdered, 4));
    }

    /**
     * @inheritdoc
     */
    public function getTotalRefunded()
    {
        return $this->getData(self::TOTAL_REFUNDED);
    }

    /**
     * @inheritdoc
     */
    public function setTotalRefunded($totalRefunded)
    {
        return $this->setData(self::TOTAL_REFUNDED, round($totalRefunded, 4));
    }

    /**
     * @inheritdoc
     */
    public function getCanShipPartially()
    {
        return $this->getData(self::CAN_SHIP_PARTIALLY);
    }

    /**
     * @inheritdoc
     */
    public function setCanShipPartially($canShipPartially)
    {
        return $this->setData(self::CAN_SHIP_PARTIALLY, $canShipPartially);
    }

    /**
     * @inheritdoc
     */
    public function getCanShipPartiallyItem()
    {
        return $this->getData(self::CAN_SHIP_PARTIALLY_ITEM);
    }

    /**
     * @inheritdoc
     */
    public function setCanShipPartiallyItem($canShipPartiallyItem)
    {
        return $this->setData(self::CAN_SHIP_PARTIALLY_ITEM, $canShipPartiallyItem);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerIsGuest()
    {
        return $this->getData(self::CUSTOMER_IS_GUEST);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerIsGuest($customerIsGuest)
    {
        return $this->setData(self::CUSTOMER_IS_GUEST, $customerIsGuest);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerNoteNotify()
    {
        return $this->getData(self::CUSTOMER_NOTE_NOTIFY);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerNoteNotify($customerNoteNotify)
    {
        return $this->setData(self::CUSTOMER_NOTE_NOTIFY, $customerNoteNotify);
    }

    /**
     * @inheritdoc
     */
    public function getBillingAddressId()
    {
        return $this->getData(self::BILLING_ADDRESS_ID);
    }

    /**
     * @inheritdoc
     */
    public function setBillingAddressId($billingAddressId)
    {
        return $this->setData(self::BILLING_ADDRESS_ID, $billingAddressId);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerGroupId()
    {
        return $this->getData(self::CUSTOMER_GROUP_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerGroupId($customerGroupId)
    {
        return $this->setData(self::CUSTOMER_GROUP_ID, $customerGroupId);
    }

    /**
     * @inheritdoc
     */
    public function getEditIncrement()
    {
        return $this->getData(self::EDIT_INCREMENT);
    }

    /**
     * @inheritdoc
     */
    public function setEditIncrement($editIncrement)
    {
        return $this->setData(self::EDIT_INCREMENT, $editIncrement);
    }

    /**
     * @inheritdoc
     */
    public function getEmailSent()
    {
        return $this->getData(self::EMAIL_SENT);
    }

    /**
     * @inheritdoc
     */
    public function setEmailSent($emailSent)
    {
        return $this->setData(self::EMAIL_SENT, $emailSent);
    }

    /**
     * @inheritdoc
     */
    public function getSendEmail()
    {
        return $this->getData(self::SEND_EMAIL);
    }

    /**
     * @inheritdoc
     */
    public function setSendEmail($sendEmail)
    {
        return $this->setData(self::SEND_EMAIL, $sendEmail);
    }

    /**
     * @inheritdoc
     */
    public function getForcedShipmentWithInvoice()
    {
        return $this->getData(self::FORCED_SHIPMENT_WITH_INVOICE);
    }

    /**
     * @inheritdoc
     */
    public function setForcedShipmentWithInvoice($forcedShipmentWithInvoice)
    {
        return $this->setData(self::FORCED_SHIPMENT_WITH_INVOICE, $forcedShipmentWithInvoice);
    }

    /**
     * @inheritdoc
     */
    public function getPaymentAuthExpiration()
    {
        return $this->getData(self::PAYMENT_AUTH_EXPIRATION);
    }

    /**
     * @inheritdoc
     */
    public function setPaymentAuthExpiration($paymentAuthExpiration)
    {
        return $this->setData(self::PAYMENT_AUTH_EXPIRATION, $paymentAuthExpiration);
    }

    /**
     * @inheritdoc
     */
    public function getQuoteAddressId()
    {
        return $this->getData(self::QUOTE_ADDRESS_ID);
    }

    /**
     * @inheritdoc
     */
    public function setQuoteAddressId($quoteAddressId)
    {
        return $this->setData(self::QUOTE_ADDRESS_ID, $quoteAddressId);
    }

    /**
     * @inheritdoc
     */
    public function getQuoteId()
    {
        return $this->getData(self::QUOTE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setQuoteId($quoteId)
    {
        return $this->setData(self::QUOTE_ID, $quoteId);
    }

    /**
     * @inheritdoc
     */
    public function getShippingAddressId()
    {
        return $this->getData(self::SHIPPING_ADDRESS_ID);
    }

    /**
     * @inheritdoc
     */
    public function setShippingAddressId($shippingAddressId)
    {
        return $this->setData(self::SHIPPING_ADDRESS_ID, $shippingAddressId);
    }

    /**
     * @inheritdoc
     */
    public function getAdjustmentNegative()
    {
        return $this->getData(self::ADJUSTMENT_NEGATIVE);
    }

    /**
     * @inheritdoc
     */
    public function setAdjustmentNegative($adjustmentNegative)
    {
        return $this->setData(self::ADJUSTMENT_NEGATIVE, round($adjustmentNegative, 4));
    }

    /**
     * @inheritdoc
     */
    public function getAdjustmentPositive()
    {
        return $this->getData(self::ADJUSTMENT_POSITIVE);
    }

    /**
     * @inheritdoc
     */
    public function setAdjustmentPositive($adjustmentPositive)
    {
        return $this->setData(self::ADJUSTMENT_POSITIVE, round($adjustmentPositive, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseAdjustmentNegative()
    {
        return $this->getData(self::BASE_ADJUSTMENT_NEGATIVE);
    }

    /**
     * @inheritdoc
     */
    public function setBaseAdjustmentNegative($baseAdjustmentNegative)
    {
        return $this->setData(self::BASE_ADJUSTMENT_NEGATIVE, round($baseAdjustmentNegative, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseAdjustmentPositive()
    {
        return $this->getData(self::BASE_ADJUSTMENT_POSITIVE);
    }

    /**
     * @inheritdoc
     */
    public function setBaseAdjustmentPositive($baseAdjustmentPositive)
    {
        return $this->setData(self::BASE_ADJUSTMENT_POSITIVE, round($baseAdjustmentPositive, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseShippingDiscountAmount()
    {
        return $this->getData(self::BASE_SHIPPING_DISCOUNT_AMOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setBaseShippingDiscountAmount($baseShippingDiscountAmount)
    {
        return $this->setData(self::BASE_SHIPPING_DISCOUNT_AMOUNT, round($baseShippingDiscountAmount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseSubtotalInclTax()
    {
        return $this->getData(self::BASE_SUBTOTAL_INCL_TAX);
    }

    /**
     * @inheritdoc
     */
    public function setBaseSubtotalInclTax($baseSubtotalInclTax)
    {
        return $this->setData(self::BASE_SUBTOTAL_INCL_TAX, round($baseSubtotalInclTax, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseTotalDue()
    {
        return $this->getData(self::BASE_TOTAL_DUE);
    }

    /**
     * @inheritdoc
     */
    public function setBaseTotalDue($baseTotalDue)
    {
        return $this->setData(self::BASE_TOTAL_DUE, round($baseTotalDue, 4));
    }

    /**
     * @inheritdoc
     */
    public function getPaymentAuthorizationAmount()
    {
        return $this->getData(self::PAYMENT_AUTHORIZATION_AMOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setPaymentAuthorizationAmount($paymentAuthorizationAmount)
    {
        return $this->setData(self::PAYMENT_AUTHORIZATION_AMOUNT, round($paymentAuthorizationAmount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getShippingDiscountAmount()
    {
        return $this->getData(self::SHIPPING_DISCOUNT_AMOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setShippingDiscountAmount($shippingDiscountAmount)
    {
        return $this->setData(self::SHIPPING_DISCOUNT_AMOUNT, round($shippingDiscountAmount, 4));
    }

    /**
     * @inheritdoc
     */
    public function getSubtotalInclTax()
    {
        return $this->getData(self::SUBTOTAL_INCL_TAX);
    }

    /**
     * @inheritdoc
     */
    public function setSubtotalInclTax($subtotalInclTax)
    {
        return $this->setData(self::SUBTOTAL_INCL_TAX, round($subtotalInclTax, 4));
    }

    /**
     * @inheritdoc
     */
    public function getTotalDue()
    {
        return $this->getData(self::TOTAL_DUE);
    }

    /**
     * @inheritdoc
     */
    public function setTotalDue($totalDue)
    {
        return $this->setData(self::TOTAL_DUE, round($totalDue, 4));
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
    public function getCustomerDob()
    {
        return $this->getData(self::CUSTOMER_DOB);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerDob($customerDob)
    {
        return $this->setData(self::CUSTOMER_DOB, $customerDob);
    }

    /**
     * @inheritdoc
     */
    public function getIncrementId()
    {
        return $this->getData(self::INCREMENT_ID);
    }

    /**
     * @inheritdoc
     */
    public function setIncrementId($incrementId)
    {
        return $this->setData(self::INCREMENT_ID, $incrementId);
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
    public function getBaseCurrencyCode()
    {
        return $this->getData(self::BASE_CURRENCY_CODE);
    }

    /**
     * @inheritdoc
     */
    public function setBaseCurrencyCode($baseCurrencyCode)
    {
        return $this->setData(self::BASE_CURRENCY_CODE, $baseCurrencyCode);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerEmail()
    {
        return $this->getData(self::CUSTOMER_EMAIL);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerEmail($customerEmail)
    {
        return $this->setData(self::CUSTOMER_EMAIL, $customerEmail);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerFirstname()
    {
        return $this->getData(self::CUSTOMER_FIRSTNAME);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerFirstname($customerFirstname)
    {
        return $this->setData(self::CUSTOMER_FIRSTNAME, $customerFirstname);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerLastname()
    {
        return $this->getData(self::CUSTOMER_LASTNAME);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerLastname($customerLastname)
    {
        return $this->setData(self::CUSTOMER_LASTNAME, $customerLastname);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerMiddlename()
    {
        return $this->getData(self::CUSTOMER_MIDDLENAME);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerMiddlename($customerMiddlename)
    {
        return $this->setData(self::CUSTOMER_MIDDLENAME, $customerMiddlename);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerPrefix()
    {
        return $this->getData(self::CUSTOMER_PREFIX);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerPrefix($customerPrefix)
    {
        return $this->setData(self::CUSTOMER_PREFIX, $customerPrefix);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerSuffix()
    {
        return $this->getData(self::CUSTOMER_SUFFIX);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerSuffix($customerSuffix)
    {
        return $this->setData(self::CUSTOMER_SUFFIX, $customerSuffix);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerTaxvat()
    {
        return $this->getData(self::CUSTOMER_TAXVAT);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerTaxvat($customerTaxvat)
    {
        return $this->setData(self::CUSTOMER_TAXVAT, $customerTaxvat);
    }

    /**
     * @inheritdoc
     */
    public function getDiscountDescription()
    {
        return $this->getData(self::DISCOUNT_DESCRIPTION);
    }

    /**
     * @inheritdoc
     */
    public function setDiscountDescription($discountDescription)
    {
        return $this->setData(self::DISCOUNT_DESCRIPTION, $discountDescription);
    }

    /**
     * @inheritdoc
     */
    public function getExtCustomerId()
    {
        return $this->getData(self::EXT_CUSTOMER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setExtCustomerId($extCustomerId)
    {
        return $this->setData(self::EXT_CUSTOMER_ID, $extCustomerId);
    }

    /**
     * @inheritdoc
     */
    public function getExtOrderId()
    {
        return $this->getData(self::EXT_ORDER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setExtOrderId($extOrderId)
    {
        return $this->setData(self::EXT_ORDER_ID, $extOrderId);
    }

    /**
     * @inheritdoc
     */
    public function getGlobalCurrencyCode()
    {
        return $this->getData(self::GLOBAL_CURRENCY_CODE);
    }

    /**
     * @inheritdoc
     */
    public function setGlobalCurrencyCode($globalCurrencyCode)
    {
        return $this->setData(self::GLOBAL_CURRENCY_CODE, $globalCurrencyCode);
    }

    /**
     * @inheritdoc
     */
    public function getHoldBeforeState()
    {
        return $this->getData(self::HOLD_BEFORE_STATE);
    }

    /**
     * @inheritdoc
     */
    public function setHoldBeforeState($holdBeforeState)
    {
        return $this->setData(self::HOLD_BEFORE_STATE, $holdBeforeState);
    }

    /**
     * @inheritdoc
     */
    public function getHoldBeforeStatus()
    {
        return $this->getData(self::HOLD_BEFORE_STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setHoldBeforeStatus($holdBeforeStatus)
    {
        return $this->setData(self::HOLD_BEFORE_STATUS, $holdBeforeStatus);
    }

    /**
     * @inheritdoc
     */
    public function getOrderCurrencyCode()
    {
        return $this->getData(self::ORDER_CURRENCY_CODE);
    }

    /**
     * @inheritdoc
     */
    public function setOrderCurrencyCode($orderCurrencyCode)
    {
        return $this->setData(self::ORDER_CURRENCY_CODE, $orderCurrencyCode);
    }

    /**
     * @inheritdoc
     */
    public function getOriginalIncrementId()
    {
        return $this->getData(self::ORIGINAL_INCREMENT_ID);
    }

    /**
     * @inheritdoc
     */
    public function setOriginalIncrementId($originalIncrementId)
    {
        return $this->setData(self::ORIGINAL_INCREMENT_ID, $originalIncrementId);
    }

    /**
     * @inheritdoc
     */
    public function getRelationChildId()
    {
        return $this->getData(self::RELATION_CHILD_ID);
    }

    /**
     * @inheritdoc
     */
    public function setRelationChildId($relationChildId)
    {
        return $this->setData(self::RELATION_CHILD_ID, $relationChildId);
    }

    /**
     * @inheritdoc
     */
    public function getRelationChildRealId()
    {
        return $this->getData(self::RELATION_CHILD_REAL_ID);
    }

    /**
     * @inheritdoc
     */
    public function setRelationChildRealId($relationChildRealId)
    {
        return $this->setData(self::RELATION_CHILD_REAL_ID, $relationChildRealId);
    }

    /**
     * @inheritdoc
     */
    public function getRelationParentId()
    {
        return $this->getData(self::RELATION_PARENT_ID);
    }

    /**
     * @inheritdoc
     */
    public function setRelationParentId($relationParentId)
    {
        return $this->setData(self::RELATION_PARENT_ID, $relationParentId);
    }

    /**
     * @inheritdoc
     */
    public function getRelationParentRealId()
    {
        return $this->getData(self::RELATION_PARENT_REAL_ID);
    }

    /**
     * @inheritdoc
     */
    public function setRelationParentRealId($relationParentRealId)
    {
        return $this->setData(self::RELATION_PARENT_REAL_ID, $relationParentRealId);
    }

    /**
     * @inheritdoc
     */
    public function getRemoteIp()
    {
        return $this->getData(self::REMOTE_IP);
    }

    /**
     * @inheritdoc
     */
    public function setRemoteIp($remoteIp)
    {
        return $this->setData(self::REMOTE_IP, $remoteIp);
    }

    /**
     * @inheritdoc
     */
    public function getShippingMethod()
    {
        return $this->getData(self::SHIPPING_METHOD);
    }

    /**
     * @inheritdoc
     */
    public function setShippingMethod($shippingMethod)
    {
        return $this->setData(self::SHIPPING_METHOD, $shippingMethod);
    }

    /**
     * @inheritdoc
     */
    public function getStoreCurrencyCode()
    {
        return $this->getData(self::STORE_CURRENCY_CODE);
    }

    /**
     * @inheritdoc
     */
    public function setStoreCurrencyCode($storeCurrencyCode)
    {
        return $this->setData(self::STORE_CURRENCY_CODE, $storeCurrencyCode);
    }

    /**
     * @inheritdoc
     */
    public function getStoreName()
    {
        return $this->getData(self::STORE_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setStoreName($storeName)
    {
        return $this->setData(self::STORE_NAME, $storeName);
    }

    /**
     * @inheritdoc
     */
    public function getXForwardedFor()
    {
        return $this->getData(self::X_FORWARDED_FOR);
    }

    /**
     * @inheritdoc
     */
    public function setXForwardedFor($xForwardedFor)
    {
        return $this->setData(self::X_FORWARDED_FOR, $xForwardedFor);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerNote()
    {
        return $this->getData(self::CUSTOMER_NOTE);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerNote($customerNote)
    {
        return $this->setData(self::CUSTOMER_NOTE, $customerNote);
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
    public function getTotalItemCount()
    {
        return $this->getData(self::TOTAL_ITEM_COUNT);
    }

    /**
     * @inheritdoc
     */
    public function setTotalItemCount($totalItemCount)
    {
        return $this->setData(self::TOTAL_ITEM_COUNT, $totalItemCount);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerGender()
    {
        return $this->getData(self::CUSTOMER_GENDER);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerGender($customerGender)
    {
        return $this->setData(self::CUSTOMER_GENDER, $customerGender);
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
        return $this->setData(
            self::BASE_DISCOUNT_TAX_COMPENSATION_AMOUNT,
            round($baseDiscountTaxCompensationAmount, 4)
        );
    }

    /**
     * @inheritdoc
     */
    public function getShippingDiscountTaxCompensationAmount()
    {
        return $this->getData(self::SHIPPING_DISCOUNT_TAX_COMPENSATION_AMOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setShippingDiscountTaxCompensationAmount($shippingDiscountTaxCompensationAmount)
    {
        return $this->setData(
            self::SHIPPING_DISCOUNT_TAX_COMPENSATION_AMOUNT,
            round($shippingDiscountTaxCompensationAmount, 4)
        );
    }

    /**
     * @inheritdoc
     */
    public function getBaseShippingDiscountTaxCompensationAmnt()
    {
        return $this->getData(self::BASE_SHIPPING_DISCOUNT_TAX_COMPENSATION_AMNT);
    }

    /**
     * @inheritdoc
     */
    public function setBaseShippingDiscountTaxCompensationAmnt($baseShippingDiscountTaxCompensationAmnt)
    {
        return $this->setData(
            self::BASE_SHIPPING_DISCOUNT_TAX_COMPENSATION_AMNT,
            round($baseShippingDiscountTaxCompensationAmnt, 4)
        );
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
        return $this->setData(
            self::DISCOUNT_TAX_COMPENSATION_INVOICED,
            round($discountTaxCompensationInvoiced, 4)
        );
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
        return $this->setData(
            self::BASE_DISCOUNT_TAX_COMPENSATION_INVOICED,
            round($baseDiscountTaxCompensationInvoiced, 4)
        );
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
        return $this->setData(
            self::DISCOUNT_TAX_COMPENSATION_REFUNDED,
            round($discountTaxCompensationRefunded, 4)
        );
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
        return $this->setData(
            self::BASE_DISCOUNT_TAX_COMPENSATION_REFUNDED,
            round($baseDiscountTaxCompensationRefunded, 4)
        );
    }

    /**
     * @inheritdoc
     */
    public function getShippingInclTax()
    {
        return $this->getData(self::SHIPPING_INCL_TAX);
    }

    /**
     * @inheritdoc
     */
    public function setShippingInclTax($shippingInclTax)
    {
        return $this->setData(self::SHIPPING_INCL_TAX, round($shippingInclTax, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseShippingInclTax()
    {
        return $this->getData(self::BASE_SHIPPING_INCL_TAX);
    }

    /**
     * @inheritdoc
     */
    public function setBaseShippingInclTax($baseShippingInclTax)
    {
        return $this->setData(self::BASE_SHIPPING_INCL_TAX, round($baseShippingInclTax, 4));
    }

    /**
     * @inheritdoc
     */
    public function getCouponRuleName()
    {
        return $this->getData(self::COUPON_RULE_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setCouponRuleName($couponRuleName)
    {
        return $this->setData(self::COUPON_RULE_NAME, $couponRuleName);
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
    public function getPaypalIpnCustomerNotified()
    {
        return $this->getData(self::PAYPAL_IPN_CUSTOMER_NOTIFIED);
    }

    /**
     * @inheritdoc
     */
    public function setPaypalIpnCustomerNotified($paypalIpnCustomerNotified)
    {
        return $this->setData(self::PAYPAL_IPN_CUSTOMER_NOTIFIED, $paypalIpnCustomerNotified);
    }

    /**
     * @inheritdoc
     */
    public function getPosId()
    {
        return $this->getData(self::POS_ID);
    }

    /**
     * @inheritdoc
     */
    public function setPosId($posId)
    {
        return $this->setData(self::POS_ID, $posId);
    }

    /**
     * @inheritdoc
     */
    public function getPosLocationId()
    {
        return $this->getData(self::POS_LOCATION_ID);
    }

    /**
     * @inheritdoc
     */
    public function setPosLocationId($posLocationId)
    {
        return $this->setData(self::POS_LOCATION_ID, $posLocationId);
    }

    /**
     * @inheritdoc
     */
    public function getItems()
    {
        if ($this->getData(OrderInterface::ITEMS)) {
            return $this->getData(OrderInterface::ITEMS);
        } else {
            return $this->getItemsCollection($this->getEntityId());
        }
    }

    /**
     * @inheritdoc
     */
    public function setItems($items)
    {
        return $this->setData(self::ITEMS, $items);
    }

    /**
     * @inheritdoc
     */
    public function getAllItems()
    {
        return $this->getItems();
    }

    /**
     * @inheritdoc
     */
    public function getAddresses()
    {
//        if ($this->getData(self::ADDRESSES)) {
//            return $this->getData(self::ADDRESSES);
//        } else {
//            return $this->getAddressesCollection($this->getEntityId());
//        }

        if ($this->getData(OrderInterface::ADDRESSES) == null) {
            $this->setData(
                OrderInterface::ADDRESSES,
                $this->getAddressesCollection()->getItems()
            );
        }
        return $this->getData(self::ADDRESSES);
    }

    /**
     * Get Addresses Collection
     *
     * @return Collection
     */
    public function getAddressesCollection()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $addressCollection = $objectManager->create(
            \Magestore\Webpos\Model\ResourceModel\Sales\Order\Address\Collection::class
        );
        $collection = $addressCollection->addFieldToFilter('parent_id', $this->getEntityId());
        return $collection;
    }

    /**
     * @inheritdoc
     */
    public function setAddresses($addresses)
    {
        return $this->setData(self::ADDRESSES, $addresses);
    }

    /**
     * @inheritdoc
     */
    public function getPayments()
    {
        if ($this->getData(self::PAYMENT)) {
            return $this->getData(self::PAYMENT);
        } else {
            return $this->getPaymentsByOrderId($this->getEntityId());
        }
    }

    /**
     * @inheritdoc
     */
    public function setPayments($payment)
    {
        return $this->setData(self::PAYMENT, $payment);
    }

    /**
     * @inheritdoc
     */
    public function getPosDeliveryDate()
    {
        return $this->getData(self::POS_DELIVERY_DATE);
    }

    /**
     * @inheritdoc
     */
    public function setPosDeliveryDate($posDeliveryDate)
    {
        return $this->setData(self::POS_DELIVERY_DATE, $posDeliveryDate);
    }

    /**
     * @inheritdoc
     */
    public function getPosStaffId()
    {
        return $this->getData(self::POS_STAFF_ID);
    }

    /**
     * @inheritdoc
     */
    public function setPosStaffId($posStaffId)
    {
        return $this->setData(self::POS_STAFF_ID, $posStaffId);
    }

    /**
     * @inheritdoc
     */
    public function getPosStaffName()
    {
        return $this->getData(self::POS_STAFF_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setPosStaffName($posStaffName)
    {
        return $this->setData(self::POS_STAFF_NAME, $posStaffName);
    }

    /**
     * @inheritdoc
     */
    public function getPosFulfillOnline()
    {
        return $this->getData(self::POS_FULFILL_ONLINE);
    }

    /**
     * @inheritdoc
     */
    public function setPosFulfillOnline($posFulfillOnline)
    {
        return $this->setData(self::POS_FULFILL_ONLINE, $posFulfillOnline);
    }

    /**
     * @inheritdoc
     */
    public function getSearchString()
    {
        if ($this->getData(self::SEARCH_STRING) == null) {
            $searchString = [];
            $searchString = $this->addOrderAttributesToSearchString($searchString);
            $searchString = $this->addProductAttributesToSearchString($searchString);
            $searchString = $this->addCustomerAttributesToSearchString($searchString);
            $searchString = $this->addPaymentsInfoToSearchString($searchString);
            return implode(',', $searchString);
        }
        return $this->getData(self::SEARCH_STRING);
    }

    /**
     * Add order attributes to search string
     *
     * @param array $searchString
     * @return array
     */
    private function addOrderAttributesToSearchString($searchString = [])
    {
        $attributesToSearch = self::SEARCH_ATTRIBUTES;
        foreach ($attributesToSearch as $attribute) {
            if ($this->getData($attribute) && !is_array($this->getData($attribute))) {
                $searchString[] = $this->getData($attribute);
            }
        }
        return $searchString;
    }

    /**
     * Add product attributes to search string
     *
     * @param array $searchString
     * @return array
     */
    private function addProductAttributesToSearchString($searchString = [])
    {
        $items = $this->getItems();

        $productAttributesToSearch = self::SEARCH_BY_PRODUCT_ATTRIBUTES;
        foreach ($items as $item) {
            foreach ($productAttributesToSearch as $attribute) {
                if ($item->getData($attribute) &&
                    !in_array($item->getData($attribute), $searchString)
                ) {
                    $searchString[] = $item->getData($attribute);
                }
            }
        }
        return $searchString;
    }

    /**
     * Add customer attributes to search string
     *
     * @param array $searchString
     * @return array
     */
    private function addCustomerAttributesToSearchString($searchString = [])
    {
        $addresses = $this->getAddresses();
        $customerAttributesToSearch = self::SEARCH_BY_CUSTOMER_ATTRIBUTES;
        foreach ($addresses as $address) {
            foreach ($customerAttributesToSearch as $attribute) {
                if ($address->getData($attribute) &&
                    !in_array($address->getData($attribute), $searchString)
                ) {
                    $searchString[] = $address->getData($attribute);
                }
            }
        }

        //todo: it is a hard code, need dynamic
        foreach ($addresses as $address) {
            $firstName = $address->getData('firstname');
            $lastName = $address->getData('lastname');
            $combination = [
                "$firstName $lastName",
                "$lastName $firstName"
            ];

            foreach ($combination as $string) {
                if (in_array(trim($string), $searchString)) {
                    continue;
                }
                $searchString[] = $string;
            }
        }
        return $searchString;
    }

    /**
     * Add product info to search string
     *
     * @param array $searchString
     * @return array
     */
    private function addPaymentsInfoToSearchString($searchString = [])
    {
        $payments = $this->getPayments() ?: [];

        foreach ($payments as $payment) {
            if (!$payment->getReferenceNumber()) {
                continue;
            }

            $searchString[] = $payment->getReferenceNumber();
        }
        return $searchString;
    }

    /**
     * @inheritdoc
     */
    public function setSearchString($searchString)
    {
        return $this->setData(self::SEARCH_STRING, $searchString);
    }

    /**
     * @inheritdoc
     */
    public function getStatusHistories()
    {
        if ($this->getData(OrderInterface::STATUS_HISTORIES) == null) {
            $this->setData(
                OrderInterface::STATUS_HISTORIES,
                array_merge(
                    $this->getCreditmemoHistoryCollection()->getItems(),
                    $this->getStatusHistoryCollection()->getItems()
                )
            );
        }
        return $this->getData(self::STATUS_HISTORIES);
    }

    /**
     * @inheritdoc
     */
    public function setStatusHistories($statusHistories)
    {
        return $this->setData(self::STATUS_HISTORIES, $statusHistories);
    }

    /**
     * @inheritdoc
     */
    public function getPosChange()
    {
        return $this->getData(self::POS_CHANGE);
    }

    /**
     * @inheritdoc
     */
    public function setPosChange($posChange)
    {
        return $this->setData(self::POS_CHANGE, $posChange);
    }

    /**
     * @inheritdoc
     */
    public function getBasePosChange()
    {
        return $this->getData(self::BASE_POS_CHANGE);
    }

    /**
     * @inheritdoc
     */
    public function setBasePosChange($basePosChange)
    {
        return $this->setData(self::BASE_POS_CHANGE, $basePosChange);
    }

    /**
     * @inheritdoc
     */
    public function getCodesDiscount()
    {
        return $this->getData(self::CODES_DISCOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setCodesDiscount($codesDiscount)
    {
        return $this->setData(self::CODES_DISCOUNT, $codesDiscount);
    }

    /**
     * @inheritdoc
     */
    public function getCodesBaseDiscount()
    {
        return $this->getData(self::CODES_BASE_DISCOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setCodesBaseDiscount($codesBaseDiscount)
    {
        return $this->setData(self::CODES_BASE_DISCOUNT, $codesBaseDiscount);
    }

    /**
     * @inheritdoc
     */
    public function getGiftVoucherGiftCodesDiscount()
    {
        return $this->getData(self::GIFT_VOUCHER_GIFT_CODES_DISCOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setGiftVoucherGiftCodesDiscount($giftVoucherGiftCodesDiscount)
    {
        return $this->setData(self::GIFT_VOUCHER_GIFT_CODES_DISCOUNT, $giftVoucherGiftCodesDiscount);
    }

    /**
     * @inheritdoc
     */
    public function getGiftVoucherGiftCodes()
    {
        return $this->getData(self::GIFT_VOUCHER_GIFT_CODES);
    }

    /**
     * @inheritdoc
     */
    public function setGiftVoucherGiftCodes($giftVoucherGiftCodes)
    {
        return $this->setData(self::GIFT_VOUCHER_GIFT_CODES, $giftVoucherGiftCodes);
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
    public function getGiftvoucherDiscountForShipping()
    {
        return $this->getData(self::GIFTVOUCHER_DISCOUNT_FOR_SHIPPING);
    }

    /**
     * @inheritdoc
     */
    public function setGiftvoucherDiscountForShipping($giftvoucherDiscountForShipping)
    {
        return $this->setData(self::GIFTVOUCHER_DISCOUNT_FOR_SHIPPING, $giftvoucherDiscountForShipping);
    }

    /**
     * @inheritdoc
     */
    public function getBaseGiftvoucherDiscountForShipping()
    {
        return $this->getData(self::BASE_GIFTVOUCHER_DISCOUNT_FOR_SHIPPING);
    }

    /**
     * @inheritdoc
     */
    public function setBaseGiftvoucherDiscountForShipping($baseGiftvoucherDiscountForShipping)
    {
        return $this->setData(self::BASE_GIFTVOUCHER_DISCOUNT_FOR_SHIPPING, $baseGiftvoucherDiscountForShipping);
    }

    /**
     * @inheritdoc
     */
    public function getPosPreTotalPaid()
    {
        return $this->getData(self::POS_PRE_TOTAL_PAID);
    }

    /**
     * @inheritdoc
     */
    public function setPosPreTotalPaid($posPreTotalPaid)
    {
        return $this->setData(self::POS_PRE_TOTAL_PAID, round($posPreTotalPaid, 4));
    }

    /**
     * @inheritdoc
     */
    public function getPosBasePreTotalPaid()
    {
        return $this->getData(self::POS_BASE_PRE_TOTAL_PAID);
    }

    /**
     * @inheritdoc
     */
    public function setPosBasePreTotalPaid($posBasePreTotalPaid)
    {
        return $this->setData(self::POS_BASE_PRE_TOTAL_PAID, round($posBasePreTotalPaid, 4));
    }

    /**
     * @inheritdoc
     */
    public function getAppliedTaxes()
    {
        if (!$this->getData(self::APPLIED_TAXES)) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            /** @var \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\Collection $appliedTaxCollection */
            $appliedTaxCollection = $objectManager->create(
                \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\Collection::class
            );
            $appliedTaxCollection->loadByOrder($this);

            $dataTaxes = [];
            /** @var \Magento\Tax\Api\Data\OrderTaxDetailsAppliedTaxInterface $item */
            foreach ($appliedTaxCollection->getItems() as $item) {
                $dataTaxes[$item->getTitle()] = $item;
            }
            $this->setAppliedTaxes($dataTaxes);
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

    /**
     * Get Gift Card Gift Codes Available Refund
     *
     * @return string|null
     */
    public function getGiftVoucherGiftCodesAvailableRefund()
    {
        $result = null;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Framework\Module\Manager $moduleManager */
        $moduleManager = $objectManager->create(\Magento\Framework\Module\Manager::class);
        if (!$moduleManager->isEnabled('Magestore_Giftvoucher')) {
            return $result;
        }
        $giftcodes = $this->getGiftVoucherGiftCodes();
        if ($giftcodes) {
            $giftcodes = explode(',', $giftcodes);
            if (!empty($giftcodes)) {
                $result = [];
                $history = $objectManager->create(\Magestore\Giftvoucher\Model\History::class);
                foreach ($giftcodes as $giftcode) {
                    $giftvoucher = $objectManager->create(\Magestore\Giftvoucher\Model\Giftvoucher::class)
                        ->loadByCode($giftcode);
                    $totalSpent = $history->getTotalSpent($giftvoucher, $this);
                    $totalRefund = $history->getTotalRefund($giftvoucher, $this);
                    $available = $totalSpent - $totalRefund;
                    $result[] = $available;
                }
                $result = implode(',', $result);
            }
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getMagestoreBaseDiscountForShipping()
    {
        return $this->getData(self::MAGESTORE_BASE_DISCOUNT_FOR_SHIPPING);
    }

    /**
     * @inheritdoc
     */
    public function setMagestoreBaseDiscountForShipping($magestoreBaseDiscountForShipping)
    {
        return $this->setData(self::MAGESTORE_BASE_DISCOUNT_FOR_SHIPPING, $magestoreBaseDiscountForShipping);
    }

    /**
     * @inheritdoc
     */
    public function getMagestoreDiscountForShipping()
    {
        return $this->getData(self::MAGESTORE_DISCOUNT_FOR_SHIPPING);
    }

    /**
     * @inheritdoc
     */
    public function setMagestoreDiscountForShipping($magestoreDiscountForShipping)
    {
        return $this->setData(self::MAGESTORE_DISCOUNT_FOR_SHIPPING, $magestoreDiscountForShipping);
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
    public function getWarehouseId()
    {
        return $this->getData(self::WAREHOUSE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setWarehouseId($warehouseId)
    {
        return $this->setData(self::WAREHOUSE_ID, $warehouseId);
    }

    /**
     * @inheritdoc
     */
    public function getOsPosCustomDiscountReason()
    {
        return $this->getData(self::OS_POS_CUSTOM_DISCOUNT_REASON);
    }

    /**
     * @inheritdoc
     */
    public function setOsPosCustomDiscountReason($osPosCustomDiscountReason)
    {
        return $this->setData(self::OS_POS_CUSTOM_DISCOUNT_REASON, $osPosCustomDiscountReason);
    }

    /**
     * @inheritdoc
     */
    public function getOsPosCustomDiscountType()
    {
        return $this->getData(self::OS_POS_CUSTOM_DISCOUNT_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setOsPosCustomDiscountType($osPosCustomDiscountType)
    {
        return $this->setData(self::OS_POS_CUSTOM_DISCOUNT_TYPE, $osPosCustomDiscountType);
    }

    /**
     * @inheritdoc
     */
    public function getOsPosCustomDiscountAmount()
    {
        return $this->getData(self::OS_POS_CUSTOM_DISCOUNT_AMOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setOsPosCustomDiscountAmount($osPosCustomDiscountAmount)
    {
        return $this->setData(self::OS_POS_CUSTOM_DISCOUNT_AMOUNT, $osPosCustomDiscountAmount);
    }

    /**
     * @inheritdoc
     */
    public function getGiftcodesAppliedDiscountForShipping()
    {
        return $this->getData(self::GIFTCODES_APPLIED_DISCOUNT_FOR_SHIPPING);
    }

    /**
     * @inheritdoc
     */
    public function setGiftcodesAppliedDiscountForShipping($giftcodesAppliedDiscountForShipping)
    {
        return $this->setData(self::GIFTCODES_APPLIED_DISCOUNT_FOR_SHIPPING, $giftcodesAppliedDiscountForShipping);
    }

    /**
     * Return collection of order status history items.
     *
     * @return \Magento\Sales\Model\ResourceModel\Order\Status\History\Collection
     */
    public function getStatusHistoryCollection()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $collection = $objectManager
            ->create(\Magento\Sales\Model\ResourceModel\Order\Status\History\Collection::class)
            ->addFieldTofilter('parent_id', $this->getEntityId())
            ->setOrder('created_at', 'desc');
        return $collection;
    }

    /**
     * Return collection of order status history items.
     *
     * @return \Magento\Sales\Model\ResourceModel\Order\Creditmemo\Comment\Collectio
     */
    public function getCreditmemoHistoryCollection()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $collection = $objectManager->create(
            \Magento\Sales\Model\ResourceModel\Order\Creditmemo\Comment\Collection::class
        );
        $collection->getSelect()
            ->joinLeft(
                ['creditmemo' => $collection->getTable('sales_creditmemo')],
                'main_table.parent_id = creditmemo.entity_id',
                ['order_id' => 'creditmemo.order_id']
            );
        $collection->addFieldToFilter('creditmemo.order_id', $this->getEntityId());
        $collection->getSelect()->order('created_at DESC');
        return $collection;
    }

    /**
     * Get payments by order ID
     *
     * @param int $orderId
     * @return array|null
     */
    public function getPaymentsByOrderId($orderId)
    {
        $payments = [];
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magestore\Webpos\Model\ResourceModel\Sales\Order\Payment\Collection $orderPayments */
        $orderPayments = $objectManager
            ->create(\Magestore\Webpos\Model\ResourceModel\Sales\Order\Payment\Collection::class)
            ->addFieldToFilter('order_id', $orderId);

        /** @var PaymentInterface $orderPayment */
        foreach ($orderPayments as $orderPayment) {
            $orderPayment->setIsPaid(1);
            $payments[] = $orderPayment;
        }

        $webposOrderPaymentCount = count($payments);

        if ($webposOrderPaymentCount) {
            $zipOnlineMagentoOrderPayments = $objectManager
                ->create(\Magento\Sales\Model\ResourceModel\Order\Payment\Collection::class)
                ->addFieldToFilter('parent_id', $orderId)
                ->addFieldToFilter('method', ['eq' => 'zipmoneypayment']);
            /** @var PaymentInterface $orderPayment */
            foreach ($zipOnlineMagentoOrderPayments as $zipOnlineMagentoOrderPayment) {
                $payments[] = $this->convertMagentoOrderPaymentToWebposOrderPayment($zipOnlineMagentoOrderPayment);
            }
            return $payments;
        }

        $magentoOrderPayments = $objectManager
            ->create(\Magento\Sales\Model\ResourceModel\Order\Payment\Collection::class)
            ->addFieldToFilter('parent_id', $orderId)
            ->addFieldToFilter('method', ['neq' => 'multipaymentforpos']);
        /** @var PaymentInterface $orderPayment */
        foreach ($magentoOrderPayments as $orderPayment) {
            $payments[] = $this->convertMagentoOrderPaymentToWebposOrderPayment($orderPayment);
        }
        return count($payments) ? $payments : null;
    }

    /**
     * Convert magento order payment to webpos order payment
     *
     * @param PaymentInterface $orderPayment
     * @return mixed
     */
    private function convertMagentoOrderPaymentToWebposOrderPayment($orderPayment)
    {
        $orderPayment->setOrderId($orderPayment->getOrder()->getId());
        $orderPayment->setPaymentId($orderPayment->getId());
        $orderPayment->setAmountPaid($orderPayment->getData('amount_paid'));
        $orderPayment->setBaseAmountPaid($orderPayment->getData('base_amount_paid'));
        $orderPayment->setIsPaid(1);
        $orderPayment->setType(PaymentInterface::TYPE_CHECKOUT);
        $orderPayment->setIsPayLater(0);
        $orderPayment->setReferenceNumber($orderPayment->getData('last_trans_id') ?:'');

        $additionalInformation = $orderPayment->getData('additional_information');
        if (is_array($additionalInformation)
            && !empty($additionalInformation['method_title'])
        ) {
            $orderPayment->setTitle($additionalInformation['method_title']);
        }

        // modify data via event
        $params = ['object' => $this, 'payment' => $orderPayment];
        $this->_eventManager->dispatch(self::EVENT_WEBPOS_GET_ORDER_PAYMENT_AFTER, $params);
        return $params['payment'];
    }

    /**
     * Get items collection
     *
     * @param int $orderId
     * @param array $filterByTypes
     * @param bool $nonChildrenOnly
     * @return \Magestore\Webpos\Model\Checkout\Order\Item[]
     */
    public function getItemsCollection($orderId, $filterByTypes = [], $nonChildrenOnly = false)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $orderItemCollection = $objectManager->create(
            \Magestore\Webpos\Model\ResourceModel\Sales\Order\Item\Collection::class
        );
        $collection = $orderItemCollection->addFieldToFilter('order_id', $orderId);
        if ($filterByTypes) {
            $collection->filterByTypes($filterByTypes);
        }
        if ($nonChildrenOnly) {
            $collection->filterByParent();
        }

        $items = [];
        if ($this->getEntityId()) {
            foreach ($collection as $item) {
                $items[] = $item;
            }
        }
        return $items;
    }

    /**
     * Get all visible items
     *
     * @return array
     */
    public function getAllVisibleItems()
    {
        $items = [];
        foreach ($this->getItems() as $item) {
            if (!$item->isDeleted() && !$item->getParentItemId()) {
                $items[] = $item;
            }
        }
        return $items;
    }

    /**
     * Retrieve store model instance
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        $storeId = $this->getStoreId();
        $storeManager = ObjectManager::getInstance()->get(\Magento\Store\Model\StoreManagerInterface::class);
        if ($storeId) {
            return $storeManager->getStore($storeId);
        }
        return $storeManager->getStore();
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\OrderExtensionInterface|null
     * @since 102.0.0
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\OrderExtensionInterface $extensionAttributes
     * @return $this
     * @since 102.0.0
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Checkout\OrderExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }

    /**
     * @inheritdoc
     */
    public function getTmpCustomerId()
    {
        return $this->getData(self::TMP_CUSTOMER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setTmpCustomerId($tmpCustomerId)
    {
        return $this->setData(self::TMP_CUSTOMER_ID, $tmpCustomerId);
    }
}
