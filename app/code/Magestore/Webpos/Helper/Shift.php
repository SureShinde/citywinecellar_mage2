<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Helper;

use \Magestore\Webpos\Model\Source\Adminhtml\Since as Since;


class Shift extends \Magento\Framework\App\Helper\AbstractHelper
{
    /** @var $shiftFactory  \Magestore\Webpos\Model\Shift\ShiftFactory */
    protected $_shiftFactory;

    /** @var  \Magestore\Webpos\Model\ResourceModel\Sales\Order\Payment\CollectionFactory */
    protected $_orderPaymentCollectionFactory;

    /** @var  $transactionFactory \Magestore\Webpos\Model\Shift\CashTransactionFactory */
    protected $_cashTransactionFactory;

    /** @var  $posRepository \Magestore\Webpos\Model\Pos\PosRepository */
    protected $posRepository;

    /** @var  $posRepository \Magestore\Appadmin\Model\Staff\StaffFactory */
    protected $staffFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * Shift constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magestore\Webpos\Model\Shift\ShiftFactory $shiftFactory
     * @param \Magestore\Webpos\Model\ResourceModel\Sales\Order\Payment\CollectionFactory $orderPaymentCollectionFactory
     * @param \Magestore\Webpos\Model\Shift\CashTransactionFactory $cashTransactionFactory
     * @param \Magestore\Webpos\Model\Pos\PosRepository $posRepository
     * @param \Magestore\Appadmin\Model\Staff\StaffFactory $staffRepository
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magestore\Webpos\Model\Shift\ShiftFactory $shiftFactory,
        \Magestore\Webpos\Model\ResourceModel\Sales\Order\Payment\CollectionFactory $orderPaymentCollectionFactory,
        \Magestore\Webpos\Model\Shift\CashTransactionFactory $cashTransactionFactory,
        \Magestore\Webpos\Model\Pos\PosRepository $posRepository,
        \Magestore\Appadmin\Model\Staff\StaffFactory $staffFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
    )
    {
        $this->_shiftFactory = $shiftFactory;
        $this->_orderPaymentCollectionFactory = $orderPaymentCollectionFactory;
        $this->_cashTransactionFactory = $cashTransactionFactory;
        $this->posRepository = $posRepository;
        $this->staffFactory = $staffFactory;
        $this->localeDate = $localeDate;

        parent::__construct($context);
    }

    /**
     * @param \Magestore\Webpos\Model\Shift\Shift $shiftModel
     * @return mixed
     */
    public function getShiftData($shiftModel)
    {
        $shiftData = $shiftModel->getData();
        $shiftData["sale_summary"] = $this->getSaleSummaryByShiftIncrementId($shiftModel->getShiftIncrementId());
        $shiftData["cash_transaction"] = $this->getCashTransaction($shiftModel);

        $shiftData["staff_name"] = $this->getStaffName($shiftModel);
        $shiftData["pos_name"] = $this->getPosName($shiftModel);

        return $shiftData;
    }

    /**
     * @param \Magestore\Webpos\Model\Shift\Shift $shiftModel
     * @return mixed
     */
    public function getShiftDataForAdmin($shiftModel)
    {
        $result = $this->getShiftData($shiftModel);
        if ($result['cash_transaction'] && !empty($result['cash_transaction'])) {
            $transactions = [];
            foreach ($result['cash_transaction'] as $transaction) {
                $transactions[] = $transaction->getData();
            }
            $result['cash_transaction'] = $transactions;
        }
        if ($result['sale_summary'] && !empty($result['sale_summary'])) {
            $saleSummary = [];
            foreach ($result['sale_summary'] as $sale) {
                $saleSummary[] = $sale->getData();
            }
            $result['sale_summary'] = $saleSummary;
        }
        if ($result['opened_at']) {
            $result['opened_at'] = $this->formatDate($result['opened_at'], \IntlDateFormatter::MEDIUM, true);
        }
        if ($result['updated_at']) {
            $result['updated_at'] = $this->formatDate($result['updated_at'], \IntlDateFormatter::MEDIUM, true);
        }
        if ($result['closed_at']) {
            $result['closed_at'] = $this->formatDate($result['closed_at'], \IntlDateFormatter::MEDIUM, true);
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $location = $objectManager->create('Magestore\Webpos\Model\Location\Location')->load($result['location_id']);
        if ($location->getId()) {
            $addressArray = [];
            if ($location->getStreet()) {
                $addressArray[] = $location->getStreet();
            }
            if ($location->getRegion()) {
                $addressArray[] = $location->getRegion();
            }
            if ($location->getCity()) {
                $addressArray[] = $location->getCity();
            }
            if ($location->getPostcode()) {
                $addressArray[] = $location->getPostcode();
            }
            if ($location->getCountry()) {
                $addressArray[] = $location->getCountry();
            }
            $result['location_address'] = implode(', ', $addressArray);
        }
        return $result;
    }

    /**
     * @param \Magestore\Webpos\Model\Shift\Shift $shift
     * @return null|string
     */
    public function getStaffName($shift)
    {
        return $this->staffFactory->create()->load($shift->getStaffId())->getName();
    }

    /**
     * @param \Magestore\Webpos\Model\Shift\Shift $shift
     * @return null|string
     * @throws \Exception
     */
    public function getPosName($shift)
    {
        return $this->posRepository->getById($shift->getPosId())->getPosName();
    }

    /**
     * @param \Magestore\Webpos\Model\Shift\Shift $shift
     * @return null|string
     * @throws \Exception
     */
    public function getStoreId($shift)
    {
        return $this->posRepository->getById($shift->getPosId())->getStoreId();
    }

    /**
     * @param $shiftIncrementId
     * @return mixed
     */
    public function getShiftDataByIncrementId($shiftIncrementId)
    {
        $shiftModel = $this->_shiftFactory->create();
        $shiftModel->load($shiftIncrementId, "shift_increment_id");
        return $this->getShiftData($shiftModel);
    }

    /**
     * @param \Magestore\Webpos\Model\Shift\Shift $shift
     * @return mixed
     */
    public function getCashTransaction($shift)
    {
        /** @var \Magestore\Webpos\Model\Shift\CashTransaction $cashTransactionModel */
        $cashTransactionModel = $this->_cashTransactionFactory->create();
        return $cashTransactionModel->getByShiftIncrementId($shift->getShiftIncrementId());
    }

    /**
     * get sales summary for a shift has $shiftIncrementId
     * @param string $shiftIncrementId
     * @return mixed
     */
    public function getSaleSummaryByShiftIncrementId($shiftIncrementId)
    {
        $orderPaymentCollection = $this->_orderPaymentCollectionFactory->create();
        $orderPaymentCollection->addFieldToFilter("shift_increment_id", $shiftIncrementId);
        $orderPayments = [];
        if ($orderPaymentCollection->getSize()) {
            foreach ($orderPaymentCollection as $orderPayment) {
                $orderPayments[] = $orderPayment;
            }
        }
        return $orderPayments;
    }

    /**
     * @return string
     */
    public function getSessionSinceDay()
    {
        $type = $this->getStoreConfig('webpos/session/session_since');
        $time = time();
        switch ($type) {
            case Since::SINCE_24H:
                $lastTime = $time - 60 * 60 * 24 * 1;
                return date('Y-m-d H:i:s', $lastTime);
            case Since::SINCE_7DAYS:
                $lastTime = $time - 60 * 60 * 24 * 7;
                return date('Y-m-d H:i:s', $lastTime);
            case Since::SINCE_MONTH:
                return date('Y-m-01 00:00:00');
            case Since::SINCE_YTD:
                return date('Y-01-01 00:00:00');
            case Since::SINCE_2YTD:
                $year = date("Y") - 1;
                return date($year . '-01-01 00:00:00');
            default:
                $lastTime = $time - 60 * 60 * 24 * 7;
                return date('Y-m-d H:i:s', $lastTime);
        }
    }

    /**
     *
     * @param string $path
     * @return string
     */
    public function getStoreConfig($path)
    {
        return $this->scopeConfig->getValue($path);
    }

    /**
     * Retrieve formatting date
     *
     * @param null|string|\DateTimeInterface $date
     * @param int $format
     * @param bool $showTime
     * @param null|string $timezone
     * @return string
     */
    public function formatDate(
        $date = null,
        $format = \IntlDateFormatter::SHORT,
        $showTime = false,
        $timezone = null
    )
    {
        return $this->localeDate->formatDateTime(
            $this->localeDate->date(new \DateTime($date)),
            $format,
            $showTime ? $format : \IntlDateFormatter::NONE,
            null,
            $timezone
        );
    }
}
