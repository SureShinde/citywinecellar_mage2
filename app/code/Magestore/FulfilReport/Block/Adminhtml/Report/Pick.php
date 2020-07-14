<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilReport\Block\Adminhtml\Report;

use Magestore\FulfilSuccess\Api\Data\PickRequestInterface;

class Pick extends Dashboard
{
    /**
     * @var \Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\PickRequest\CollectionFactory
     */
    protected $pickRequestCollectionFactory;

    /**
     * Pick constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\PickRequest\CollectionFactory $pickRequestCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\PickRequest\CollectionFactory $pickRequestCollectionFactory,
        array $data = []
    )
    {
        parent::__construct($context, $orderCollectionFactory,$request, $date, $data);
        $this->pickRequestCollectionFactory = $pickRequestCollectionFactory;
    }
    
    /**
     * @return \Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\PickRequest\Collection
     */
    public function getPickRequestCollection()
    {
        /** @var \Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\PickRequest\Collection $collection */
        $collection = $this->pickRequestCollectionFactory->create();
        return $collection;
    }

    /**
     * 
     * @return array
     */
    public function getPickRequestCollectionPerDay()
    {
        $dataPost = $this->request->getPost();
        $timeRange = $dataPost['time'];
        $totalPickRequests = [];
        if (!isset($dataPost['type']) || !$dataPost['type']) {
            $totalPickRequests = $this->getPickRequestsInPeriod('last7days');
        }        
        if (isset($dataPost['type']) && $dataPost['type'] == 'perday') {
            $totalPickRequests = $this->getPickRequestsInPeriod($timeRange);
        }
        if ($dataPost['type'] == 'customweek') {
            $orderCustomWeek = $this->getPickRequestsPerDayCustomRange($dataPost['datefrom'], $dataPost['dateto']);
            return $orderCustomWeek;
        }

        return $totalPickRequests;
    }

    /**
     * @return array
     */
    public function getPickRequestsInPeriod($timeRange)
    {
        $totalPickRequests = [];
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
            $totalPickRequests = array();
            for ($i = $lastIndex; $i >= 0; $i--) {
                $toDate = date('Y-m-d 23:59:59', strtotime("-{$i} days"));
                $fromDate = date('Y-m-d 00:00:00', strtotime("-{$i} days"));
                $date = date('d/m', strtotime("-{$i} days"));
                $pickRequestsPerDay = $this->getPickRequestCollection()
                    ->addFieldToFilter(
                        'status', ['eq' => PickRequestInterface::STATUS_PICKED]
                    )
                    ->addFieldToFilter(
                        'updated_at', ['from' => $fromDate, 'to' => $toDate]
                    )
                    ->getSize();
                $totalPickRequests[$date] = $pickRequestsPerDay;
            };            
        }
       
        return $totalPickRequests;
    }

    /**
     * @return array
     */
    public function getPickRequestsPerDayCustomRange($dateFrom, $dateTo)
    {
        $today = date('Y-m-d 23:59:59', strtotime($this->date->gmtDate()));
        $fromDate = date('Y-m-d 00:00:00', strtotime($dateFrom));
        $toDate = date('Y-m-d 23:59:59', strtotime($dateTo));
        $dateDiffToday = strtotime($today) - strtotime($toDate);
        $dateDiffCustomRange = strtotime($toDate) - strtotime($fromDate);
        $dayToday = floor(($dateDiffToday) / (60 * 60 * 24));
        $toDateFromNow = floor(($dateDiffCustomRange) / (60 * 60 * 24));
        $totalPickRequests = array();
        for ($i = $toDateFromNow; $i >= 0; $i--) {
            $j = $dayToday + $i;
            $toDate = date('Y-m-d 23:59:59', strtotime("-{$j} days"));
            $fromDate = date('Y-m-d 00:00:00', strtotime("-{$j} days"));
            $date = date('d/m', strtotime("-{$j} days"));
            $pickRequestsPerDay = $this->getPickRequestCollection()
                ->addFieldToFilter(
                    'status', ['eq' => PickRequestInterface::STATUS_PICKED]
                )
                ->addFieldToFilter(
                    'updated_at', ['from' => $fromDate, 'to' => $toDate]
                )
                ->getSize();
            $totalPickRequests[$date] = $pickRequestsPerDay;
        }
        return $totalPickRequests;
    }
}