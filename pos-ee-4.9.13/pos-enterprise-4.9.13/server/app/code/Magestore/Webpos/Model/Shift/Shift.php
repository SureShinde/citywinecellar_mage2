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

use \Magestore\Webpos\Api\Data\Shift\ShiftInterface as ShiftInterface;


/**
 * Class Shift
 * @package Magestore\Webpos\Model\Shift
 */

class Shift extends \Magento\Framework\Model\AbstractModel implements ShiftInterface
{
    /** @var  $transactionFactory \Magestore\Webpos\Model\Shift\CashTransactionFactory */
    protected $_cashTransactionFactory;

    /** @var  \Magestore\Webpos\Helper\Shift */
    protected $_shiftHelper;

    /** @var  \Magestore\Webpos\Helper\Currency */
    protected $_webposCurrencyHelper;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Shift constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magestore\Webpos\Model\ResourceModel\Shift\Shift $resource
     * @param \Magestore\Webpos\Model\ResourceModel\Shift\Shift\Collection $resourceCollection
     * @param \Magestore\Appadmin\Api\Event\DispatchServiceInterface $dispatchService
     * @param \Magestore\Webpos\Api\Pos\PosRepositoryInterface $posRepository
     * @param CashTransactionFactory $cashTransactionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magestore\Webpos\Helper\Currency $webposCurrencyHelper
     * @param \Magestore\Webpos\Helper\Shift $shiftHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\Webpos\Model\ResourceModel\Shift\Shift $resource,
        \Magestore\Webpos\Model\ResourceModel\Shift\Shift\Collection $resourceCollection,
        \Magestore\Appadmin\Api\Event\DispatchServiceInterface $dispatchService,
        \Magestore\Webpos\Api\Pos\PosRepositoryInterface $posRepository,
        \Magestore\Webpos\Model\Shift\CashTransactionFactory $cashTransactionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magestore\Webpos\Helper\Currency $webposCurrencyHelper,
        \Magestore\Webpos\Helper\Shift $shiftHelper,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection,  $data);
        $this->_eventManager = $context->getEventDispatcher();
        $this->_cashTransactionFactory = $cashTransactionFactory;
        $this->_storeManager = $storeManager;
        $this->_shiftHelper = $shiftHelper;
        $this->_webposCurrencyHelper = $webposCurrencyHelper;
    }


    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\Webpos\Model\ResourceModel\Shift\Shift');
    }



    /**
     *  Entity id
     * @return int
     */
    public function getShiftId()
    {
        return (int)$this->getData(self::SHIFT_ID);
    }

    /**
     * Set Entity Id
     *
     * @param int $id
     * @return $this
     */
    public function setShiftId($shiftId)
    {
        return $this->setData(self::SHIFT_ID, $shiftId);
    }

    /**
     *  shift id
     * @return string
     */
    public function getShiftIncrementId()
    {
        return $this->getData(self::SHIFT_INCREMENT_ID);
    }

    /**
     * Set Shift Id
     *
     * @param string $shiftIncrementId
     * @return $this
     */
    public function setShiftIncrementId($shiftIncrementId)
    {
        return $this->setData(self::SHIFT_INCREMENT_ID, $shiftIncrementId);
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
     *  location id
     * @return int|null
     */
    public function getLocationId()
    {
        return $this->getData(self::LOCATION_ID);
    }

    /**
     * Set Location_Id
     *
     * @param int $locationId
     * @return $this
     */
    public function setLocationId($locationId)
    {
        return $this->setData(self::LOCATION_ID, $locationId);
    }

    /**
     *  open time
     * @return string/null
     */
    public function getOpenedAt()
    {
        return $this->getData(self::OPENED_AT);
    }

    /**
     * set Opended At
     * @param $opened_at $openedAt
     * @return $this
     */
    public function setOpenedAt($opened_at){
        return $this->setData(self::OPENED_AT, $opened_at);
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
     * @param $updated_at
     * @return $this|null|string
     */
    public function setUpdatedAt($updated_at)
    {
        return $this->setData(self::UPDATED_AT, $updated_at);
    }


    /**
     *  closed at
     * @return string
     */
    public function getClosedAt()
    {
        return $this->getData(self::CLOSED_AT);
    }

    /**
     * set Closed At
     * @param string $closed_at
     * @return $this
     */
    public function setClosedAt($closed_at)
    {
        return $this->setData(self::CLOSED_AT, $closed_at);
    }

    /**
     * float amount when open shift
     * @return float
     */
    public function getOpeningAmount()
    {
        return $this->getData(self::OPENING_AMOUNT);
    }

    /**
     * Set Float Amount
     * @param float $opening_amount
     * @return $this
     */
    public function setOpeningAmount($opening_amount)
    {
        return $this->setData(self::OPENING_AMOUNT, $opening_amount);
    }

    /**
     * get base float amount when open shift
     * @return float
     */
    public function getBaseOpeningAmount()
    {
        return $this->getData(self::BASE_OPENING_AMOUNT);
    }

    /**
     * Set Base Float Amount
     * @param float $base_opening_amount
     * @return $this
     */
    public function setBaseOpeningAmount($base_opening_amount)
    {
        return $this->setData(self::BASE_OPENING_AMOUNT, $base_opening_amount);
    }

    /**
     * closed amount when open shift
     * @return float
     */
    public function getClosedAmount()
    {
        return $this->getData(self::CLOSED_AMOUNT);
    }

    /**
     * set Closed Amount
     * @param float @closed_amount
     * @return $this
     */
    public function setClosedAmount($closedAmount)
    {
        return $this->setData(self::CLOSED_AMOUNT, $closedAmount);
    }

    /**
     * base closed amount when open shift
     * @return float
     */
    public function getBaseClosedAmount()
    {
        return $this->getData(self::BASE_CLOSED_AMOUNT);
    }

    /**
     * set Base Closed Amount
     * @param float @base_closed_amount
     * @return $this
     */
    public function setBaseClosedAmount($baseClosedAmount)
    {
        return $this->setData(self::BASE_CLOSED_AMOUNT, $baseClosedAmount);
    }

    /**
     * status
     * @return int
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * set Status
     * @param int @status
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * get closed note
     * @return string
     */
    public function getClosedNote()
    {
        return $this->getData(self::CLOSED_NOTE);
    }

    /**
     * @param string $closedNote
     * @return $this
     */
    public function setClosedNote($closedNote)
    {
        return $this->setData(self::CLOSED_NOTE, $closedNote);
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
    public function setBaseCurrencyCode($baseCurrencyCode){
        return $this->setData(self::BASE_CURRENCY_CODE,$baseCurrencyCode);
    }

    /**
     * get shift currency code
     * @return string
     */
    public function getShiftCurrencyCode(){
        return $this->getData(self::SHIFT_CURRENCY_CODE);
    }

    /**
     * set shift currency code
     * @param string $shiftCurrencyCode
     * @return $this
     */
    public function setShiftCurrencyCode($shiftCurrencyCode){
        return $this->setData(self::SHIFT_CURRENCY_CODE,$shiftCurrencyCode);
    }

    /**
     * get pos id
     * @return string
     */
    public function getPosId()
    {
        return $this->getData(self::POS_ID);
    }

    /**
     * get pos name
     * @return string
     */
    public function getPosName()
    {
        return $this->_shiftHelper->getPosName($this);
    }

    /**
     * get staff name
     * @return string
     */
    public function getStaffName()
    {
        return $this->_shiftHelper->getStaffName($this);
    }

    /**
     * set pos id
     * @param string $posId
     * @return $this
     */
    public function setPosId($posId)
    {
        return $this->setData(self::POS_ID, $posId);
    }


    /**
     * get cash transaction
     * @return array
     */
    public function getCashTransaction()
    {
        return $this->_shiftHelper->getCashTransaction($this);
    }

    public function getSaleSummary()
    {
        return $this->_shiftHelper->getSaleSummaryByShiftIncrementId($this->getShiftIncrementId());
    }

    /**
     * get a list of Shift for a specific staff_id.
     * Because in the frontend we just need to show all shift for "this week"
     * so we will return this week shift only.
     *
     * @param integer $staffId
     * @return array
     */
    public function getList($staffId)
    {
        /** @var  $shiftCollection \Magestore\Webpos\Model\ResourceModel\Shift\Shift\Collection */
        $shiftCollection = $this->getCollection();
        $shiftCollection->addFieldToFilter("staff_id", $staffId);
        $shiftCollection->setOrder("id", "DESC");

        //return data
        $data = [];

        /** @var \Magestore\Webpos\Model\Shift\Shift $shift */
        foreach ($shiftCollection as $shift) {
            $data[] = $this->_shiftHelper->getShiftData($shift);
        }

        return $data;
    }


    /**check if shift_currency_code is change
     * and check if base_currency_code is change.
     * if yes, update new data for shift then update value for shift_currency_code and base_currency_code.
     *
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    public function updateShiftDataCurrency($data){

        //currency code that stored in shift record
        $shiftBaseCurrencyCode = $data['base_currency_code'];
        $shiftCurrencyCode = $data['shift_currency_code'];
        //current currency code of the system now
        $baseCurrencyCode = $this->_webposCurrencyHelper->getBaseCurrencyCode();
        $currentCurrencyCode = $this->_webposCurrencyHelper->getCurrentCurrencyCode();
        //no thing change on currency, so we don't need to convert data.
        if(($shiftBaseCurrencyCode == $baseCurrencyCode) && ($shiftCurrencyCode == $currentCurrencyCode)){
            return $data;
        }
        //stored baseCurrencyCode is different from the base currency code now
        //convert all old base currency value to new base currency value
        if($shiftBaseCurrencyCode != $baseCurrencyCode){
            $data['base_currency_code'] = $baseCurrencyCode;
            $data['base_opening_amount'] = $this->_webposCurrencyHelper->currencyConvert($data['base_opening_amount'],$shiftBaseCurrencyCode, $baseCurrencyCode );
            $data['base_closed_amount'] = $this->_webposCurrencyHelper->currencyConvert($data['base_closed_amount'],$shiftBaseCurrencyCode, $baseCurrencyCode );
        }

        //stored of display currency code is different from the current display currency code at this time
        //convert all old display currency value to new display currency value
        if ($shiftCurrencyCode != $currentCurrencyCode){
            $data['shift_currency_code'] = $currentCurrencyCode;
            $data['opening_amount'] = $this->_webposCurrencyHelper->currencyConvert($data['opening_amount'],$shiftCurrencyCode, $currentCurrencyCode);
            $data['closed_amount'] = $this->_webposCurrencyHelper->currencyConvert($data['closed_amount'],$shiftCurrencyCode, $currentCurrencyCode);
        }

        $this->setData($data);
        $this->save();
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function getExtensionAttributes() {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(\Magestore\Webpos\Api\Data\Shift\ShiftExtensionInterface $extensionAttributes){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}