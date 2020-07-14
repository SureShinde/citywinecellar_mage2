<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\MigrateData\Controller\Adminhtml\Migratedata\Order\Ms\Product;

use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Type;
use Magestore\PurchaseOrderSuccess\Model\System\Config\ProductSource;

/**
 * Class Save
 *
 * @package Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Quotation
 */
class Migrate extends \Magento\Backend\App\Action
{
    
    public $order_info = array();
    public $order_item_info = array();
    public $order_item_flag = 0;
    
    public $store_id = 2;
    public $import_limit = 0;
    
    public $orderPrefix = 'TLS_MS_';
    public $website = 'tls';
    /**
     * Quotation grid
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $start = microtime(TRUE);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $csvProcessor = $objectManager->create(\Magento\Framework\File\Csv::class);
        $params = $this->getRequest()->getParams();
        $file = $this->getRequest()->getFiles('migrate_product');
        $importRawData = $csvProcessor->getData($file['tmp_name']);
        
        $fileFields = $importRawData[0];
        $validFields = $this->filterFileFields($fileFields);
        $invalidFields = array_diff_key($fileFields, $validFields);
        $importData = $this->filterImportData($importRawData, $invalidFields, $validFields);
        $success = 0;
        foreach ($importData as $rowIndex => $dataRow) {
            // skip headers
            if ($rowIndex == 0 || !$dataRow[2]) {
                continue;
            }
            
            if ($this->importProductMapping($dataRow)) {
                $success++;
            }
        }
        $end = microtime(TRUE);
        echo "The code took " . ($end - $start) . " seconds to complete.";
        die('completed');
        var_dump($success);
        die();
    }
    
    public function getSalesPayment()
    {
        return array('method');
    }
    
    
    
    
    /**
     * Filter file fields (i.e. unset invalid fields)
     *
     * @param array $fileFields
     * @return string[] filtered fields
     */
    public function filterFileFields(array $fileFields)
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
     * Get required columns
     *
     * @return array
     */
    public function getRequiredCsvFields()
    {
        // indexes are specified for clarity, they are used during import
        return [
            0 => 'sku',
            1 => 'ms_id',
            2 => 'name'
        ];
    }
    
    /**
     * Modify import data
     *
     * @param array $rawData
     * @param array $invalidFields
     * @param array $validFields
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function filterImportData(array $rawData, array $invalidFields, array $validFields)
    {
        $validFieldsNum = count($validFields);
        foreach ($rawData as $rowIndex => $dataRow) {
            // skip empty rows
            if (count($dataRow) <= 1) {
                unset($rawData[$rowIndex]);
                continue;
            }
            // unset invalid fields from data row
            foreach (array_keys($dataRow) as $fieldIndex) {
                if (isset($invalidFields[$fieldIndex])) {
                    unset($rawData[$rowIndex][$fieldIndex]);
                }
            }
            // check if number of fields in row match with number of valid fields
            if (count($rawData[$rowIndex]) != $validFieldsNum) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file format.'));
            }
        }
        return $rawData;
    }
    
    public function convertDateTime($origDate)
    {
        $date = str_replace('/', '-', $origDate );
        $newDate = date("Y-m-d H:i", strtotime($date));
        
        return $newDate;
    }
    
    /**
     * Save supplier
     *
     * @param $dataRow
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function importProductMapping($dataRow)
    {
        $website = $this->website;
        $magento_product_sku = $dataRow[0];
        $ms_product_id = $dataRow[1];
        $product_name = $dataRow[2];
    
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    
        $resource = $objectManager->create(\Magento\Framework\App\ResourceConnection::class);
        $connection = $resource->getConnection();
        $table = $resource->getTableName('microsoft_product_mapping');
        $selectQuery = 'SELECT `id` from '. $table .' Where `magento_product_sku` = '. $magento_product_sku .' and `ms_product_id` = ' . $ms_product_id .';';
        $result = $connection->fetchOne($selectQuery);
        if ($result) {
            return true;
        }
        $query = 'INSERT INTO '.$table.'(
                `magento_product_sku`,
                `ms_product_id`,
                `product_name`)
            VALUES (
                \''.$magento_product_sku.'\',
                '.$ms_product_id.',
                \''. str_replace("'","''", $product_name). '\'
            );';
        $connection->query($query);
        
        return true;
    }
    
}
