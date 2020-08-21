<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Model\InventoryTransfer;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magestore\TransferStock\Api\Data\InventoryTransfer\InventoryTransferInterface;
use Magestore\TransferStock\Api\MultiSourceInventory\SourceManagementInterface;
use Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\GlobalStock\CollectionFactory
    as GlobalStockCollectionFactory;

/**
 * Tax Rate CSV Import Handler
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
     * @var \Magestore\TransferStock\Model\InventoryTransferFactory
     */
    protected $transferStockFactory;

    /**
     * @var \Magestore\TransferStock\Api\TransferManagementInterface
     */
    protected $transferStockManagement;

    /**
     * @var GlobalStockCollectionFactory
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
     * @var SourceManagementInterface
     */
    protected $sourceManagement;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $resourceProduct;

    /**
     * CsvImportHandler constructor.
     *
     * @param \Magento\Framework\File\Csv $csvProcessor
     * @param \Magestore\TransferStock\Model\InventoryTransferFactory $transferStockFactory
     * @param \Magestore\TransferStock\Api\TransferManagementInterface $transferStockManagement
     * @param SourceManagementInterface $sourceManagement
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Filesystem $filesystem
     * @param GlobalStockCollectionFactory $productCollectionFactory
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Magento\Framework\Filesystem\File\WriteFactory $fileWriteFactory
     * @param \Magento\Framework\Filesystem\Driver\File $driverFile
     * @param \Magento\Catalog\Model\ResourceModel\Product $resourceProduct
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\File\Csv $csvProcessor,
        \Magestore\TransferStock\Model\InventoryTransferFactory $transferStockFactory,
        \Magestore\TransferStock\Api\TransferManagementInterface $transferStockManagement,
        \Magestore\TransferStock\Api\MultiSourceInventory\SourceManagementInterface $sourceManagement,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Filesystem $filesystem,
        GlobalStockCollectionFactory $productCollectionFactory,
        \Magento\Backend\Model\Session $backendSession,
        \Magento\Framework\Filesystem\File\WriteFactory $fileWriteFactory,
        \Magento\Framework\Filesystem\Driver\File $driverFile,
        \Magento\Catalog\Model\ResourceModel\Product $resourceProduct
    ) {
        $this->csvProcessor = $csvProcessor;
        $this->transferStockFactory = $transferStockFactory;
        $this->transferStockManagement = $transferStockManagement;
        $this->request = $request;
        $this->filesystem = $filesystem;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->backendSession = $backendSession;
        $this->driverFile = $driverFile;
        $this->fileWriteFactory = $fileWriteFactory;
        $this->sourceManagement = $sourceManagement;
        $this->resourceProduct = $resourceProduct;
    }

    /**
     * Import From Csv File
     *
     * @param array $file
     * @return array
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function importFromCsvFile($file)
    {
        if (!isset($file['tmp_name'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file upload attempt.'));
        }

        if (strlen($file['name']) <= 4 || substr($file['name'], -4) != '.csv') {
            return [
                'status' => false,
                'message' => 'The file format is invalid. Please upload a CSV file again.'
            ];
        }

        $importProductRawData = $this->csvProcessor->getData($file['tmp_name']);
        $fileFields = $importProductRawData[0];
        $validFields = $this->_filterFileFields($fileFields);
        $invalidFields = array_diff_key($fileFields, $validFields);
        $importProductData = $this->_filterImportProductData($importProductRawData, $invalidFields, $validFields);
        /** @var InventoryTransferInterface $transferStock */
        $transferStock = $this->transferStockFactory->create();
        $transferStock->load($this->request->getParam('id'));
        if (!$transferStock->getInventorytransferId()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Inventory transfer with ID %1 is not found.', $this->request->getParam('id'))
            );
        }
        if (count($importProductRawData) > 1001) {
            return [
                'status' => false,
                'message' => 'The file cannot contain the number of SKU exceeding 1000. '
                    . 'Please modify and try to upload the file again.'
            ];
        }

        $transferValidData = [];
        $invalidData = [];
        $productQtyInSendingSource = $this->getProductQty($importProductData, $transferStock->getSourceWarehouseCode());
        foreach ($importProductData as $rowIndex => $dataRow) {
            if ($rowIndex == 0) {
                continue;
            }

            $productSku = $dataRow[0];
            $productModel = $this->productCollectionFactory->create()
                ->addFieldToSelect('name')
                ->addFieldToFilter('sku', $productSku)
                ->setPageSize(1)->setCurPage(1)->getFirstItem();

            if (!$this->resourceProduct->getIdBySku($productSku)) {
                $dataRow[2] = 'SKU does not exist in the system.';
                $invalidData[] = $dataRow;
                continue;
            }

            if (!$productModel->getId()) {
                $dataRow[2] = 'The system does not support uploading this type of product.';
                $invalidData[] = $dataRow;
                continue;
            }

            if (!isset($productQtyInSendingSource[$productSku])) {
                $dataRow[2] = 'SKU does not exist in the sending source.';
                $invalidData[] = $dataRow;
                continue;
            }

            if (!isset($dataRow[1]) || $dataRow[1] == null) {
                $dataRow[2] = 'Qty to Send is null.';
                $invalidData[] = $dataRow;
                continue;
            }

            if ((float)$dataRow[1] < 0) {
                $dataRow[2] = 'Qty to Send is negative.';
                $invalidData[] = $dataRow;
                continue;
            }

            if ((float)$dataRow[1] > $productQtyInSendingSource[$productSku]) {
                $dataRow[2] = 'Qty to Send is greater than Qty in sending source.';
                $invalidData[] = $dataRow;
                continue;
            }

            $transferValidData[] = [
                'id' => $productModel->getId(),
                'sku' => $productModel->getSku(),
                'name' => $productModel->getName(),
                'qty_transferred' => (float)$dataRow[1],
                'qty_received' => 0
            ];
        }

        if (count($transferValidData)) {
            $this->transferStockManagement->addProductsToInventoryTransfer(
                $transferStock->getInventorytransferId(),
                $transferValidData
            );
        }

        if (count($invalidData)) {
            $this->createInvalidAdjustedFile($invalidData);
        }

        return [
            'status' => true,
            'message' => 'The data has been uploaded successfully.'
        ];
    }

    /**
     * Get Product Qty
     *
     * @param array $importProductData
     * @param string $sourceCode
     * @return array
     */
    public function getProductQty($importProductData, $sourceCode)
    {
        $result = [];

        $skus = [];
        foreach ($importProductData as $rowIndex => $dataRow) {
            if ($rowIndex == 0) {
                continue;
            }
            $skus[] = $dataRow[0];
        }
        $sourceProducts = $this->sourceManagement->getListProductInSource($sourceCode, $skus);

        foreach ($sourceProducts as $sourceItem) {
            $result[$sourceItem->getSku()] = $sourceItem->getQuantity();
        }

        return $result;
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
     * Create Invalid Adjusted File
     *
     * @param array $invalidData
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function createInvalidAdjustedFile($invalidData)
    {
        $this->backendSession->setData('error_import', true);
        $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)->create('import');
        $filename = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)
            ->getAbsolutePath('import/import_product_to_transferstock_invalid.csv');

        $file = $this->fileWriteFactory->create(
            $filename,
            \Magento\Framework\Filesystem\DriverPool::FILE,
            'w'
        );
        $file->close();

        $data = [
            ['SKU', 'Qty to Send', 'Error reason']
        ];
        $data = array_merge($data, $invalidData);
        $this->csvProcessor->saveData($filename, $data);
    }

    /**
     * Get Required Csv Fields
     *
     * @return array
     */
    public function getRequiredCsvFields()
    {
        // indexes are specified for clarity, they are used during import
        return [
            0 => __('SKU'),
            1 => __('Qty to Send')
        ];
    }

    /**
     * Filter Import Product Data
     *
     * @param array $productRawData
     * @param array $invalidFields
     * @param array $validFields
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
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
            foreach (array_keys($dataRow) as $fieldIndex) {
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
}
