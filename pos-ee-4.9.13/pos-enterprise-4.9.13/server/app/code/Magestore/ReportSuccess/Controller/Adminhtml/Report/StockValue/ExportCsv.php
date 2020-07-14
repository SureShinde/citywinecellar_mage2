<?php

/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */
namespace Magestore\ReportSuccess\Controller\Adminhtml\Report\StockValue;

/**
 * Class ExportCsv
 * @package Magestore\ReportSuccess\Controller\Adminhtml\Report\StockValue
 */
class ExportCsv extends \Magestore\ReportSuccess\Controller\Adminhtml\Report\AbstractExport {
    /**
     * @var string
     */
    protected $fileName = 'Stockvalue_report_';
    /**
     * @var string
     */
    protected $converterClassName = 'Magestore\ReportSuccess\Model\Export\StockValue\ConvertToCsv';
}