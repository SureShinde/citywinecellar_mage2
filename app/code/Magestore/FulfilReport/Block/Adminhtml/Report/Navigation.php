<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilReport\Block\Adminhtml\Report;

/**
 * Block Navigation
 */
class Navigation extends \Magento\Backend\Block\Template
{
    /**
     * Report list
     *
     * @var array
     */
    protected $reportList;

    /**
     * @var \Magestore\FulfilSuccess\Api\FulfilManagementInterface
     */
    protected $fulfilManagement;

    /**
     * Navigation constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magestore\FulfilSuccess\Api\FulfilManagementInterface $fulfilManagement
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magestore\FulfilSuccess\Api\FulfilManagementInterface $fulfilManagement,
        array $data = []
    ) {
        $this->fulfilManagement = $fulfilManagement;
        parent::__construct($context, $data);
    }

    /**
     * Get a list of staff report controllers and names
     *
     * @return array
     */
    public function getStaffReportList()
    {
        return [
            'fulfilstaff' => __('Fulfilment by Staff'),
            'fulfilstaffdaily' => __('Fulfilment by Staff (Daily)')
        ];
    }

    /**
     * Get a list of location report controllers and names
     *
     * @return array
     */
    public function getWarehouseReportList()
    {
        $isMSIEnable = $this->isMSIEnable();
        return [
            'fulfilwarehouse' => __('Fulfilment by %1', $isMSIEnable ? 'Source' : 'Warehouse'),
            'fulfilwarehousedaily' => __('Fulfilment by %1 (Daily)', $isMSIEnable ? 'Source' : 'Warehouse')
        ];
    }

    /**
     * Get report list
     *
     * @return array
     */
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
     * Get report link from name
     *
     * @param string $controller
     * @return string
     */
    public function getReportLink($controller)
    {
        $path = 'fulfilreport/report_' . $controller;
        return $this->getUrl($path, ['_forced_secure' => $this->getRequest()->isSecure()]);
    }

    /**
     * Get current report name
     *
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

    /**
     * Is MSI Enable
     *
     * @return bool
     */
    public function isMSIEnable()
    {
        return $this->fulfilManagement->isMSIEnable();
    }
}
