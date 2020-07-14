<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model;

use Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderCodeInterface;

class PurchaseOrderCode extends \Magento\Framework\Model\AbstractModel
    implements PurchaseOrderCodeInterface
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'purchaseordersuccess_purchaseorder_code';

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
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ){
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
        $this->_init('Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrderCode');
    }

    /**
     * Get purchase order code id
     *
     * @return int
     */
    public function getPurchaseOrderCodeId(){
        return $this->_getData(self::PURCHASE_ORDER_CODE_ID);
    }

    /**
     * Set purchase order code id
     *
     * @param int $purchaseOrderCodeId
     * @return $this
     */
    public function setPurchaseOrderCodeId($purchaseOrderCodeId){
        return $this->setData(self::PURCHASE_ORDER_CODE_ID, $purchaseOrderCodeId);
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode(){
        return $this->_getData(self::CODE);
    }

    /**
     * Set code
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code){
        return $this->setData(self::CODE, $code);
    }

    /**
     * Get current id
     *
     * @return int
     */
    public function getCurrentId(){
        return $this->_getData(self::CURRENT_ID);
    }

    /**
     * Set current id
     *
     * @param int $currentId
     * @return $this
     */
    public function setCurrentId($currentId){
        return $this->setData(self::CURRENT_ID, $currentId);
    }
}