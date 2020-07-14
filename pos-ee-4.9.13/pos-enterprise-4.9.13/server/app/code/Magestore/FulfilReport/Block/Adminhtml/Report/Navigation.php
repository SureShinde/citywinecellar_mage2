<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilReport\Block\Adminhtml\Report;

class Navigation extends \Magento\Backend\Block\Template
{
    /**
     * report list
     *
     * @var array
     */
    protected $reportList;

    /**
     * @var \Magestore\FulfilSuccess\Api\FulfilManagementInterface
     */
    protected $fulfilManagement;


    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magestore\FulfilSuccess\Api\FulfilManagementInterface $fulfilManagement,
        array $data = []
    )
    {
        $this->fulfilManagement = $fulfilManagement;
        parent::__construct($context, $data);
    }


    /**
     * get a list of staff report controllers and names
     *
     * @return array
     */
    public function getStaffReportList()
    {

        return array(
            'fulfilstaff' => __('Fulfilment by Staff'),
            'fulfilstaffdaily' => __('Fulfilment by Staff (Daily)')
        );
    }

    /**
     * get a list of location report controllers and names
     *
     * @return array
     */
    public function getWarehouseReportList()
    {
        $isMSIEnable = $this->isMSIEnable();
        return array(
            'fulfilwarehouse' => __('Fulfilment by %1', $isMSIEnable ? 'Source' : 'Warehouse'),
            'fulfilwarehousedaily' => __('Fulfilment by %1 (Daily)', $isMSIEnable ? 'Source' : 'Warehouse')
        );
    }

    public function getReportList()
    {
        if (!$this->reportList) {
            $this->reportList = array_merge(
                $this->getStaffReportList(),
                $this->getWarehouseReportList()
            );
        }
        return $this->reportList;
    }

    /**
     * get report link from name
     *
     * @param string
     * @return string
     */
    public function getReportLink($controller)
    {
        $path = 'fulfilreport/report_' . $controller;
        return $this->getUrl($path, array('_forced_secure' => $this->getRequest()->isSecure()));
    }

    /**
     * get current report name
     *
     * @param
     * @return string
     */
    public function getCurrentReportName()
    {
        $controller = $this->getRequest()->getControllerName();
        $controller = str_replace('report_', '', $controller);
        $reportList = $this->getReportList();
        $reportName = '';
        if (isset($reportList[$controller])) {
            $reportName = $reportList[$controller];
        }
        return $reportName;
    }

    public function isMSIEnable()
    {
        return $this->fulfilManagement->isMSIEnable();
    }
}