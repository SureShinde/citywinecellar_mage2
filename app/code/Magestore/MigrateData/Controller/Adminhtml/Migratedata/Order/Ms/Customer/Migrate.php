<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\MigrateData\Controller\Adminhtml\Migratedata\Order\Ms\Customer;

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
    
    public $store_id = 1; //cwc = 1; cls = 2;
    public $import_limit = 0;
    
    public $orderPrefix = 'CWC_MS_'; //cwc = CWC_MS_; cls = CLS_MS
    public $website = 'cwc'; //cwc = cwc; cls = cls
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
        $file = $this->getRequest()->getFiles('migrate_customer');
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
            
            if ($this->importCustmer($dataRow)) {
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
            0 => 'ID',
            1 => 'EmailAddress',
            2 => 'FirstName',
            3 => 'LastName',
            4 => 'Company',
            5 => 'Country',
            6 => 'State',
            7 => 'Address',
            8 => 'City',
            9 => 'Zip',
            10 => 'TaxNumber',
            11 => 'PhoneNumber',
            12 => 'FaxNumber'
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
    public function importCustmer($dataRow)
    {
        $website = $this->website;
        $ms_id = $dataRow[0];
        $email = $dataRow[1];
        $firstname = $dataRow[2];
        $lastname = $dataRow[3];
        $company = $dataRow[4];
        $country = $dataRow[5];
        $state = $dataRow[6];
        $address = $dataRow[7];
        $city = $dataRow[8];
        $zipcode = $dataRow[9];
        $tax_number = $dataRow[10];
        $phone = $dataRow[11];
        $fax_number = $dataRow[12];
    
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    
        $resource = $objectManager->create(\Magento\Framework\App\ResourceConnection::class);
        $connection = $resource->getConnection();
        $table= $resource->getTableName('microsoft_customer_info');
        $query = 'INSERT INTO '.$table.'(
                `website`,
                `ms_id`,
                `email`,
                `firstname`,
                `lastname`,
                `company`,
                `country`,
                `state`,
                `address`,
                `city`,
                `zipcode`,
                `tax_number`,
                `phone`,
                `fax_number`)
            VALUES (
                \''.$website.'\',
                '.$ms_id.',
                \''.$email.'\',
                \''.str_replace("'","''", $firstname).'\',
                \''.str_replace("'","''", $lastname).'\',
                \''.str_replace("'","''", $company).'\',
                \''.$country.'\',
                \''.$state.'\',
                \''.str_replace("'","''", $address).'\',
                \''.str_replace("'","''", $city).'\',
                \''.$zipcode.'\',
                \''.$tax_number.'\',
                \''.$phone.'\',
                \''.$fax_number.'\'
                
            );';
        $connection->query($query);
        
        return true;
        
    }
    
}
