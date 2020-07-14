<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Cron\HistoricalReport;

    /**
     * Class GenerateFile
     * @package Magestore\ReportSuccess\Cron\HistoricalReport
     */
/**
 * Class GenerateFileManually
 * @package Magestore\ReportSuccess\Cron\HistoricalReport
 */
class GenerateFileManually
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magestore\ReportSuccess\Model\HistoricalReport\ConvertToCsv
     */
    protected $convertToCsv;

    /**
     * @var \Magestore\ReportSuccess\Model\ResourceModel\CronManual\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magestore\ReportSuccess\Api\ReportManagementInterface
     */
    protected $reportManagement;

    /**
     * GenerateFileManually constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magestore\ReportSuccess\Model\HistoricalReport\ConvertToCsv $convertToCsv
     * @param \Magestore\ReportSuccess\Model\ResourceModel\CronManual\CollectionFactory $collectionFactory
     * @param \Magestore\ReportSuccess\Api\ReportManagementInterface $reportManagement
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\ReportSuccess\Model\HistoricalReport\ConvertToCsv $convertToCsv,
        \Magestore\ReportSuccess\Model\ResourceModel\CronManual\CollectionFactory $collectionFactory,
        \Magestore\ReportSuccess\Api\ReportManagementInterface $reportManagement
    )
    {
        $this->objectManager = $objectManager;
        $this->convertToCsv = $convertToCsv;
        $this->collectionFactory = $collectionFactory;
        $this->reportManagement = $reportManagement;
    }


    /**
     * @return $this
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $cronManualCollection = $this->collectionFactory->create();
        foreach ($cronManualCollection as $cronManual) {
            $locationCode = $cronManual->getLocationCode();
            $cronManual->delete();
            if ($locationCode !== 'all') {
                if ($this->reportManagement->isMSIEnable()) {
                    try {
                        /** @var \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository */
                        $sourceRepository = $this->objectManager->get('Magento\InventoryApi\Api\SourceRepositoryInterface');
                        $source = $sourceRepository->get($locationCode);
                        $this->convertToCsv->getCsvFile($source, true);
                    } catch (\Exception $e) {
                        return $this;
                    }
                } else {
                    /** @var \Magestore\InventorySuccess\Model\Warehouse $warehouse */
                    $warehouse = $this->objectManager->create('Magestore\InventorySuccess\Model\Warehouse')
                        ->load($locationCode, 'warehouse_code');
                    if ($warehouse->getWarehouseId()) {
                        $this->convertToCsv->getCsvFile($warehouse, true);
                    }
                }
            } else {
                $this->convertToCsv->getCsvFile(null, true);
            }
        }
        return $this;
    }
}