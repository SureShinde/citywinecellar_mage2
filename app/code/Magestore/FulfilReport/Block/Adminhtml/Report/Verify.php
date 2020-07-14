<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilReport\Block\Adminhtml\Report;

use Magestore\FulfilSuccess\Api\Data\PickRequestInterface;

class Verify extends Dashboard
{

    public function getVerifyRequestCollection()
    {
        $collection = $this->getOrders()->addFieldToFilter('is_verified', ['eq' => '1']);
        return $collection;
    }

    /**
     * 
     * @return array
     */
    public function getVerifyRequestCollectionPerDay()
    {
        $dataPost = $this->request->getPost();
        $timeRange = $dataPost['time'];
        $totalVerifiedOrders = [];
        if (!isset($dataPost['type']) || !$dataPost['type']) {
            $totalVerifiedOrders = $this->getVerifiedOrdersInPeriod('last7days');
        }
        if (isset($dataPost['type']) && $dataPost['type'] == 'perday') {
            $totalVerifiedOrders = $this->getVerifiedOrdersInPeriod($timeRange);
        }
        if ($dataPost['type'] == 'customweek') {
            $orderCustomWeek = $this->getVerifiedOrdersPerDayCustomRange($dataPost['datefrom'], $dataPost['dateto']);
            return $orderCustomWeek;
        }

        return $totalVerifiedOrders;
    }

    /**
     * @return array
     */
    public function getVerifiedOrdersInPeriod($timeRange)
    {
        $totalVerifiedOrders = [];
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
                $verifyRequestsPerDay = $this->getVerifyRequestCollection()
                    ->addFieldToFilter(
                        'updated_at', ['from' => $fromDate, 'to' => $toDate]
                    )
                    ->getSize();
                $totalVerifiedOrders[$date] = $verifyRequestsPerDay;
            };            
        }

        return $totalVerifiedOrders;
    }

    /**
     * @return array
     */
    public function getVerifiedOrdersPerDayCustomRange($dateFrom, $dateTo)
    {
        $today = date('Y-m-d 23:59:59', strtotime($this->date->gmtDate()));
        $fromDate = date('Y-m-d 00:00:00', strtotime($dateFrom));
        $toDate = date('Y-m-d 23:59:59', strtotime($dateTo));
        $dateDiffToday = strtotime($today) - strtotime($toDate);
        $dateDiffCustomRange = strtotime($toDate) - strtotime($fromDate);
        $dayToday = floor(($dateDiffToday) / (60 * 60 * 24));
        $toDateFromNow = floor(($dateDiffCustomRange) / (60 * 60 * 24));
        $totalVerifiedOrders = array();
        for ($i = $toDateFromNow; $i >= 0; $i--) {
            $j = $dayToday + $i;
            $toDate = date('Y-m-d 23:59:59', strtotime("-{$j} days"));
            $fromDate = date('Y-m-d 00:00:00', strtotime("-{$j} days"));
            $date = date('d/m', strtotime("-{$j} days"));
            $pickRequestsPerDay = $this->getVerifyRequestCollection()
                ->addFieldToFilter(
                    'updated_at', ['from' => $fromDate, 'to' => $toDate]
                )
                ->getSize();
            $totalVerifiedOrders[$date] = $pickRequestsPerDay;
        }
        return $totalVerifiedOrders;
    }
}