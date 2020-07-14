<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Invoice;

use \Magestore\PurchaseOrderSuccess\Api\Data\RefundInterface;

/**
 * Class Refund
 * @package Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Invoice
 */
class Refund extends \Magento\Framework\Model\AbstractModel
    implements RefundInterface
{

    public function __construct(
        \Magestore\PurchaseOrderSuccess\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Invoice\Refund');
    }

    /**
     * Get purchase order invoice refund id
     *
     * @return int
     */
    public function getPurchaseOrderInvoiceRefundId(){
        return $this->_getData(self::PURCHASE_ORDER_INVOICE_REFUND_ID);
    }

    /**
     * Set purchase order invoice refund id
     *
     * @param int $purchaseOrderInvoiceRefundId
     * @return $this
     */
    public function setPurchaseOrderInvoiceRefundId($purchaseOrderInvoiceRefundId){
        return $this->setData(self::PURCHASE_ORDER_INVOICE_REFUND_ID, $purchaseOrderInvoiceRefundId);
    }

    /**
     * Get purchase order invoice id
     *
     * @return int
     */
    public function getPurchaseOrderInvoiceId(){
        return $this->_getData(self::PURCHASE_ORDER_INVOICE_ID);
    }

    /**
     * Set purchase order invoice id
     *
     * @param int $purchaseOrderInvoiceId
     * @return $this
     */
    public function setPurchaseOrderInvoiceId($purchaseOrderInvoiceId){
        return $this->setData(self::PURCHASE_ORDER_INVOICE_ID, $purchaseOrderInvoiceId);
    }

    /**
     * Get refund amount
     *
     * @return float
     */
    public function getRefundAmount(){
        return $this->_getData(self::REFUND_AMOUNT);
    }

    /**
     * Set refund amount
     *
     * @param float $refundAmount
     * @return $this
     */
    public function setRefundAmount($refundAmount){
        return $this->setData(self::REFUND_AMOUNT, $refundAmount);
    }

    /**
     * Get reason
     *
     * @return string
     */
    public function getReason(){
        return $this->_getData(self::REASON);
    }

    /**
     * Set reason
     *
     * @param string $reason
     * @return $this
     */
    public function setReason($reason){
        return $this->setData(self::REASON, $reason);
    }

    /**
     * Get refund at
     *
     * @return string
     */
    public function getRefundAt(){
        return $this->_getData(self::REFUND_AT);
    }

    /**
     * Set refund at
     *
     * @param string $refundAt
     * @return $this
     */
    public function setRefundAt($refundAt){
        return $this->setData(self::REFUND_AT, $refundAt);
    }

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt(){
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * Set created at
     *
     * @param string|null $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt){
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}