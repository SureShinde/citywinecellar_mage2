<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\BarcodeSuccess\Model;

use Magestore\BarcodeSuccess\Model\History;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Tax Rate CSV Import Handler
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
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    protected $helper;

    protected $productCollectionFactory;

    protected $messageManager;

    protected $filesystem;

    protected $backendSession;

    protected $fileWriteFactory;

    protected $driverFile;

    public function __construct(
        \Magento\Framework\File\Csv $csvProcessor,
        \Magestore\BarcodeSuccess\Helper\Data $helper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Backend\Model\Session $backendSession,
        \Magento\Framework\Filesystem\File\WriteFactory $fileWriteFactory,
        \Magento\Framework\Filesystem\Driver\File $driverFile
    )
    {
        $this->csvProcessor = $csvProcessor;
        $this->helper = $helper;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->messageManager = $messageManager;
        $this->filesystem = $filesystem;
        $this->backendSession = $backendSession;
        $this->driverFile = $driverFile;
        $this->fileWriteFactory = $fileWriteFactory;
    }


    public function importFromCsvFile($file, $reason)
    {
        if (!isset($file['tmp_name'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file upload attempt.'));
        }


        $importProductRawData = $this->csvProcessor->getData($file['tmp_name']);

        $fileFields = $importProductRawData[0];

        $validFields = $this->_filterFileFields($fileFields);

        $invalidFields = array_diff_key($fileFields, $validFields);
        
        $importProductData = $this->_filterImportProductData($importProductRawData, $invalidFields, $validFields);

        $totalQty = 0;
        $barcodeArray = array();
        $hasError = false;
        $invalidData = array(
            array('SKU', 'BARCODE', 'QTY', 'SUPPLIER', 'PURCHASE_TIME')
        );
        if (!count($importProductData)) {
            $invalidData = $importProductRawData;
            $hasError = true;
        }
        $importSuccess = 0;
        $editSuccess = 0;

        foreach ($importProductData as $rowIndex => $dataRow) {
            // skip headers
            if ($rowIndex == 0) {
                continue;
            }
            $productSku = $dataRow[0];
            $barcode = $dataRow[1];
            $qty = $dataRow[2];
            $supplierCode = $dataRow[3];
            $purchaseTime = $dataRow[4];

            $data['product_sku'] = $productSku;
            $data['barcode'] = $barcode;
            $data['qty'] = $qty;
            $data['supplier_code'] = $supplierCode;
            $data['purchased_time'] = $purchaseTime;
            $barcodeArray[] = $data;
        }

        $invalidSku = array();
        $invalidBarcode = array();

        $historyId = '';
        $history = $this->helper->getModel('Magestore\BarcodeSuccess\Api\Data\HistoryInterface');
        $historyResource = $this->helper->getModel('Magestore\BarcodeSuccess\Model\ResourceModel\History');
        $adminSession = $this->helper->getModel('Magento\Backend\Model\Auth\Session');
        try {
            $admin = $adminSession->getUser();
            $adminId = ($admin) ? $admin->getId() : 0;
            $history->setData('type', History::IMPORTED);
            $history->setData('reason', $reason);
            $history->setData('created_by', $adminId);

            $historyResource->save($history);
            $historyId = $history->getId();
        } catch (\Exception $e) {
            $this->helper->addLog($e->getMessage());
        }
        
        $oneBarcodePerSku = $this->helper->getStoreConfig('barcodesuccess/general/one_barcode_per_sku');
        foreach ($barcodeArray as $barcodeData) {
            if ($barcodeData['product_sku'] && $barcodeData['barcode']) {
                $productSku = $barcodeData['product_sku'];
                if ($oneBarcodePerSku) {
                    $skuExist = $this->helper->getModel('Magestore\BarcodeSuccess\Model\Barcode')->load($barcodeData['product_sku'],'product_sku');
                    if ($skuExist->getId()) {
                        $skuExist
                            ->setBarcode($barcodeData['barcode'])
                            ->setQty($barcodeData['qty'])
                            ->setPurchasedTime($barcodeData['purchased_time'])
                            ->save();
                        $editSuccess++;
                        continue;
                    }
                }
                $productModel = $this->productCollectionFactory->create()
                    ->addFieldToSelect('name')
                    ->addFieldToFilter('sku', $productSku)
                    ->setPageSize(1)
                    ->setCurPage(1)
                    ->getFirstItem();
                $barcodeExist = $this->helper->getModel('Magestore\BarcodeSuccess\Model\Barcode')->load($barcodeData['barcode'],'barcode');
                if ($productModel->getId() && !$barcodeExist->getId()) {
                    $barcodeData['product_id'] = $productModel->getId();
                    $barcodeData['history_id'] = $historyId;
                    $totalQty += floatval($barcodeData['qty']);
                    $barcodeArray[] = $barcodeData;
                    $barcode = $this->helper->getModel('\Magestore\BarcodeSuccess\Api\Data\BarcodeInterface');
                    $barcode->setData($barcodeData);
                    $this->helper->resource->save($barcode);
                    $importSuccess++;
                } else {
                    if (!$productModel->getId()) {
                        $invalidSku[] = $productSku;
                    }
                    if ($barcodeExist->getId()) {
                        $invalidBarcode[] = $barcodeData['barcode'];
                    }
                    $invalidData[] = $barcodeData;
                    $hasError = true;
                }
            } else {
                $invalidData[] = $barcodeData;
                $hasError = true;
            }
        }

        if($importSuccess > 0){
            $history->setData('total_qty', $totalQty);
            $history->save();
        }else{
            $history->setId($historyId)->delete();
        }

        if ($hasError) {
            $this->backendSession->setData('error_import', true);
            $this->backendSession->setData('sku_exist', count($invalidSku));
            $this->backendSession->setData('barcode_exist', count($invalidBarcode));
            
            $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)->create('import');
            $filename = $this->filesystem->getDirectoryRead(DirectoryList::VAR_DIR)
                            ->getAbsolutePath('import_product_invalid.csv');
            $file = $this->fileWriteFactory->create(
                $filename,
                \Magento\Framework\Filesystem\DriverPool::FILE,
                'w'
            );
            $file->close();

            $this->csvProcessor->saveData($filename, $invalidData);
        }

        return array(
            'history_id' => $historyId,
            'import_success' => $importSuccess,
            'edit_success' => $editSuccess
        );
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

    public function getRequiredCsvFields()
    {
        // indexes are specified for clarity, they are used during import
        return [
            0 => __('SKU'),
            1 => __('BARCODE'),
            2 => __('QTY'),
        ];
    }

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

    public function getBaseDirMedia()
    {
        return $this->filesystem->getDirectoryRead('media');
    }

}
