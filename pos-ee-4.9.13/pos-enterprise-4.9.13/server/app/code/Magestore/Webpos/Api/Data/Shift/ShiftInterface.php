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

use Magento\Framework\Api\ExtensibleDataInterface;
/**
 * Interface ShiftInterface
 * @package Magestore\Webpos\Api\Data\Shift
 */
interface ShiftInterface extends ExtensibleDataInterface
{

    /**
     * Constants defined for keys of data array
     */

    const OPEN_STATUS = 0;
    const CLOSE_STATUS = 1;

    const SHIFT_ID = "shift_id";
    const SHIFT_INCREMENT_ID = "shift_increment_id";
    const STAFF_ID = "staff_id";
    const STAFF_NAME = "staff_name";
    const LOCATION_ID = "location_id";
    const OPENED_AT = "opened_at";
    const CLOSED_AT = "closed_at";
    const OPENING_AMOUNT = "opening_amount";
    const BASE_OPENING_AMOUNT = "base_opening_amount";
    const CLOSED_AMOUNT = "closed_amount";
    const BASE_CLOSED_AMOUNT = "base_closed_amount";
    const STATUS = "status";
    const CLOSED_NOTE = "closed_note";
    const BASE_CURRENCY_CODE = "base_currency_code";
    const SHIFT_CURRENCY_CODE = "shift_currency_code";
    const UPDATED_AT = "updated_at";
    const POS_ID = "pos_id";
    const CASH_TRANSACTION = "cash_transaction";


    /**
     *  id
     * @return int|null
     */
    public function getShiftId();


    /**
     * Set Shift Id
     *
     * @param string $shiftId
     * @return ShiftInterface
     */
    public function setShiftId($shiftId);

    /**
     *  shift increment id
     * @return string|null
     */
    public function getShiftIncrementId();


    /**
     * Set Shift increment Id
     *
     * @param string $shift_increment_id
     * @return ShiftInterface
     */
    public function setShiftIncrementId($shift_increment_id);

    /**
     *  staff id
     * @return int|null
     */
    public function getStaffId();


    /**
     * Set Staff Id
     *
     * @param int $staff_id
     * @return ShiftInterface
     */
    public function setStaffId($staff_id);

    /**
     *  location_id
     * @return int|null
     */
    public function getLocationId();


    /**
     * Set Location Id
     *
     * @param int $location_id
     * @return ShiftInterface
     */
    public function setLocationId($location_id);


    /**
     *  open time
     * @return string|null opened_at
     */
    public function getOpenedAt();


    /**
     * opened time
     * @param $opened_at
     * @return string|null opened_at
     */
    public function setOpenedAt($opened_at);

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
     *  get closed at
     * @return string|null closed-at
     */
    public function getClosedAt();

    /**
     * set Closed At
     * @param string|null $closed_at
     * @return ShiftInterface
     */
    public function setClosedAt($closed_at);

    /**
     * float amount when open shift
     * @return float
     */
    public function getOpeningAmount();

    /**
     * Set Float Amount
     * @param float $opening_amount
     * @return ShiftInterface
     */
    public function setOpeningAmount($opening_amount);

    /**
     * base_float amount when open shift
     * @return float
     */
    public function getBaseOpeningAmount();

    /**
     * Set Base Float Amount
     * @param float $base_opening_amount
     * @return ShiftInterface
     */
    public function setBaseOpeningAmount($base_opening_amount);

    /**
     * closed amount when open shift
     * @return float
     */
    public function getClosedAmount();

    /**
     * set Closed Amount
     * @param float @closed_amount
     * @return ShiftInterface
     */
    public function setClosedAmount($closed_amount);

    /**
     * base closed amount when open shift
     * @return float
     */
    public function getBaseClosedAmount();

    /**
     * set Base Closed Amount
     * @param float @base_closed_amount
     * @return ShiftInterface
     */
    public function setBaseClosedAmount($base_closed_amount);


    /**
     * status
     * @return int
     */
    public function getStatus();


    /**
     * set Status
     * @param int $status
     * @return ShiftInterface
     */
    public function setStatus($status);

    /**
     * get closed note
     * @return string
     */
    public function getClosedNote();

    /**
     * set closed note
     * @param string $closed_note
     * @return ShiftInterface
     */
    public function setClosedNote($closed_note);

    /**
     * get base currency code
     * @return string
     */
    public function getBaseCurrencyCode();

    /**
     * set base currency code
     * @param string $base_currency_code
     * @return ShiftInterface
     */
    public function setBaseCurrencyCode($base_currency_code);

    /**
     * get shift currency code
     * @return string
     */
    public function getShiftCurrencyCode();

    /**
     * set shift currency code
     * @param string $shift_currency_code
     * @return ShiftInterface
     */
    public function setShiftCurrencyCode($shift_currency_code);

    /**
     * get pos id
     * @return string
     */
    public function getPosId();

    /**
     * set pos id
     * @param string $posId
     * @return ShiftInterface
     */
    public function setPosId($posId);

    /**
     * get pos name
     * @return string
     */
    public function getPosName();

    /**
     * get pos name
     * @return string
     */
    public function getStaffName();

    /**
     * get cash transaction
     * @return \Magestore\Webpos\Api\Data\Shift\CashTransactionInterface[]
     */
    public function getCashTransaction();

    /**
     * get sale summary
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\PaymentInterface[]
     */
    public function getSaleSummary();

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Shift\ShiftExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Shift\ShiftExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\Magestore\Webpos\Api\Data\Shift\ShiftExtensionInterface $extensionAttributes);

}