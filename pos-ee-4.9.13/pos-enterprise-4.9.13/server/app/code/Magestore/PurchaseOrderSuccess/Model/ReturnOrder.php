<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model;

use Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderInterface;

class ReturnOrder extends \Magento\Framework\Model\AbstractModel
    implements ReturnOrderInterface
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'purchaseordersuccess_returnorder';

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\ReturnOrder\Item\ItemService
     */
    protected $returnItemService;

    /**
     * @var \Magento\Backend\Model\Auth
     */
    protected $_auth;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\ReturnOrder\ReturnOrderService
     */
    protected $returnOrderService;

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'returnorder';

    public function __construct(
        \Magestore\PurchaseOrderSuccess\Service\ReturnOrder\ReturnOrderService $returnOrderService,
        \Magestore\PurchaseOrderSuccess\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\PurchaseOrderSuccess\Service\ReturnOrder\Item\ItemService $returnItemService,
        \Magento\Backend\Model\Auth $auth,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ){
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->returnItemService = $returnItemService;
        $this->returnOrderService = $returnOrderService;
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
        $this->_init('Magestore\PurchaseOrderSuccess\Model\ResourceModel\ReturnOrder');
    }

    /**
     * Get return order id
     *
     * @return int
     */
    public function getReturnOrderId(){
        return $this->_getData(self::RETURN_ORDER_ID);
    }

    /**
     * Set return order id
     *
     * @param int $returnOrderId
     * @return $this
     */
    public function setReturnOrderId($returnOrderId){
        return $this->setData(self::RETURN_ORDER_ID, $returnOrderId);
    }

    /**
     * Get return code
     *
     * @return string|null
     */
    public function getReturnCode(){
        return $this->_getData(self::RETURN_CODE);
    }

    /**
     * Set return code
     *
     * @param string $returnCode
     * @return $this
     */
    public function setReturnCode($returnCode){
        return $this->setData(self::RETURN_CODE, $returnCode);
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
     * Get supplier id
     *
     * @return int
     */
    public function getWarehouseId(){
        return $this->_getData(self::WAREHOUSE_ID);
    }

    /**
     * Set warehouse id
     *
     * @param int $warehouseId
     * @return $this
     */
    public function setWarehouseId($warehouseId){
        return $this->setData(self::WAREHOUSE_ID, $warehouseId);
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
     * Set total qty returned
     *
     * @param float $totalQtyReturned
     * @return $this
     */
    public function setTotalQtyReturned($totalQtyReturned){
        return $this->setData(self::TOTAL_QTY_RETURNED, $totalQtyReturned);
    }

    /**
     * Get returnd at
     *
     * @return string
     */
    public function getReturnedAt() {
        return $this->_getData(self::RETURNED_AT);
    }

    /**
     * Set returned at
     *
     * @param string $returnedAt
     * @return $this
     */
    public function setReturnedAt($returnedAt) {
        return $this->setData(self::RETURNED_AT, $returnedAt);
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
     * Get purchase order item
     *
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemInterface[]
     */
    public function getItems(){
        return $this->returnItemService->getProductsByReturnOrderId($this->getId())->getItems();
    }

    /**
     * Set purchase order item
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemInterface[] $item
     * @return $this
     */
    public function setItems($item){
        return $this->setData(self::ITEMS, $item);
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
            $this->setStatus(ReturnOrder\Option\Status::STATUS_PENDING);
            $user = $this->_auth->getUser();
            $this->setUserId($user->getUserId());
            $this->setCreatedBy($user->getUserName());
        }else{
            $countItems = $this->returnItemService->getProductsByReturnOrderId($this->getId())->getSize();
        }
        $this->returnOrderService->getReturnCode($this);
        $this->_eventManager->dispatch('model_save_before', ['object' => $this]);
        $this->_eventManager->dispatch($this->_eventPrefix . '_save_before', $this->_getEventData());
        return $this;
    }

    public function canSendEmail(){
        $status = $this->getStatus();
        if(!$status || !$this->getId())
            return false;
        if($status == ReturnOrder\Option\Status::STATUS_CANCELED)
            return false;
        return true;
    }
}