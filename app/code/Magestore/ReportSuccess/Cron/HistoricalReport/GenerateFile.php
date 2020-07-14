<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Cron\HistoricalReport;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Store\Model\ScopeInterface;

/**
 * Class GenerateFile
 * @package Magestore\ReportSuccess\Cron\HistoricalReport
 */
class GenerateFile
{
    /**
     * @var \Magestore\ReportSuccess\Model\HistoricalReport\ConvertToCsv
     */
    protected $convertToCsv;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var \Magestore\ReportSuccess\Model\Fs\CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * @var \Magestore\ReportSuccess\Api\ReportManagementInterface
     */
    protected $reportManagement;
    /**
     * @var Filesystem\Directory\WriteInterface
     */
    protected $directory;


    /**
     * GenerateFile constructor.
     * @param \Magestore\ReportSuccess\Model\HistoricalReport\ConvertToCsv $convertToCsv
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magestore\ReportSuccess\Model\Fs\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magestore\ReportSuccess\Api\ReportManagementInterface $reportManagement
     * @param Filesystem $filesystem
     */
    public function __construct(
        \Magestore\ReportSuccess\Model\HistoricalReport\ConvertToCsv $convertToCsv,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magestore\ReportSuccess\Model\Fs\CollectionFactory $collectionFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magestore\ReportSuccess\Api\ReportManagementInterface $reportManagement,
        Filesystem $filesystem
    )
    {
        $this->convertToCsv = $convertToCsv;
        $this->objectManager = $objectManager;
        $this->scopeConfig = $scopeConfig;
        $this->collectionFactory = $collectionFactory;
        $this->localeDate = $localeDate;
        $this->reportManagement = $reportManagement;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
    }


    /**
     * @return $this
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $currentHour = (int)$this->localeDate->date()->format('H');
        $scheduleHour = (int)$this->scopeConfig->getValue('reportsuccess/historical_stock_report/schedule_time', ScopeInterface::SCOPE_STORE);
        $duration = $this->scopeConfig->getValue('reportsuccess/historical_stock_report/duration', ScopeInterface::SCOPE_STORE);
        if ($duration && $duration != \Magestore\ReportSuccess\Model\Config\Source\Duration::LIFE_TIME) {
            $oldFiles = $this->getOldFiles();
            $oldFiles = $oldFiles->getItems();
            foreach ($oldFiles as $file) {
                $fileName = $file['filename'];
                $this->directory->delete($fileName);
            }
        }

        if ($currentHour != $scheduleHour) {
            return $this;
        }
        $this->convertToCsv->getCsvFile();
        $resources = [];
        if ($this->reportManagement->isMSIEnable()) {
            $resources = $this->objectManager->create('Magestore\InventorySuccess\Model\ResourceModel\Warehouse\Collection');
        } else {
            /** @var \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository */
            $sourceRepository = $this->objectManager->get('Magento\InventoryApi\Api\SourceRepositoryInterface');
            $resources = $sourceRepository->getList()->getItems();
        }
        foreach ($resources as $resource) {
            $this->convertToCsv->getCsvFile($resource);
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOldFiles()
    {
        $duration = $this->scopeConfig->getValue('reportsuccess/historical_stock_report/duration', ScopeInterface::SCOPE_STORE);
        $collection = $this->collectionFactory->create();
        if ($duration == \Magestore\ReportSuccess\Model\Config\Source\Duration::LAST_7_DAYS) {
            $collection->addFieldToFilter('date_object', array('lt' => gmdate('Y-m-d H:i:s', strtotime('-6 days'))));
        }
        if ($duration == \Magestore\ReportSuccess\Model\Config\Source\Duration::LAST_30_DAYS) {
            $collection->addFieldToFilter('date_object', array('lt' => gmdate('Y-m-d H:i:s', strtotime('-29 days'))));
        }
        if ($duration == \Magestore\ReportSuccess\Model\Config\Source\Duration::LAST_3_MONTHS) {
            $collection->addFieldToFilter('date_object', array('lt' => gmdate('Y-m-d H:i:s', strtotime('-3 months'))));
        }
        if ($duration == \Magestore\ReportSuccess\Model\Config\Source\Duration::LAST_6_MONTHS) {
            $collection->addFieldToFilter('date_object', array('lt' => gmdate('Y-m-d H:i:s', strtotime('-6 months'))));
        }
        if ($duration == \Magestore\ReportSuccess\Model\Config\Source\Duration::LAST_12_MONTHS) {
            $collection->addFieldToFilter('date_object', array('lt' => gmdate('Y-m-d H:i:s', strtotime('-12 months'))));
        }
        return $collection;
    }
}