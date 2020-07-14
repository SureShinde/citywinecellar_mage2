<?php

/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */
namespace Magestore\ReportSuccess\Controller\Adminhtml\Report\IncomingStock;

/**
 * Class ExportCsv
 * @package Magestore\ReportSuccess\Controller\Adminhtml\Report\IncomingStock
 */
class ExportCsv extends \Magestore\ReportSuccess\Controller\Adminhtml\Report\AbstractExport {
    /**
     * @var string
     */
    protected $fileName = 'incomingstock_report_';
    /**
     * @var string
     */
    protected $converterClassName = 'Magestore\ReportSuccess\Model\Export\IncomingStock\ConvertToCsv';
}