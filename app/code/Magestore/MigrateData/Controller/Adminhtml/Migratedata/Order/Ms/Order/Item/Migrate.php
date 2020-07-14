<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\MigrateData\Controller\Adminhtml\Migratedata\Order\Ms\Order\Item;

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
    
    public $import_limit = 0;
    
    public $orderPrefix = 'CWC_MS_';
    public $website = 'cwc';
    public $store_id = 1;
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
        $file = $this->getRequest()->getFiles('migrate_order_item');
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
//            $start_one = microtime(TRUE);
            if ($this->createOrderItem($dataRow)) {
                $success++;
            }
//            $end_one = microtime(TRUE);
//            echo '<br />';
//            echo "One order " . ($end_one - $start_one) . " seconds to complete.";
//            echo "<br />";
//            echo "<br />";
        }
        
        $end = microtime(TRUE);
        echo '<br />';
        var_dump($success);
        echo '<br />';
        echo "The code took " . ($end - $start) . " seconds to complete.";
        echo '<br />';
        die('completed');
        echo "The code took " . ($end - $start) . " seconds to complete.";
        var_dump($success);
        die('completed');
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
            0 => 'Commission',
            1 => 'Cost',
            2 => 'FullPrice',
            3 => 'StoreID',
            4 => 'ID',
            5 => 'TransactionNumber',
            6 => 'ItemID',
            7 => 'Price',
            8 => 'PriceSource',
            9 => 'Quantity',
            10 => 'SalesRepID',
            11 => 'Taxable',
            12 => 'DetailID',
            13 => 'Comment',
            14 => 'DBTimeStamp',
            15 => 'DiscountReasonCodeID',
            16 => 'ReturnReasonCodeID',
            17 => 'TaxChangeReasonCodeID',
            18 => 'SalesTax',
            19 => 'QuantityDiscountID',
            20 => 'ItemType',
            21 => 'ComputedQuantity',
            22 => 'TransactionTime',
            23 => 'IsAddMoney',
            24 => 'VoucherID',
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
    public function createOrderItem($dataRow)
    {
        try {
            $cost = $dataRow[1];
            $original_price = $dataRow[2];
            $microsoft_order_item_id = $dataRow[4];
            $microsoft_order_id = $dataRow[5];
            $microsoft_product_id = $dataRow[6];
            $price = $dataRow[7];
            $qty = $dataRow[9];
            $tax_amount = $dataRow[18] * $qty;
            $productType = 'simple';
            $createdAt = $dataRow[22];
            $createdAt = $this->convertDateTime($createdAt);
            $row_total = $price * $qty;
            
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $resource = $objectManager->create(\Magento\Framework\App\ResourceConnection::class);
            $connection = $resource->getConnection();
            
            //check imported
            $table = $resource->getTableName('microsoft_order_item_imported');
//            $start_checkItemImported = microtime(TRUE);
            $checkItemImported = $connection->fetchOne('Select `id` from '. $table . ' where `website` = \''. $this->website .'\' and `microsoft_order_item_id` = '. $microsoft_order_item_id .';');
//            $end_checkItemImported = microtime(TRUE);
//            echo '<br />';
//            echo "checkItemImported " . ($end_checkItemImported - $start_checkItemImported) . " seconds to complete.";
            if ($checkItemImported) {
                return true;
            }
            
            //order item
            if ($qty > 0) {
                $table = $resource->getTableName('sales_order');
//                $start_checkorderId = microtime(TRUE);
                $orderId = $connection->fetchOne('Select `entity_id` from '.$table.' where `microsoft_order_id` = '.$microsoft_order_id.' and `store_id` = '. $this->store_id .';');
//                $end_checkorderId = microtime(TRUE);
//                echo '<br />';
//                echo "checkorderId " . ($end_checkorderId - $start_checkorderId) . " seconds to complete.";
                if ($orderId) {
                    $productMappingTable = $resource->getTableName('microsoft_product_mapping');
//                    $start_checkproductData = microtime(TRUE);
                    $productData = $connection->fetchAll('Select * from '. $productMappingTable.' where `ms_product_id` = '. $microsoft_product_id .';');
//                    $end_checkproductData = microtime(TRUE);
//                    echo '<br />';
//                    echo "checkproductData " . ($end_checkproductData - $start_checkproductData) . " seconds to complete.";
                    if ($productData && isset($productData[0])) {
                        $productSku = $productData[0]['magento_product_sku'];
                        $productName = $productData[0]['product_name'];
                        //check item exist
                        $orderItemTable = $resource->getTableName('sales_order_item');
//                        $start_checkexistOrderItemId = microtime(TRUE);
                        $existOrderItemId = $connection->fetchOne('Select `item_id` from '. $orderItemTable .' where `order_id` = ' . $orderId .' and `sku` = '. $productSku .';');
//                        $end_checkexistOrderItemId = microtime(TRUE);
//                        echo '<br />';
//                        echo "checkexistOrderItemId " . ($end_checkexistOrderItemId - $start_checkexistOrderItemId) . " seconds to complete.";
//                        $start_checkInsertItem = microtime(TRUE);
                        if ($existOrderItemId) {
                            $orderItemUpdateQuery = ' UPDATE '.$orderItemTable.' SET
                                    `qty_invoiced`=`qty_invoiced` + '.$qty.',
                                    `qty_ordered`=`qty_ordered` + '.$qty.',
                                    `qty_shipped`=`qty_shipped` + '.$qty.',
                                    `tax_amount`=`tax_amount` + '.$tax_amount.',
                                    `base_tax_amount`=`base_tax_amount` + '.$tax_amount.',
                                    `base_tax_amount`=`base_tax_amount` + '.$tax_amount.',
                                    `row_total`=`row_total` + '.$row_total.',
                                    `base_row_total`=`base_row_total` + '.$row_total.'
                                WHERE `item_id` = '.$existOrderItemId.'
                            ';
                            $connection->query($orderItemUpdateQuery);
                        } else {
                            $orderItemQuery = 'Insert Into ' . $orderItemTable .'(
                                        `order_id`,
                                        `store_id`,
                                        `created_at`,
                                        `updated_at`,
                                        `product_type`,
                                        `sku`,
                                        `name`,
                                        `qty_invoiced`,
                                        `qty_ordered`,
                                        `qty_shipped`,
                                        `base_cost`,
                                        `price`,
                                        `base_price`,
                                        `original_price`,
                                        `base_original_price`,
                                        `tax_amount`,
                                        `base_tax_amount`,
                                        `row_total`,
                                        `base_row_total`
                                    )
                                Values (
                                    '.$orderId.',
                                    '.$this->store_id.',
                                    \''.$createdAt.'\',
                                    \''.$createdAt.'\',
                                    \''.$productType.'\',
                                    \''.$productSku.'\',
                                    \''.str_replace("'","''", $productName).'\',
                                    '.$qty.',
                                    '.$qty.',
                                    '.$qty.',
                                    '.$cost.',
                                    '.$price.',
                                    '.$price.',
                                    '.$original_price.',
                                    '.$original_price.',
                                    '.$tax_amount.',
                                    '.$tax_amount.',
                                    '.$row_total.',
                                    '.$row_total.'
                                    
                                );
                            ';
                            $connection->query($orderItemQuery);
                        }
//                        $end_checkInsertItem = microtime(TRUE);
//                        echo '<br />';
//                        echo "checkInsertItem " . ($end_checkInsertItem - $start_checkInsertItem) . " seconds to complete.";
                    }
                }
            }
            
            //refund item
            if ($qty < 0) {
                $microsoftOrderRefundTable = $resource->getTableName('microsoft_order_refund');
//                $start_checkOrderRefund = microtime(TRUE);
                $microsoftOrderRefundId = $connection->fetchOne('Select `microsoft_order_id` from '. $microsoftOrderRefundTable .' where `microsoft_order_refund_id` = '.$microsoft_order_id.';');
//                $end_checkOrderRefund = microtime(TRUE);
//                echo '<br />';
//                echo "checkOrderRefund " . ($end_checkOrderRefund - $start_checkOrderRefund) . " seconds to complete.";
                if ($microsoftOrderRefundId) {
                    $table = $resource->getTableName('sales_order');
                    $orderId = $connection->fetchOne('Select `entity_id` from '.$table.' where `microsoft_order_id` = '.$microsoftOrderRefundId.'  and `store_id` = '. $this->store_id .';');
                    if ($orderId) {
                        $productMappingTable = $resource->getTableName('microsoft_product_mapping');
                        $productData = $connection->fetchAll('Select * from '. $productMappingTable.' where `ms_product_id` = '. $microsoft_product_id .';');
                        if ($productData && isset($productData[0])) {
//                            $start_checkInsertItemRefund = microtime(TRUE);
                            $productSku = $productData[0]['magento_product_sku'];
                            $orderItemTable = $resource->getTableName('sales_order_item');
                            $orderItemUpdateQuery = ' UPDATE '.$orderItemTable.' SET
                                    `qty_refunded`=`qty_refunded` + '. $qty*(-1) .',
                                    `amount_refunded`=`amount_refunded` + '. ($row_total - $tax_amount)*(-1).',
                                    `base_amount_refunded`=`base_amount_refunded` + '. ($row_total - $tax_amount)*(-1).',
                                    `tax_refunded`=`tax_refunded` + '. $tax_amount .',
                                    `tax_refunded`=`tax_refunded` + '. $tax_amount .',
                                    `base_tax_refunded`=`base_tax_refunded` + '. $tax_amount .'
                                WHERE `order_id` = '.$orderId.' and `sku` = '.$productSku.'
                            ';

                            $connection->query($orderItemUpdateQuery);
//                            $end_checkInsertItemRefund = microtime(TRUE);
//                            echo '<br />';
//                            echo "checkInsertItemRefund " . ($end_checkInsertItemRefund - $start_checkInsertItemRefund) . " seconds to complete.";
                        }
                    }
                }
            }
            //update imported
            $table = $resource->getTableName('microsoft_order_item_imported');
            $itemImportedQuery = 'Insert into '. $table . '(
                                    `website`,
                                    `microsoft_order_item_id`
                                    )
                                values (
                                    \''.$this->website.'\',
                                    '.$microsoft_order_item_id.'
                                );
                ';
            $connection->query($itemImportedQuery);
    
            return true;
        } catch (\Exception $e) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $logger = $objectManager->create(\Magestore\MigrateData\Logger\Logger::class);
            $logger->info($microsoft_order_item_id . ': ');
            $logger->info($e->getMessage());
            echo "<pre>";
            var_dump($e->getMessage());
//            die();
        }
        
    }
    
    public function getCustomerInfo($email)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customer = $objectManager->create(\Magento\Customer\Model\Customer::class);
        $customer->setWebsiteId($objectManager->create(\Magento\Store\Model\Store::class)->load($this->store_id)->getWebsiteId());
        if ($customer->loadByEmail($email))
            return $customer->getData();
        else
            return false;
    }
    
}
