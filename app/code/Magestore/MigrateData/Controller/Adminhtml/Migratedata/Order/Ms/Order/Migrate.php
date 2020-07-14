<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\MigrateData\Controller\Adminhtml\Migratedata\Order\Ms\Order;

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
    
    public $orderPrefix = 'CWC_MS_'; //cwc = CWC_MS_; tls = TLS_MS_
    public $website = 'cwc'; //cwc = cwc; tls = tls
    public $store_id = 1; //cwc = 1; tls = 2
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
        $file = $this->getRequest()->getFiles('migrate_order');
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
            
            if ($this->createOrder($dataRow)) {
                $success++;
            }
        }
        $end = microtime(TRUE);
        echo '<br />';
        var_dump($success);
        echo '<br />';
        echo "The code took " . ($end - $start) . " seconds to complete.";
        echo '<br />';
        die('completed');
        die();
    }
    
    
    
    
    

    
    public function getSalesTable()
    {
        return array(
            'increment_id',
            'customer_email',
            'customer_firstname',
            'customer_lasttname',
            'customer_prefix',
            'customer_middlename',
            'customer_suffix',
            'taxvat',
            'created_at',
            'updated_at',
            'invoice_created_at',
            'shipment_created_at',
            'creditmemo_created_at',
            'tax_amount',
            'base_tax_amount',
            'discount_amount',
            'base_discount_amount',
            'shipping_tax_amount',
            'base_shipping_tax_amount',
            'base_to_global_rate',
            'base_to_order_rate',
            'store_to_base_rate',
            'store_to_order_rate',
            'subtotal_incl_tax',
            'base_subtotal_incl_tax',
            'coupon_code',
            'shipping_incl_tax',
            'base_shipping_incl_tax',
            'shipping_method',
            'shipping_amount',
            'subtotal',
            'base_subtotal',
            'grand_total',
            'base_grand_total',
            'base_shipping_amount',
            'adjustment_positive',
            'adjustment_negative',
            'refunded_shipping_amount',
            'base_refunded_shipping_amount',
            'refunded_subtotal',
            'base_refunded_subtotal',
            'refunded_tax_amount',
            'base_refunded_tax_amount',
            'refunded_discount_amount',
            'base_refunded_discount_amount',
            'store_id',
            'order_status',
            'order_state',
            'hold_before_state',
            'hold_before_status',
            'store_currency_code',
            'base_currency_code',
            'order_currency_code',
            'total_paid',
            'base_total_paid',
            'is_virtual',
            'total_qty_ordered',
            'remote_ip',
            'total_refunded',
            'base_total_refunded',
            'total_canceled',
            'total_invoiced');
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
            0 => 'ShipToID',
            1 => 'StoreID',
            2 => 'TransactionNumber',
            3 => 'BatchNumber',
            4 => 'Time',
            5 => 'CustomerID',
            6 => 'CashierID',
            7 => 'Total',
            8 => 'SalesTax',
            9 => 'Comment',
            10 => 'ReferenceNumber',
            11 => 'DBTimeStamp',
            12 => 'Status',
            13 => 'ExchangeID',
            14 => 'ChannelType',
            15 => 'RecallID',
            16 => 'RecallType'
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
    public function createOrder($dataRow)
    {
        try {
//        $supplierCode = $dataRow[0];
//        $supplierName = $dataRow[1];
            $orderNumber = $dataRow[2];
//        $contactEmail = $dataRow[3];
            $createdAt = $dataRow[4];
            $customerId = $dataRow[5];
//        $telephone = $dataRow[6];
            $total = $dataRow[7];
            $tax = $dataRow[8];
//        $city = $dataRow[9];
//        $countryId = $dataRow[10];
//        $regionId = $dataRow[11];
//        $postcode = $dataRow[12];
//        $website = $dataRow[13];
//        $accountNumber = $dataRow[14];
            $recallID = $dataRow[15];
//        $terms = $dataRow[16];
    
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            
            if ($total < 0) {
                $resource = $objectManager->create(\Magento\Framework\App\ResourceConnection::class);
                $connection = $resource->getConnection();
                $table = $resource->getTableName('microsoft_order_refund');
                $orderRefund = $connection->fetchOne('Select `id` from ' . $table . ' Where `microsoft_order_refund_id` = ' . $orderNumber . ' and `microsoft_order_id` = '. $recallID .' and `website` = \''.$this->website.'\';');
                if (!$orderRefund) {
                    $refundQuery = 'INSERT INTO '.$table.'(
                            `website`,
                            `microsoft_order_refund_id`,
                            `microsoft_order_id`
                            )
                        VALUES (
                            "'.$this->website.'",
                            '.$orderNumber.',
                            '.$recallID.'
                        );';
                    
                    $connection->query($refundQuery);
                }
                return true;
            }
    
            $subtotal = $total - $tax;
            $createdAt = $this->convertDateTime($createdAt);
            
    
            $order = $objectManager->create(\Magento\Sales\Model\Order::class)->loadByIncrementId($this->orderPrefix . $orderNumber);
    
            if ($order->getId())
                return false;
    
            $order = $objectManager->create(\Magento\Sales\Model\Order::class);
            $order->setIncrementId($this->orderPrefix . $orderNumber)
                  ->setStatus('complete')
//            ->setState('complete')
                  ->setStoreId($this->store_id)
                  ->setBaseCurrencyCode('USD')
                  ->setStoreCurrencyCode('USD')
                  ->setGlobalCurrencyCode('USD')
                  ->setOrderCurrencyCode('USD')
                  ->setCreatedAt($createdAt)
                  ->setUpdatedAt($createdAt);
    
            $order->setMicrosoftOrderId($orderNumber);
    
            $order->setBaseTaxAmount($tax);
            $order->setTaxAmount($tax);
            $order->setSubtotal($subtotal)
                  ->setBaseSubtotal($subtotal)
                  ->setGrandTotal($total)
                  ->setBaseGrandTotal($total)
                  ->setSubtotalInclTax($subtotal + $tax)
                  ->setBaseSubtotalInclTax($subtotal + $tax);
    
            // Set Customer data

//        $custm_detail = $this->getCustomerInfo($sales_order_arr['customer_email']);
            $customerData = [
                'email' => 'guest@gmail.com',
                'firstname' => 'guest',
                'lastname' => 'guest'
            ];
    
            $customerAddress = [
                'firstname' => 'guest',
                'lastname' => 'guest',
                'street' => '-',
                'country_id' => 'US',
                'region' => '',
                'postcode' => '-',
                'telephone' => '',
                'company' => '',
                'city' => '-',
                'fax' => ''
    
            ];
            if ($customerId) {
                $resource = $objectManager->create(\Magento\Framework\App\ResourceConnection::class);
                $connection = $resource->getConnection();
                $table = $resource->getTableName('microsoft_customer_info');
                $customer = $connection->fetchAll('Select * from ' . $table . ' Where `ms_id` = ' . $customerId . ' and `website` = \''.$this->website.'\';');
                if (count($customer)) {
                    $customer = $customer[0];
                    try {
                        $email = trim($customer['email']);
                        if ($customer['firstname']) {
                            $customerData['firstname'] = $customer['firstname'];
                            $customerAddress['firstname'] = $customer['firstname'];
                        }
                        if ($customer['lastname']) {
                            $customerData['lastname'] = $customer['lastname'];
                            $customerAddress['lastname'] = $customer['lastname'];
                        }
                        if ($customer['address']) {
                            $customerAddress['street'] = $customer['address'];
                        }
                        if ($customer['country']) {
                            $customerAddress['country_id'] = $customer['country'];
                        }
                        if ($customer['state']) {
                            $customerAddress['region'] = $customer['state'];
                        }
                        if ($customer['zipcode']) {
                            $customerAddress['postcode'] = $customer['zipcode'];
                        }
                        if ($customer['phone']) {
                            $customerAddress['telephone'] = $customer['phone'];
                        }
                        if ($customer['company']) {
                            $customerAddress['company'] = $customer['company'];
                        }
                        if ($customer['city']) {
                            $customerAddress['city'] = $customer['city'];
                        }
                        if ($customer['fax_number']) {
                            $customerAddress['fax'] = $customer['fax_number'];
                        }
                        
                        if ($email) {
                            $customerData['email'] = $email;
                        }
                        
                        $order->setCustomerEmail($customerData['email'])
                              ->setCustomerFirstname($customerData['firstname'])
                              ->setCustomerLastname($customerData['lastname'])
                              ->setCustomerIsGuest(1)
                              ->setCustomerGroupId(0);
                        
                        if ($email) {
                            $custm_detail = $this->getCustomerInfo($email);
                            if ($custm_detail) {
                                $order->setCustomerEmail(trim($custm_detail['email']))
                                      ->setCustomerFirstname($custm_detail['firstname'])
                                      ->setCustomerLastname($custm_detail['lastname'])
                                      ->setCustomerId($custm_detail['entity_id'])
                                      ->setCustomerIsGuest(0)
                                      ->setCustomerGroupId($custm_detail['group_id']);
                            }
                        }
                    } catch (\Exception $e) {
                        var_dump($e->getMessage());
                        var_dump($customer);
//                        die();
                    }
                } else {
                    $order->setCustomerEmail($customerData['email'])
                          ->setCustomerFirstname($customerData['firstname'])
                          ->setCustomerLastname($customerData['lastname'])
                          ->setCustomerIsGuest(1)
                          ->setCustomerGroupId(0);
                }
            } else {
                $order->setCustomerEmail($customerData['email'])
                      ->setCustomerFirstname($customerData['firstname'])
                      ->setCustomerLastname($customerData['lastname'])
                      ->setCustomerIsGuest(1)
                      ->setCustomerGroupId(0);
            }
    
            // Set Billing Address
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $billingAddress = $objectManager->create(\Magento\Sales\Model\Order\Address::class)
                //Mage::getModel('sales/order_address')
                                            ->setStoreId($this->store_id)
                                            ->setAddressType(\Magento\Quote\Model\Quote\Address::TYPE_BILLING)
//                                        ->setCustomerAddressId($sales_order_arr['billing_address']['customer_address_id'])
//                                        ->setPrefix($sales_order_arr['billing_address']['prefix'])
                                            ->setFirstname($customerAddress['firstname'])
//                                        ->setMiddlename($sales_order_arr['billing_address']['middlename'])
                                            ->setLastname($customerAddress['lastname'])
//                                        ->setSuffix($sales_order_arr['billing_address']['suffix'])
                                            ->setCompany($customerAddress['company'])
                                            ->setStreet($customerAddress['street'])
                                            ->setCity($customerAddress['city'])
                                            ->setCountryId($customerAddress['country_id'])
                                            ->setRegion($customerAddress['region'])
                                            ->setPostcode($customerAddress['postcode'])
                                            ->setTelephone($customerAddress['telephone'])
                                            ->setFax($customerAddress['fax']);
            $order->setBillingAddress($billingAddress);
    
            // Set Shipping Address
            $shippingAddress = $objectManager->create(\Magento\Sales\Model\Order\Address::class)
                                             ->setStoreId($this->store_id)
                                             ->setAddressType(\Magento\Quote\Model\Quote\Address::TYPE_SHIPPING)
//                                         ->setCustomerAddressId($sales_order_arr['shipping_address']['customer_address_id'])
//                                         ->setPrefix($sales_order_arr['shipping_address']['prefix'])
                                             ->setFirstname($customerAddress['firstname'])
                //                                        ->setMiddlename($sales_order_arr['billing_address']['middlename'])
                                             ->setLastname($customerAddress['lastname'])
                //                                        ->setSuffix($sales_order_arr['billing_address']['suffix'])
                                             ->setCompany($customerAddress['company'])
                                             ->setStreet($customerAddress['street'])
                                             ->setCity($customerAddress['city'])
                                             ->setCountryId($customerAddress['country_id'])
                                             ->setRegion($customerAddress['region'])
                                             ->setPostcode($customerAddress['postcode'])
                                             ->setTelephone($customerAddress['telephone'])
                                             ->setFax($customerAddress['fax']);
            $order->setShippingAddress($shippingAddress)
                  ->setShippingMethod('storepickup_storepickup')
                  ->setShippingDescription('Store Pickup');
    
    
            //set payment
            $orderPayment = $objectManager->create(\Magento\Sales\Model\Order\Payment::class)
                                          ->setStoreId($this->store_id)
                                          ->setCustomerPaymentId(0)
                                          ->setMethod('checkmo')
                                          ->setPoNumber(' - ');
            $order->setPayment($orderPayment);
            $order->save();
//        var_dump($dataRow);die();
    
            return true;
        } catch (\Exception $e) {
            echo "<pre>";
            var_dump($orderNumber);
            var_dump($e->getMessage());
//            var_dump($order->getCustomerEmail());
            var_dump('Customer data: ');
            var_dump($customerData);
            var_dump('Customer address: ');
            var_dump($customerAddress);
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
