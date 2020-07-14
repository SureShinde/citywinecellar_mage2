<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Created by PhpStorm.
 * User: steve
 * Date: 07/06/2016
 * Time: 09:21
 */

namespace Magestore\Webpos\Api\Data\Shift;

/**
 * Interface CashTransactionInterface
 * @package Magestore\Webpos\Api\Data\Shift
 */
interface CashTransactionInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * Constants defined for keys of data array
     */
    const ADD_TYPE = "add";
    const REMOVE_TYPE = "remove";


    const TRANSACTION_INCREMENT_ID = "transaction_increment_id";
    const LOCATION_ID = "location_id";
    const ORDER_INCREMENT_ID = "order_increment_id";
    const SHIFT_INCREMENT_ID = "shift_increment_id";
    const VALUE = "value";
    const BASE_VALUE = "base_value";
    const CREATED_AT = "created_at";
    const NOTE = "note";
    const TYPE = "type";
    const BASE_CURRENCY_CODE = "base_currency_code";
    const TRANSACTION_CURRENCY_CODE = "transaction_currency_code";
    const UPDATED_AT = "updated_at";


    /**
     *  transaction id
     * @return string|null
     */
    public function getTransactionIncrementId();


    /**
     * Set Transaction Id
     *
     * @param string $transaction_increment_id
     * @return CashTransactionInterface
     */
    public function setTransactionIncrementId($transaction_increment_id);

    /**
     *  shift id
     * @return string|null
     */
    public function getShiftIncrementId();


    /**
     * Set Shift Id
     *
     * @param string $shift_increment_id
     * @return CashTransactionInterface
     */
    public function setShiftIncrementId($shift_increment_id);

    /**
     *  get created at
     * @return string|null created_at
     */
    public function getCreatedAt();

    /**
     *  set created at
     * @param $created_at
     * @return string|null created_at
     */
    public function setCreatedAt($created_at);


    /**
     *  updated at
     * @return string|null updated_at
     */
    public function getUpdatedAt();


    /**
     *  updated time
     * @param $updated_at
     * @return string|null updated_at
     */
    public function setUpdatedAt($updated_at);

    /**
     * get value of the transaction
     * @return float
     */
    public function getValue();


    /**
     * set value of the transaction
     * @param float $value
     * @return CashTransactionInterface
     */
    public function setValue($value);

    /**
     * get base_value of the transaction
     * @return float
     */
    public function getBaseValue();


    /**
     * set base_value of the transaction
     * @param float $base_value
     * @return CashTransactionInterface
     */
    public function setBaseValue($base_value);

    /**
     * get note
     * @return string
     */
    public function getNote();

    /**
     * set note
     * @param string $note
     * @return CashTransactionInterface
     */
    public function setNote($note);


    /**
     *  location id
     * @return int|null
     */
    public function getLocationId();


    /**
     * Set Location Id
     *
     * @param int $location_id
     * @return CashTransactionInterface
     */
    public function setLocationId($location_id);


    /**
     *  order id
     * @return string|null
     */
    public function getOrderIncrementId();


    /**
     * Set Order Id
     *
     * @param string $order_increment_id
     * @return CashTransactionInterface
     */
    public function setOrderIncrementId($order_increment_id);


    /**
     * get type of transaction: add, remove, order
     * add: add cash to cash drawer by manual
     * remove: remove cash from cash drawer by manual
     * order: add cash to cash drawer from order
     * @return string
     */
    public function getType();

    /**
     * set type
     * @param string $type
     * @return CashTransactionInterface
     */
    public function setType($type);

    /**
     * get base currency code
     * @return string
     */
    public function getBaseCurrencyCode();

    /**
     * set base currency code
     * @param string $base_currency_code
     * @return CashTransactionInterface
     */
    public function setBaseCurrencyCode($base_currency_code);

    /**
     * get shift currency code
     * @return string
     */
    public function getTransactionCurrencyCode();

    /**
     * set Transaction currency code
     * @param string $transaction_currency_code
     * @return CashTransactionInterface
     */
    public function setTransactionCurrencyCode($transaction_currency_code);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Shift\CashTransactionExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Shift\CashTransactionExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Shift\CashTransactionExtensionInterface $extensionAttributes
    );
}