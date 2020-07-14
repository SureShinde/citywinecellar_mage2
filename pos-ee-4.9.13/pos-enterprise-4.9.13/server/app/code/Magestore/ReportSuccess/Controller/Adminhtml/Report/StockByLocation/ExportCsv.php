<?php

/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */
namespace Magestore\ReportSuccess\Controller\Adminhtml\Report\StockByLocation;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magestore\ReportSuccess\Model\Export\AbstractConvertToCsv;

/**
 * Class ExportCsv
 * @package Magestore\ReportSuccess\Controller\Adminhtml\Report\StockByLocation
 */
class ExportCsv extends \Magestore\ReportSuccess\Controller\Adminhtml\Report\AbstractExport {
    /**
     * @var string
     */
    protected $fileName = 'Stockbywarehouse_report_';
    /**
     * @var string
     */
    protected $converterClassName = 'Magestore\ReportSuccess\Model\Export\StockByLocation\ConvertToCsv';
    /**
     * @var \Magestore\ReportSuccess\Model\Bookmark
     */
    protected $bookmark;

    /**
     * ExportCsv constructor.
     * @param Context $context
     * @param AbstractConvertToCsv $converter
     * @param FileFactory $fileFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magestore\ReportSuccess\Model\Bookmark $bookmark
     */
    public function __construct(
        Context $context,
        AbstractConvertToCsv $converter,
        FileFactory $fileFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magestore\ReportSuccess\Model\Bookmark $bookmark
    )
    {
        $this->bookmark = $bookmark;
        $metric = $this->bookmark->getMetric();
        $metric = str_replace(' ', '', ucwords(str_replace('_', ' ', $metric)));
        $this->fileName = $metric. '_bywarehouse_report_';
        parent::__construct($context, $converter, $fileFactory, $localeDate);
    }
}