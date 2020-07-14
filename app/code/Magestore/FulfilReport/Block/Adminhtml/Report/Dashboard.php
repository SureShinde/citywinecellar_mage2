<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magestore\FulfilReport\Block\Adminhtml\Report;

use Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\Grid\Collection;

class Dashboard extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $_orderCollectionFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $_orders;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        array $data = []
    )
    {
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_storeManager = $context->getStoreManager();
        $this->urlBuilder = $context->getUrlBuilder();
        $this->request = $request;
        $this->date = $date;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * @return Collection
     */
    public function getOrders()
    {
        $this->_orders = $this->_orderCollectionFactory->create()->addFieldToSelect("entity_id");
        return $this->_orders;
    }

    /**
     * @return Collection
     */
    public function getNewOrders()
    {
        $orders = $this->getOrderCollection()->addFieldToFilter('state', ['eq' => 'new'])->getSize();
        return $orders;
    }

    /**
     * @return Collection
     */
    public function getHoldOrders()
    {
        $orders = $this->getOrderCollection()->addFieldToFilter('state', ['eq' => 'holded'])->getSize();
        return $orders;
    }

    /**
     * @return Collection
     */
    public function getCancelOrders()
    {
        $orders = $this->getOrderCollection()->addFieldToFilter('state', ['eq' => 'canceled'])->getSize();
        return $orders;
    }

    /**
     * @return Collection
     */
    public function getVerifiedOrders()
    {
        $orders = $this->getOrderCollection()->addFieldToFilter('is_verified', ['eq' => '1'])->getSize();
        return $orders;
    }

    /**
     * @return Collection
     */
    public function getProcessingOrders()
    {
        $orders = $this->getOrderCollection()->addFieldToFilter('state', ['eq' => 'processing'])->getSize();
        return $orders;
    }

    /**
     * @return string
     */
    public function getOrderStatusUrl()
    {
        $url = $this->urlBuilder->getUrl('fulfilreport/report/status');
        return $url;
    }

    /**
     * @return string
     */
    public function getOrderPerDayUrl()
    {
        $url = $this->urlBuilder->getUrl('fulfilreport/report/perday');
        return $url;
    }

    /**
     * @return string
     */
    public function getShipmentCarrierUrl()
    {
        $url = $this->urlBuilder->getUrl('fulfilreport/report/carrier');
        return $url;
    }

    /**
     * @return string
     */
    public function getVerifyRequestUrl()
    {
        $url = $this->urlBuilder->getUrl('fulfilreport/report/verify');
        return $url;
    }

    /**
     * @return string
     */
    public function getPickRequestUrl()
    {
        $url = $this->urlBuilder->getUrl('fulfilreport/report/pick');
        return $url;
    }

    /**
     * @return string
     */
    public function getPackRequestUrl()
    {
        $url = $this->urlBuilder->getUrl('fulfilreport/report/pack');
        return $url;
    }

    /**
     * @return Collection
     */
    public function getOrderCollection()
    {
        $toDate = $this->date->gmtDate();
        $dataPost = $this->request->getPost();
        $timeRange = $dataPost['time'];
        $fromDate = date('Y-m-d 00:00:00', strtotime('-0 days'));
        $orderCollection = $this->getOrders()->addAttributeToFilter(
            'created_at',
            [
                'from' => $fromDate,
                'to' => $toDate
            ]
        );
        if ($timeRange == 'yesterday') {
            $toDate = date('Y-m-d 23:59:59', strtotime('-1 days'));
            $fromDate = date('Y-m-d', strtotime('-2 days'));
            $orderCollection = $this->getOrders()->addAttributeToFilter(
                'created_at',
                [
                    'from' => $fromDate,
                    'to' => $toDate
                ]
            );
        }
        if ($timeRange == 'last7days') {
            $toDate = $this->date->gmtDate();
            $fromDate = date('Y-m-d 00:00:00', strtotime('-7 days'));
            $orderCollection = $this->getOrders()->addAttributeToFilter(
                'created_at',
                [
                    'from' => $fromDate,
                    'to' => $toDate
                ]
            );
        }
        if ($timeRange == 'last30days') {
            $toDate = $this->date->gmtDate();
            $fromDate = date('Y-m-d 00:00:00', strtotime('-30 days'));
            $orderCollection = $this->getOrders()->addAttributeToFilter(
                'created_at',
                [
                    'from' => $fromDate,
                    'to' => $toDate
                ]
            );
        }
        if (isset($dataPost['dateto'])) {
            $fromDate = date('Y-m-d 00:00:00', strtotime($dataPost['datefrom']));
            $toDate = date('Y-m-d 23:59:59', strtotime($dataPost['dateto']));
            $orderCollection = $this->getOrders()->addAttributeToFilter(
                'created_at',
                [
                    'from' => $fromDate,
                    'to' => $toDate
                ]
            );
        }

        return $orderCollection;
    }

    /**
     * @return array
     */
    public function getOrderCollectionPerDay()
    {
        $dataPost = $this->request->getPost();
        $timeRange = $dataPost['time'];
        $totalOrder = [];
        for ($i = 6; $i >= 0; $i--) {
            $toDate = date('Y-m-d 23:59:59', strtotime("-{$i} days"));
            $fromDate = date('Y-m-d 00:00:00', strtotime("-{$i} days"));
            $date = date('d/m', strtotime("-{$i} days"));
            $orderPerday = $this->getOrders()
                ->addAttributeToFilter('created_at', array('from' => $fromDate, 'to' => $toDate))->getSize();
            $totalOrder[$date] = $orderPerday;
        };
        if (!isset($dataPost['type']) && !$dataPost['type']) {
            $totalOrder = $this->getOrderInPeriod('last7days');
        }        
        if (isset($dataPost['type']) && $dataPost['type'] == 'perday') {
            $totalOrder = $this->getOrderInPeriod($timeRange);
        }
        if ($dataPost['type'] == 'customweek') {
            $orderCustomWeek = $this->getOrderPerDayCustomRange($dataPost['datefrom'], $dataPost['dateto']);
            return $orderCustomWeek;
        }

        return $totalOrder;
    }

    /**
     * @return array
     */
    public function getOrderInPeriod($timeRange)
    {
        $totalOrder = [];
        $lastIndex = 0;
        switch ($timeRange){
            case 'last7days':
                $lastIndex = 6;
                break;
            case 'last14days':
                $lastIndex = 13;
                break;
            case 'last30days':
                $lastIndex = 29;
                break;
            default:
                $lastIndex = 6;
        }       
        if($lastIndex) {
            for ($i = $lastIndex; $i >= 0; $i--) {
                $toDate = date('Y-m-d 23:59:59', strtotime("-{$i} days"));
                $fromDate = date('Y-m-d 00:00:00', strtotime("-{$i} days"));
                $date = date('d/m', strtotime("-{$i} days"));
                $orderPerday = $this->getOrders()
                    ->addAttributeToFilter('created_at', array('from' => $fromDate, 'to' => $toDate))->getSize();
                $totalOrder[$date] = $orderPerday;
            };            
        }        
        return $totalOrder;
    }

    /**
     * @return array
     */
    public function getOrderPerDayCustomRange($dateFrom, $dateTo)
    {
        $today = date('Y-m-d 23:59:59', strtotime($this->date->gmtDate()));
        $fromDate = date('Y-m-d 00:00:00', strtotime($dateFrom));
        $toDate = date('Y-m-d 23:59:59', strtotime($dateTo));
        $dateDiffToday = strtotime($today) - strtotime($toDate);
        $dateDiffCustomRange = strtotime($toDate) - strtotime($fromDate);
        $dayToday = floor(($dateDiffToday) / (60 * 60 * 24));
        $toDateFromNow = floor(($dateDiffCustomRange) / (60 * 60 * 24));
        $orderCustomWeek = array();
        for ($i = $toDateFromNow; $i >= 0; $i--) {
            $j = $dayToday + $i;
            $toDate = date('Y-m-d 23:59:59', strtotime("-{$j} days"));
            $fromDate = date('Y-m-d 00:00:00', strtotime("-{$j} days"));
            $date = date('d/m', strtotime("-{$j} days"));
            $orderPerday = $this->getOrders()
                ->addAttributeToFilter('created_at', array('from' => $fromDate, 'to' => $toDate))->getSize();
            $orderCustomWeek[$date] = $orderPerday;
        }
        return $orderCustomWeek;
    }

    /**
     * @return Collection
     */
    public function getShipmentCollection()
    {
        $shipmentCollection = $this->_objectManager
            ->create('\Magento\Shipping\Model\ResourceModel\Order\Track\Collection')
            ->addFieldToSelect(['carrier_code', 'order_id', 'track_number', 'created_at']);
        return $shipmentCollection;
    }

    /**
     * @return Collection
     */
    public function getShipmentCollectionBefore($day)
    {
        $toDate = $this->date->gmtDate();
        $fromDate = date('Y-m-d 00:00:00', strtotime("-{$day} days"));
        $shipmentCollection = $this->_objectManager
            ->create('\Magento\Shipping\Model\ResourceModel\Order\Track\Collection')
            ->addAttributeToFilter('created_at', array('from' => $fromDate, 'to' => $toDate))
            ->addFieldToSelect(['carrier_code', 'order_id', 'track_number', 'created_at']);
        return $shipmentCollection;
    }

    /**
     * @return array
     */
    public function getOrderCollectionByCarrier()
    {
        $carrier = array();
        $dataPost = $this->request->getPost();
        $day = 0;
        if (isset($dataPost['time'])) {
            if ($dataPost['time'] == 'yesterday')
                $day = 1;
            if ($dataPost['time'] == 'last7days')
                $day = 7;
            if ($dataPost['time'] == 'last30days')
                $day = 30;
        }

        // Get carrier collection before x days
        $totalCarriers = $this->getShipmentCollectionBefore($day)->getSize();
        $upsCarrier = $this->getShipmentCollectionBefore($day)
            ->addFieldToFilter('carrier_code', array('eq' => 'ups'))->getSize();
        $uspsCarrier = $this->getShipmentCollectionBefore($day)
            ->addFieldToFilter('carrier_code', array('eq' => 'usps'))->getSize();
        $dhlCarrier = $this->getShipmentCollectionBefore($day)
            ->addFieldToFilter('carrier_code', array('eq' => 'dhl'))->getSize();
        $fedexCarrier = $this->getShipmentCollectionBefore($day)
            ->addFieldToFilter('carrier_code', array('eq' => 'fedex'))->getSize();
        $customCarrier = $this->getShipmentCollectionBefore($day)
            ->addFieldToFilter('carrier_code', array('eq' => 'custom'))->getSize();

        if (isset($dataPost['type']) && $dataPost['type'] == 'customcarrier') {
            $fromDate = date('Y-m-d 00:00:00', strtotime($dataPost['datefrom']));
            $toDate = date('Y-m-d 23:59:59', strtotime($dataPost['dateto']));
            $carrier = $this->calculateCarrierTimeRange($fromDate, $toDate);
            return $carrier;
        }
        // Calculate percentage of carriers
        if ($upsCarrier)
            $carrier['UPS'] = $upsCarrier / $totalCarriers * 100;
        if ($uspsCarrier)
            $carrier['USPS'] = $uspsCarrier / $totalCarriers * 100;
        if ($dhlCarrier)
            $carrier['DHL'] = $dhlCarrier / $totalCarriers * 100;
        if ($fedexCarrier)
            $carrier['FedEx'] = $fedexCarrier / $totalCarriers * 100;
        if ($customCarrier)
            $carrier['Custom'] = $customCarrier / $totalCarriers * 100;
        return $carrier;
    }

    /**
     * @return array
     */
    public function calculateCarrierTimeRange($fromDate, $toDate)
    {
        $carrier = array();
        // Get carrier collection by time range
        $totalCarriers = $this->getShipmentCollection()
            ->addAttributeToFilter('created_at', array('from' => $fromDate, 'to' => $toDate))->getSize();
        $upsCarrier = $this->getShipmentCollection()
            ->addAttributeToFilter('created_at', array('from' => $fromDate, 'to' => $toDate))
            ->addFieldToFilter('carrier_code', array('eq' => 'ups'))->getSize();
        $uspsCarrier = $this->getShipmentCollection()
            ->addAttributeToFilter('created_at', array('from' => $fromDate, 'to' => $toDate))
            ->addFieldToFilter('carrier_code', array('eq' => 'usps'))->getSize();
        $dhlCarrier = $this->getShipmentCollection()
            ->addAttributeToFilter('created_at', array('from' => $fromDate, 'to' => $toDate))
            ->addFieldToFilter('carrier_code', array('eq' => 'dhl'))->getSize();
        $fedexCarrier = $this->getShipmentCollection()
            ->addAttributeToFilter('created_at', array('from' => $fromDate, 'to' => $toDate))
            ->addFieldToFilter('carrier_code', array('eq' => 'fedex'))->getSize();
        $customCarrier = $this->getShipmentCollection()
            ->addAttributeToFilter('created_at', array('from' => $fromDate, 'to' => $toDate))
            ->addFieldToFilter('carrier_code', array('eq' => 'custom'))->getSize();

        // Calculate percentage of carriers
        if ($upsCarrier)
            $carrier['UPS'] = $upsCarrier / $totalCarriers * 100;
        if ($uspsCarrier)
            $carrier['USPS'] = $uspsCarrier / $totalCarriers * 100;
        if ($dhlCarrier)
            $carrier['DHL'] = $dhlCarrier / $totalCarriers * 100;
        if ($fedexCarrier)
            $carrier['FedEx'] = $fedexCarrier / $totalCarriers * 100;
        if ($customCarrier)
            $carrier['Custom'] = $customCarrier / $totalCarriers * 100;
        return $carrier;
    }

}


