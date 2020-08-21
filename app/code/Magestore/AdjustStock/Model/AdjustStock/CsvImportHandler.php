<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Model\AdjustStock;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magestore\AdjustStock\Api\Data\AdjustStock\AdjustStockInterface;
use Magestore\AdjustStock\Api\Data\AdjustStock\ProductInterface;
use Magestore\AdjustStock\Model\ResourceModel\AdjustStock\GlobalStock\CollectionFactory;

/**
 * Class CsvImportHandler
 *
 * Used to import csv
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class CsvImportHandler
{
    /**
     * CSV Processor
     *
     * @var \Magento\Framework\File\Csv
     */
    protected $csvProcessor;

    /**
     * @var \Magestore\InventorySuccess\Model\AdjustStockFactory
     */
    protected $adjustStockFactory;

    /**
     * @var \Magestore\InventorySuccess\Api\AdjustStock\AdjustStockManagementInterface
     */
    protected $adjustStockManagement;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * @var \Magento\Framework\Filesystem\File\WriteFactory
     */
    protected $fileWriteFactory;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $driverFile;

    /**
     * @var \Magestore\AdjustStock\Api\MultiSourceInventory\SourceManagementInterface
     */
    protected $sourceManagement;

    /**
     * CsvImportHandler constructor.
     *
     * @param \Magento\Framework\File\Csv $csvProcessor
     * @param \Magestore\AdjustStock\Model\AdjustStockFactory $adjustStockFactory
     * @param \Magestore\AdjustStock\Api\AdjustStock\AdjustStockManagementInterface $adjustStockManagement
     * @param \Magestore\AdjustStock\Api\MultiSourceInventory\SourceManagementInterface $sourceManagement
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Filesystem $filesystem
     * @param CollectionFactory $productCollectionFactory
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Magento\Framework\Filesystem\File\WriteFactory $fileWriteFactory
     * @param \Magento\Framework\Filesystem\Driver\File $driverFile
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\File\Csv $csvProcessor,
        \Magestore\AdjustStock\Model\AdjustStockFactory $adjustStockFactory,
        \Magestore\AdjustStock\Api\AdjustStock\AdjustStockManagementInterface $adjustStockManagement,
        \Magestore\AdjustStock\Api\MultiSourceInventory\SourceManagementInterface $sourceManagement,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Filesystem $filesystem,
        CollectionFactory $productCollectionFactory,
        \Magento\Backend\Model\Session $backendSession,
        \Magento\Framework\Filesystem\File\WriteFactory $fileWriteFactory,
        \Magento\Framework\Filesystem\Driver\File $driverFile
    ) {
        $this->csvProcessor = $csvProcessor;
        $this->adjustStockFactory = $adjustStockFactory;
        $this->adjustStockManagement = $adjustStockManagement;
        $this->request = $request;
        $this->filesystem = $filesystem;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->backendSession = $backendSession;
        $this->driverFile = $driverFile;
        $this->fileWriteFactory = $fileWriteFactory;
        $this->sourceManagement = $sourceManagement;
    }

    /**
     * Import from csv file
     *
     * @param string $file
     * @param int $importImmediately
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function importFromCsvFile($file, $importImmediately = 0)
    {
        if (!isset($file['tmp_name'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file upload attempt.'));
        }
        $importProductRawData = $this->csvProcessor->getData($file['tmp_name']);
        $fileFields = $importProductRawData[0];
        $validFields = $this->_filterFileFields($fileFields);
        $invalidFields = array_diff_key($fileFields, $validFields);
        $importProductData = $this->_filterImportProductData($importProductRawData, $invalidFields, $validFields);
        $adjustStock = $this->adjustStockFactory->create();
        if ($this->request->getParam('id')) {
            $adjustStock = $adjustStock->load($this->request->getParam('id'));
        }
        $adjustData = [];
        $invalidData = [];
        foreach ($importProductData as $rowIndex => $dataRow) {
            if ($rowIndex == 0) {
                continue;
            }
            $productSku = $dataRow[0];
            $productModel = $this->productCollectionFactory->create()
                ->addFieldToSelect('name')
                ->addFieldToFilter('sku', $productSku)
                ->setPageSize(1)->setCurPage(1)->getFirstItem();

            if ($productModel->getId() && isset($dataRow[1]) &&
                is_numeric($dataRow[1])) {
                $changeQty = floatval($dataRow[1]);
                $adjustData[AdjustStockInterface::KEY_PRODUCTS][$productModel->getId()] = [
                    ProductInterface::PRODUCT_ID => $productModel->getId(),
                    ProductInterface::PRODUCT_SKU => $productSku,
                    ProductInterface::CHANGE_QTY => $changeQty,
                    ProductInterface::PRODUCT_NAME => $productModel->getName(),
                    ProductInterface::BARCODE => $productModel->getBarcode()
                ];

                $sourceItems = $this->sourceManagement->getSourceItemsMap(
                    $productSku,
                    [$adjustStock->getData(AdjustStockInterface::SOURCE_CODE)]
                );
                if (isset($sourceItems[$adjustStock->getData(AdjustStockInterface::SOURCE_CODE)])) {
                    $sourceItem = $sourceItems[$adjustStock->getData(AdjustStockInterface::SOURCE_CODE)];
                    $adjustData[AdjustStockInterface::KEY_PRODUCTS][$productModel->getId()][ProductInterface::OLD_QTY]
                        = $sourceItem->getQuantity();
                } else {
                    $adjustData[AdjustStockInterface::KEY_PRODUCTS][$productModel->getId()][ProductInterface::OLD_QTY]
                        = 0;
                }

                $adjustData[AdjustStockInterface::KEY_PRODUCTS][$productModel->getId()][ProductInterface::NEW_QTY]
                    = $adjustData[AdjustStockInterface::KEY_PRODUCTS][$productModel->getId()][ProductInterface::OLD_QTY]
                    //phpcs:disable
                    + $adjustData[AdjustStockInterface::KEY_PRODUCTS][$productModel->getId()][ProductInterface::CHANGE_QTY];

            } else {
                $invalidData[] = $dataRow;
            }
        }

        if ($adjustStock->getId()) {
            $adjustData[AdjustStockInterface::ADJUSTSTOCK_CODE]
                = $adjustStock->getData(AdjustStockInterface::ADJUSTSTOCK_CODE);
            $adjustData[AdjustStockInterface::SOURCE_CODE] = $adjustStock->getData(AdjustStockInterface::SOURCE_CODE);
            $adjustData[AdjustStockInterface::SOURCE_NAME] = $adjustStock->getData(AdjustStockInterface::SOURCE_NAME);
            $adjustData[AdjustStockInterface::REASON] = $adjustStock->getData(AdjustStockInterface::REASON);
            $adjustData[AdjustStockInterface::CREATED_AT] = $adjustStock->getData(AdjustStockInterface::CREATED_AT);
            $adjustData[AdjustStockInterface::CREATED_BY] = $adjustStock->getData(AdjustStockInterface::CREATED_BY);
        }

        if (count($invalidData)) {
            $this->createInvalidAdjustedFile($invalidData);
        }

        $this->adjustStockManagement->createAdjustment($adjustStock, $adjustData);
        if ($importImmediately == 1) {
            $this->adjustStockManagement->complete($adjustStock);
        }
    }

    /**
     * Filter file fields (i.e. unset invalid fields)
     *
     * @param array $fileFields
     * @return string[] filtered fields
     */
    public function _filterFileFields(array $fileFields)
    {
        $filteredFields = $this->getRequiredCsvFields();
        $requiredFieldsNum = count($this->getRequiredCsvFields());
        $fileFieldsNum = count($fileFields);

        // process title-related fields that are located right after required fields with store code as field name)
        for ($index = $requiredFieldsNum; $index < $fileFieldsNum; $index++) {
            $titleFieldName = $fileFields[$index];
            $filteredFields[$index] = $titleFieldName;
        }

        return $filteredFields;
    }

    /**
     * Create adjust invalid data
     *
     * @param array $invalidData
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function createInvalidAdjustedFile($invalidData)
    {
        $this->backendSession->setData('error_import', true);
        $this->backendSession->setData('sku_invalid', count($invalidData));
        $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)->create('import');
        $filename = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)
            ->getAbsolutePath('import/import_product_to_adjuststock_invalid.csv');

        $file = $this->fileWriteFactory->create(
            $filename,
            \Magento\Framework\Filesystem\DriverPool::FILE,
            'w'
        );
        $file->close();

        $data = [
            [__('SKU'), __('QTY')]
        ];
        $data = array_merge($data, $invalidData);
        $this->csvProcessor->saveData($filename, $data);
    }

    /**
     * Get required csv fields
     *
     * @return array
     */
    public function getRequiredCsvFields()
    {
        // indexes are specified for clarity, they are used during import
        return [
            0 => __('SKU'),
            1 => __('QTY')
        ];
    }

    /**
     * Filter import product data
     *
     * @param array $productRawData
     * @param array $invalidFields
     * @param array $validFields
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function _filterImportProductData(array $productRawData, array $invalidFields, array $validFields)
    {
        $validFieldsNum = count($validFields);
        foreach ($productRawData as $rowIndex => $dataRow) {
            // skip empty rows
            if (count($dataRow) <= 1) {
                unset($productRawData[$rowIndex]);
                continue;
            }
            // unset invalid fields from data row
            foreach ($dataRow as $fieldIndex => $fieldValue) {
                if (isset($invalidFields[$fieldIndex])) {
                    unset($productRawData[$rowIndex][$fieldIndex]);
                }
            }
            // check if number of fields in row match with number of valid fields
            if (count($productRawData[$rowIndex]) != $validFieldsNum) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file format.'));
            }
        }
        return $productRawData;
    }

    /**
     * Get base dir media
     *
     * @return \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    public function getBaseDirMedia()
    {
        return $this->filesystem->getDirectoryRead('media');
    }
}
