<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Created by PhpStorm.
 * User: steve
 * Date: 06/06/2016
 * Time: 13:29
 */

namespace Magestore\Webpos\Model\Shift;

use \Magento\Framework\Model\AbstractModel as AbstractModel;
use \Magestore\Webpos\Api\Data\Shift\CashTransactionInterface as CashTransactionInterface;

/**
 * Class Transaction
 * @package Magestore\Webpos\Model\CashTransaction
 */

class CashTransaction extends AbstractModel implements CashTransactionInterface
{
    /** @var  \Magestore\Webpos\Helper\Currency */
    protected $_webposCurrencyHelper;




    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\Webpos\Helper\Currency $webposCurrencyHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,

        array $data = []
    ) {
        $this->_webposCurrencyHelper = $webposCurrencyHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('Magestore\Webpos\Model\ResourceModel\Shift\CashTransaction');
    }

    public function getTransactionIncrementId()
    {
        return $this->getData(self::TRANSACTION_INCREMENT_ID);
    }

    public function setTransactionIncrementId($transaction_increment_id)
    {
        return $this->setData(self::TRANSACTION_INCREMENT_ID, $transaction_increment_id);
    }

    public function getShiftIncrementId()
    {
        return $this->getData(self::SHIFT_INCREMENT_ID);
    }

    public function setShiftIncrementId($shift_increment_id)
    {
        return $this->setData(self::SHIFT_INCREMENT_ID, $shift_increment_id);
    }

    /**
     *  location id
     * @return int|null
     */
    public function getLocationId()
    {
        return $this->getData(self::LOCATION_ID);
    }

    /**
     * Set location Id
     *
     * @param int $location_id
     * @return $this
     */
    public function setLocationId($location_id)
    {
        return $this->setData(self::LOCATION_ID, $location_id);
    }


    /**
     *  order id
     * @return string|null
     */
    public function getOrderIncrementId()
    {
        return $this->getData(self::ORDER_INCREMENT_ID);
    }

    /**
     * Set Order Id
     *
     * @param string $order_increment_id
     * @return $this
     */
    public function setOrderIncrementId($order_increment_id)
    {
        return $this->setData(self::ORDER_INCREMENT_ID, $order_increment_id);
    }

    /**
     *  get created at
     * @return string|null created_at
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     *  set created at
     * @return string|null created_at
     */
    public function setCreatedAt($created_at)
    {
        return $this->setData(self::CREATED_AT, $created_at);
    }

    /**
     *  get updated at
     * @return string|null updated_at
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     *  set updated at
     * @return string|null updated_at
     */
    public function setUpdatedAt($updated_at)
    {
        return $this->setData(self::UPDATED_AT, $updated_at);
    }


    /**
     * get value of the transaction
     * @return float
     */
    public function getValue()
    {
        return $this->getData(self::VALUE);
    }


    /**
     * set value of the transaction
     * @param float @value
     * @return $this
     */
    public function setValue($value)
    {
        return $this->setData(self::VALUE, $value);
    }

    /**
     * get base_value of the transaction
     * @return float
     */
    public function getBaseValue()
    {
        return $this->getData(self::BASE_VALUE);
    }

    /**
     * set base_value of the transaction
     * @param float @base_value
     * @return $this
     */
    public function setBaseValue($base_value)
    {
        return $this->setData(self::BASE_VALUE, $base_value);
    }

    /**
     * get note
     * @return string
     */
    public function getNote()
    {
        return $this->getData(self::NOTE);
    }

    /**
     * set note
     * @param string $note
     * @return $this
     */
    public function setNote($note)
    {
        return $this->setData(self::NOTE, $note);
    }

    /**
     * get type
     * @return string
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * set type
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * get base currency code
     * @return string
     */
    public function getBaseCurrencyCode(){
        return $this->getData(self::BASE_CURRENCY_CODE);
    }

    /**
     * set base currency code
     * @param string $base_currency_code
     * @return $this
     */
    public function setBaseCurrencyCode($base_currency_code){
        return $this->setData(self::BASE_CURRENCY_CODE,$base_currency_code);
    }

    /**
     * get Transaction currency code
     * @return string
     */
    public function getTransactionCurrencyCode(){
        return $this->getData(self::TRANSACTION_CURRENCY_CODE);
    }

    /**
     * set transaction currency code
     * @param string $transaction_currency_code
     * @return $this
     */
    public function setTransactionCurrencyCode($transaction_currency_code){
        return $this->setData(self::TRANSACTION_CURRENCY_CODE,$transaction_currency_code);
    }
    
    
    /**
     * get staff name
     * @return string
     */
    public function getStaffName()
    {
        return $this->getData(self::STAFF_NAME);
    }

    /**
     * set staff name
     * @param string $staff_name
     * @return $this
     */
    public function setStaffName($staff_name)
    {
        return $this->setData(self::STAFF_NAME, $staff_name);
    }


    /**
     *  staff id
     * @return int|null
     */
    public function getStaffId()
    {
        return $this->getData(self::STAFF_ID);
    }


    /**
     * Set Staff Id
     *
     * @param int $staffId
     * @return $this
     */
    public function setStaffId($staffId)
    {
        return $this->setData(self::STAFF_ID, $staffId);
    }

    /**
     * get all transactions for a shift has shift_id
     * @param string $shift_increment_id
     * @return mixed
     */
    public function getByShiftIncrementId($shift_increment_id)
    {
        /** @var \Magestore\Webpos\Model\ResourceModel\Shift\CashTransaction\Collection $collection */
        $collection = $this->getCollection();
        $collection->addFieldToFilter("shift_increment_id", $shift_increment_id)->setOrder('created_at', 'DESC');
        $transactions = [];
        if($collection->getSize()) {
            foreach ($collection as $transaction) {
                $transactions[] = $transaction;
            }
        }
        return $transactions;
    }
    /**
     * get all transactions for a shift has shift_id
     * @param string $shift_increment_id
     * @return mixed
     */
    public function getCollectionByShift($shift_increment_id)
    {
        /** @var \Magestore\Webpos\Model\ResourceModel\Shift\CashTransaction\Collection $collection */
        $collection = $this->getCollection();
        $collection->addFieldToFilter("shift_increment_id", $shift_increment_id)->setOrder('created_at', 'DESC');
        return $collection;
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
        \Magestore\Webpos\Api\Data\Shift\CashTransactionExtensionInterface $extensionAttributes
    ){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}