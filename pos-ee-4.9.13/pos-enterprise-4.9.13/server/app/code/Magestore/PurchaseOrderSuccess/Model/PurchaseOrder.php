<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model;

use Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface;
use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Type as PurchaseOrderType;
use Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\PurchaseOrderService;

class PurchaseOrder extends \Magento\Framework\Model\AbstractModel
    implements PurchaseOrderInterface
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'purchaseordersuccess_purchaseorder';

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\Config\ShippingMethod
     */
    protected $shippingMethodService;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\Config\TaxAndShipping
     */
    protected $taxAndShippingService;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\Config\PaymentTerm
     */
    protected $paymentTermService;

    /**
     * @var PurchaseOrderService
     */
    protected $purchaseOrderService;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item\ItemService
     */
    protected $purchaseItemService;

    /**
     * @var \Magento\Backend\Model\Auth
     */
    protected $_auth;

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'purchaseorder';
    
    public function __construct(
        \Magestore\PurchaseOrderSuccess\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\PurchaseOrderSuccess\Service\Config\ShippingMethod $shippingMethodService,
        \Magestore\PurchaseOrderSuccess\Service\Config\TaxAndShipping $taxAndShippingService,
        \Magestore\PurchaseOrderSuccess\Service\Config\PaymentTerm $paymentTermService,
        \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\PurchaseOrderService $purchaseOrderService,
        \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item\ItemService $purchaseItemService,
        \Magento\Backend\Model\Auth $auth,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ){
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->shippingMethodService = $shippingMethodService;
        $this->taxAndShippingService = $taxAndShippingService;
        $this->paymentTermService = $paymentTermService;
        $this->purchaseOrderService = $purchaseOrderService;
        $this->purchaseItemService = $purchaseItemService;
        $this->_auth = $auth;
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder');
    }

    /**
     * Get purchase order id
     *
     * @return int
     */
    public function getPurchaseOrderId(){
        return $this->_getData(self::PURCHASE_ORDER_ID);
    }

    /**
     * Set purchase order id
     *
     * @param int $purchaseOrderId
     * @return $this
     */
    public function setPurchaseOrderId($purchaseOrderId){
        return $this->setData(self::PURCHASE_ORDER_ID, $purchaseOrderId);
    }

    /**
     * Get purchase code
     *
     * @return string|null
     */
    public function getPurchaseCode(){
        return $this->_getData(self::PURCHASE_CODE);
    }

    /**
     * Set purchase code
     *
     * @param string $purchaseCode
     * @return $this
     */
    public function setPurchaseCode($purchaseCode){
        return $this->setData(self::PURCHASE_CODE, $purchaseCode);
    }

    /**
     * Get supplier id
     *
     * @return int
     */
    public function getSupplierId(){
        return $this->_getData(self::SUPPLIER_ID);
    }

    /**
     * Set supplier id
     *
     * @param int $supplierId
     * @return $this
     */
    public function setSupplierId($supplierId){
        return $this->setData(self::SUPPLIER_ID, $supplierId);
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType(){
        return $this->_getData(self::TYPE);
    }

    /**
     * Set type
     *
     * @param int $type
     * @return $this
     */
    public function setType($type){
        return $this->setData(self::TYPE, $type);
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus(){
        return $this->_getData(self::STATUS);
    }

    /**
     * Set status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status){
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get send email
     *
     * @return int
     */
    public function getSendEmail(){
        return $this->_getData(self::SEND_EMAIL);
    }

    /**
     * Set send email
     *
     * @param int $sendEmail
     * @return $this
     */
    public function setSendEmail($sendEmail){
        return $this->setData(self::SEND_EMAIL, $sendEmail);
    }

    /**
     * Get is sent email
     *
     * @return boolean
     */
    public function getIsSent(){
        return $this->_getData(self::IS_SENT);
    }

    /**
     * Set is sent email
     *
     * @param int $isSent
     * @return $this
     */
    public function setIsSent($isSent){
        return $this->setData(self::IS_SENT, $isSent);
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment(){
        return $this->_getData(self::COMMENT);
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return $this
     */
    public function setComment($comment){
        return $this->setData(self::COMMENT, $comment);
    }

    /**
     * Get shipping address
     *
     * @return string
     */
    public function getShippingAddress(){
        return $this->_getData(self::SHIPPING_ADDRESS);
    }

    /**
     * Set shipping address
     *
     * @param string $shippingAddress
     * @return $this
     */
    public function setShippingAddress($shippingAddress){
        return $this->setData(self::SHIPPING_ADDRESS, $shippingAddress);
    }

    /**
     * Get shipping method
     *
     * @return string
     */
    public function getShippingMethod(){
        return $this->_getData(self::SHIPPING_METHOD);
    }

    /**
     * Set shipping method
     *
     * @param string $shippingMethod
     * @return $this
     */
    public function setShippingMethod($shippingMethod){
        return $this->setData(self::SHIPPING_METHOD, $shippingMethod);
    }

    /**
     * Get shipping cost
     *
     * @return float
     */
    public function getShippingCost(){
        return $this->_getData(self::SHIPPING_COST);
    }

    /**
     * Set shipping cost
     *
     * @param float $shippingCost
     * @return $this
     */
    public function setShippingCost($shippingCost){
        return $this->setData(self::SHIPPING_COST, $shippingCost);
    }

    /**
     * Get payment term
     *
     * @return string
     */
    public function getPaymentTerm(){
        return $this->_getData(self::PAYMENT_TERM);
    }

    /**
     * Set payment term
     *
     * @param string $paymentTerm
     * @return $this
     */
    public function setPaymentTerm($paymentTerm){
        return $this->setData(self::PAYMENT_TERM, $paymentTerm);
    }

    /**
     * Get placed via
     *
     * @return string
     */
    public function getPlacedVia(){
        return $this->_getData(self::PLACED_VIA);
    }

    /**
     * Set placed via
     *
     * @param string $placedVia
     * @return $this
     */
    public function setPlacedVia($placedVia){
        return $this->setData(self::PLACED_VIA, $placedVia);
    }

    /**
     * Get user id
     *
     * @return int
     */
    public function getUserId(){
        return $this->_getData(self::USER_ID);
    }

    /**
     * Set user id
     *
     * @param string $userId
     * @return $this
     */
    public function setUserId($userId){
        return $this->setData(self::USER_ID, $userId);
    }

    /**
     * Get created by
     *
     * @return string
     */
    public function getCreatedBy(){
        return $this->_getData(self::CREATED_BY);
    }

    /**
     * Set created by
     *
     * @param string $createdBy
     * @return $this
     */
    public function setCreatedBy($createdBy){
        return $this->setData(self::CREATED_BY, $createdBy);
    }

    /**
     * Get total qty orderred
     *
     * @return float
     */
    public function getTotalQtyOrderred(){
        return $this->_getData(self::TOTAL_QTY_ORDERRED);
    }

    /**
     * Set total qty orderred
     *
     * @param float $subtotal
     * @return $this
     */
    public function setTotalQtyOrderred($totalQtyOrderred){
        return $this->setData(self::TOTAL_QTY_ORDERRED, $totalQtyOrderred);
    }

    /**
     * Get total qty received
     *
     * @return float
     */
    public function getTotalQtyReceived(){
        return $this->_getData(self::TOTAL_QTY_RECEIVED);
    }

    /**
     * Set total qty received
     *
     * @param float $totalQtyReceived
     * @return $this
     */
    public function setTotalQtyReceived($totalQtyReceived){
        return $this->setData(self::TOTAL_QTY_RECEIVED, $totalQtyReceived);
    }

    /**
     * Get total qty billed
     *
     * @return float
     */
    public function getTotalQtyBilled(){
        return $this->_getData(self::TOTAL_QTY_BILLED);
    }

    /**
     * Set total qty billed
     *
     * @param float $totalQtyBilled
     * @return $this
     */
    public function setTotalQtyBilled($totalQtyBilled){
        return $this->setData(self::TOTAL_QTY_BILLED, $totalQtyBilled);
    }

    /**
     * Get total qty transferred
     *
     * @return float
     */
    public function getTotalQtyTransferred(){
        return $this->_getData(self::TOTAL_QTY_TRANSFERRED);
    }

    /**
     * Set total qty transferred
     *
     * @param float $totalQtyTransferred
     * @return $this
     */
    public function setTotalQtyTransferred($totalQtyTransferred){
        return $this->setData(self::TOTAL_QTY_TRANSFERRED, $totalQtyTransferred);
    }

    /**
     * Get total qty returned
     *
     * @return float
     */
    public function getTotalQtyReturned(){
        return $this->_getData(self::TOTAL_QTY_RETURNED);
    }

    /**
     * Set total qty transferred
     *
     * @param float $totalQtyTransferred
     * @return $this
     */
    public function setTotalQtyReturned($totalQtyReturned){
        return $this->setData(self::TOTAL_QTY_RETURNED, $totalQtyReturned);
    }

    /**
     * Get subtotal
     *
     * @return float
     */
    public function getSubtotal(){
        return $this->_getData(self::SUBTOTAL);
    }

    /**
     * Set subtotal
     *
     * @param float $subtotal
     * @return $this
     */
    public function setSubtotal($subtotal){
        return $this->setData(self::SUBTOTAL, $subtotal);
    }

    /**
     * Get total tax
     *
     * @return float
     */
    public function getTotalTax(){
        return $this->_getData(self::TOTAL_TAX);
    }

    /**
     * Set total tax
     *
     * @param float $totalTax
     * @return $this
     */
    public function setTotalTax($totalTax){
        return $this->setData(self::TOTAL_TAX, $totalTax);
    }

    /**
     * Get total discount
     *
     * @return float
     */
    public function getTotalDiscount(){
        return $this->_getData(self::TOTAL_DISCOUNT);
    }

    /**
     * Set total discount
     *
     * @param float $totalDiscount
     * @return $this
     */
    public function setTotalDiscount($totalDiscount){
        return $this->setData(self::TOTAL_DISCOUNT, $totalDiscount);
    }

    /**
     * Get grand total exclude tax
     *
     * @return float
     */
    public function getGrandTotalExclTax(){
        return $this->_getData(self::GRAND_TOTAL_EXCL_TAX);
    }

    /**
     * Set grand total exclude tax
     *
     * @param float $grandTotalExclTax
     * @return $this
     */
    public function setGrandTotalExclTax($grandTotalExclTax){
        return $this->setData(self::GRAND_TOTAL_EXCL_TAX, $grandTotalExclTax);
    }

    /**
     * Get grand total include tax
     *
     * @return float
     */
    public function getGrandTotalInclTax(){
        return $this->_getData(self::GRAND_TOTAL_INCL_TAX);
    }

    /**
     * Set grand total include tax
     *
     * @param float $grandTotalInclTax
     * @return $this
     */
    public function setGrandTotalInclTax($grandTotalInclTax){
        return $this->setData(self::GRAND_TOTAL_INCL_TAX, $grandTotalInclTax);
    }

    /**
     * Get total billed
     *
     * @return float
     */
    public function getTotalBilled(){
        return $this->_getData(self::TOTAL_BILLED);
    }

    /**
     * Set total billed
     *
     * @param float $totalBilled
     * @return $this
     */
    public function setTotalBilled($totalBilled){
        return $this->setData(self::TOTAL_BILLED, $totalBilled);
    }

    /**
     * Get total due
     *
     * @return float
     */
    public function getTotalDue(){
        return $this->_getData(self::TOTAL_DUE);
    }

    /**
     * Set total due
     *
     * @param float $totalDue
     * @return $this
     */
    public function setTotalDue($totalDue){
        return $this->setData(self::TOTAL_DUE, $totalDue);
    }

    /**
     * Get currency code
     *
     * @return string
     */
    public function getCurrencyCode(){
        return $this->_getData(self::CURRENCY_CODE);
    }

    /**
     * Set currency code
     *
     * @param string $currencyCode
     * @return $this
     */
    public function setCurrencyCode($currencyCode){
        return $this->setData(self::CURRENCY_CODE, $currencyCode);
    }

    /**
     * Get currency rate
     *
     * @return string
     */
    public function getCurrencyRate(){
        return $this->_getData(self::CURRENCY_RATE);
    }

    /**
     * Set currency rate
     *
     * @param string $currencyRate
     * @return $this
     */
    public function setCurrencyRate($currencyRate){
        return $this->setData(self::CURRENCY_RATE, $currencyRate);
    }

    /**
     * Get purchased at
     *
     * @return string
     */
    public function getPurchasedAt(){
        return $this->_getData(self::PURCHASED_AT);
    }

    /**
     * Set purchased at
     *
     * @param string $purchasedAt
     * @return $this
     */
    public function setPurchasedAt($purchasedAt){
        return $this->setData(self::PURCHASED_AT, $purchasedAt);
    }

    /**
     * Get started at
     *
     * @return string
     */
    public function getStartedAt(){
        return $this->_getData(self::STARTED_AT);
    }

    /**
     * Set started at
     *
     * @param string $startedAt
     * @return $this
     */
    public function setStartedAt($startedAt){
        return $this->setData(self::STARTED_AT, $startedAt);
    }

    /**
     * Get expected at
     *
     * @return string
     */
    public function getExpectedAt(){
        return $this->_getData(self::EXPECTED_AT);
    }

    /**
     * Set expected at
     *
     * @param string $expectedAt
     * @return $this
     */
    public function setExpectedAt($expectedAt){
        return $this->setData(self::EXPECTED_AT, $expectedAt);
    }

    /**
     * Get canceled at
     *
     * @return string
     */
    public function getCanceledAt(){
        return $this->_getData(self::CANCELED_AT);
    }

    /**
     * Set canceled at
     *
     * @param string $canceledAt
     * @return $this
     */
    public function setCanceledAt($canceledAt){
        return $this->setData(self::CANCELED_AT, $canceledAt);
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

    /**
     * Get updated at
     *
     * @return string
     */
    public function getUpdatedAt(){
        return $this->_getData(self::UPDATED_AT);
    }

    /**
     * Set updated at
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt){
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * Get purchaseKey
     *
     * @return string
     */
    public function getPurchaseKey(){
        return $this->_getData(self::PURCHASE_KEY);
    }

    /**
     * Set purchaseKey
     *
     * @param string $purchaseKey
     * @return $this
     */
    public function setPurchaseKey($purchaseKey){
        return $this->setData(self::PURCHASE_KEY, $purchaseKey);
    }
    
    /**
     * Get purchase order item
     *
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface[]
     */
    public function getItems(){
        return $this->purchaseItemService->getProductsByPurchaseOrderId($this->getId())->getItems();
    }

    /**
     * Set purchase order item
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface[] $item
     * @return $this
     */
    public function setItems($item){
        return $this->setData(self::ITEMS, $item);
    }

    /**
     * Check $this is a quotation
     *
     * @return bool
     */
    public function isQuotation(){
        return $this->getType() == PurchaseOrderType::TYPE_QUOTATION;
    }

    /**
     * Processing object before save data
     *
     * @return $this
     */
    public function beforeSave()
    {
        if (!$this->getId()) {
            $this->isObjectNew(true);
            $this->setStatus(PurchaseOrder\Option\Status::STATUS_PENDING);
            $user = $this->_auth->getUser();
            $this->setUserId($user->getUserId());
            $this->setCreatedBy($user->getUserName());
//            $this->purchaseOrderService->getCurrencyCode($this);
//            $this->setShippingCost($this->taxAndShippingService->getDefaultShippingCost());
        }else{
            $this->shippingMethodService->saveConfig($this);
            $this->paymentTermService->saveConfig($this);
            $countItems = $this->purchaseItemService->getProductsByPurchaseOrderId($this->getId())->getSize();
            if($countItems==0){
                $this->setShippingCost(0);
            }
        }
        $this->setGrandTotalExclTax(
            $this->getSubtotal()+$this->getShippingCost()+$this->getTotalDiscount()
        );
        $this->setGrandTotalInclTax(
            $this->getGrandTotalExclTax()+$this->getTotalTax()
        );
        // add purchase key
        if($this->getPurchaseKey() == null) {
            $tmp = [
                'id' => $this->getId(),
                'supplier_id' => $this->getSupplierId()
            ];
            $key = hash('sha256', serialize($tmp));
            $this->setPurchaseKey($key);
        }
        $this->purchaseOrderService->getPurchaseCode($this);
        $this->_eventManager->dispatch('model_save_before', ['object' => $this]);
        $this->_eventManager->dispatch($this->_eventPrefix . '_save_before', $this->_getEventData());
        return $this;
    }
    
    public function canSendEmail(){
        $status = $this->getStatus();
        if(!$status || !$this->getSendEmail() || !$this->getId())
            return false;
        if($status == PurchaseOrder\Option\Status::STATUS_CANCELED)
            return false;
        return true;
    }
}