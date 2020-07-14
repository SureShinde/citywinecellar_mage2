<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Block\Adminhtml\Report\Inventory\HistoricalStock;

use Magestore\ReportSuccess\Api\ReportManagementInterface;

/**
 * Class GenerateReport
 * @package Magestore\ReportSuccess\Block\Adminhtml\Report\Inventory\HistoricalStock
 */
class GenerateReport extends \Magento\Backend\Block\Template
{
    /**
     * @var ReportManagementInterface
     */
    protected $reportManagement;

    /**
     * @var \Magestore\ReportSuccess\Model\Source\Adminhtml\LocationCode
     */
    protected $locationCode;

    /**
     * GenerateReport constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param ReportManagementInterface $reportManagement
     * @param \Magestore\ReportSuccess\Model\Source\Adminhtml\LocationCode $locationCode
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magestore\ReportSuccess\Api\ReportManagementInterface $reportManagement,
        \Magestore\ReportSuccess\Model\Source\Adminhtml\LocationCode $locationCode,
        array $data = []
    )
    {
        $this->reportManagement = $reportManagement;
        $this->locationCode = $locationCode;
        parent::__construct($context, $data);
    }

    public function getSelectResourceLabel()
    {
        if ($this->reportManagement->isMSIEnable()) {
            return __('Select Source');
        }
        return __('Select Warehouse');
    }

    /**
     * @return array
     */
    public function getLocations()
    {
        return $this->locationCode->toOptionArray();
    }

}