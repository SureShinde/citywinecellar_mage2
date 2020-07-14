<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilReport\Block\Adminhtml\Report;

use Magestore\FulfilSuccess\Api\Data\PackRequestInterface;

class Pack extends \Magestore\FulfilReport\Block\Adminhtml\Report\Dashboard
{
    /**
     * @var \Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequest\CollectionFactory
     */
    protected $packRequestCollectionFactory;

    /**
     * Pack constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequest\CollectionFactory $packRequestCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequest\CollectionFactory $packRequestCollectionFactory,
        array $data = []
    )
    {
        parent::__construct($context, $orderCollectionFactory,$request, $date, $data);
        $this->packRequestCollectionFactory = $packRequestCollectionFactory;
    }

    /**
     * @return \Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequest\Collection
     */
    public function getPackRequestCollection()
    {
        /** @var \Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequest\Collection $collection */
        $collection = $this->packRequestCollectionFactory->create();
        return $collection;
    }

    /**
     * 
     * @return array
     */
    public function getPackRequestCollectionPerDay()
    {
        $dataPost = $this->request->getPost();
        $timeRange = $dataPost['time'];
        $totalPackRequests = [];

        if(!isset($dataPost['type']) || !$dataPost['type']) {
            $totalPackRequests = $this->getPackRequestsInPeriod('last7days');
        }
        
        if (isset($dataPost['type']) && $dataPost['type'] == 'perday') {
            $totalPackRequests = $this->getPackRequestsInPeriod($timeRange);
        }
        if ($dataPost['type'] == 'customweek') {
            $orderCustomWeek = $this->getPackRequestsPerDayCustomRange($dataPost['datefrom'], $dataPost['dateto']);
            return $orderCustomWeek;
        }

        return $totalPackRequests;
    }

    /**
     * @return array
     */
    public function getPackRequestsInPeriod($timeRange)
    {
        $totalPackRequests = [];
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
                $packRequestsPerDay = $this->getPackRequestCollection()
                    ->addFieldToFilter(
                        'status', ['eq' => PackRequestInterface::STATUS_PACKED]
                    )
                    ->addFieldToFilter(
                        'updated_at', ['from' => $fromDate, 'to' => $toDate]
                    )
                    ->getSize();
                $totalPackRequests[$date] = $packRequestsPerDay;
            };            
        }
        
        return $totalPackRequests;
    }

    /**
     * @return array
     */
    public function getPackRequestsPerDayCustomRange($dateFrom, $dateTo)
    {
        $today = date('Y-m-d 23:59:59', strtotime($this->date->gmtDate()));
        $fromDate = date('Y-m-d 00:00:00', strtotime($dateFrom));
        $toDate = date('Y-m-d 23:59:59', strtotime($dateTo));
        $dateDiffToday = strtotime($today) - strtotime($toDate);
        $dateDiffCustomRange = strtotime($toDate) - strtotime($fromDate);
        $dayToday = floor(($dateDiffToday) / (60 * 60 * 24));
        $toDateFromNow = floor(($dateDiffCustomRange) / (60 * 60 * 24));
        $totalPackRequests = array();
        for ($i = $toDateFromNow; $i >= 0; $i--) {
            $j = $dayToday + $i;
            $toDate = date('Y-m-d 23:59:59', strtotime("-{$j} days"));
            $fromDate = date('Y-m-d 00:00:00', strtotime("-{$j} days"));
            $date = date('d/m', strtotime("-{$j} days"));
            $packRequestsPerDay = $this->getPackRequestCollection()
                ->addFieldToFilter(
                    'status', ['eq' => PackRequestInterface::STATUS_PACKED]
                )
                ->addFieldToFilter(
                    'updated_at', ['from' => $fromDate, 'to' => $toDate]
                )
                ->getSize();
            $totalPackRequests[$date] = $packRequestsPerDay;
        }
        return $totalPackRequests;
    }
}