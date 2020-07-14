<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\MigrateData\Controller\Adminhtml\Migratedata\Order;

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
    public $store_id = 0;
    public $import_limit = 0;
    
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
        $this->readCSV($importRawData);
        $end = microtime(TRUE);
        echo "The code took " . ($end - $start) . " seconds to complete.";
        die('completed');
        
        
        $fileFields = $importRawData[0];
        $validFields = $this->filterFileFields($fileFields);
        $invalidFields = array_diff_key($fileFields, $validFields);
        $importData = $this->filterImportData($importRawData, $invalidFields, $validFields);
        $success = 0;
        foreach ($importData as $rowIndex => $dataRow) {
            // skip headers
            if ($rowIndex == 0 || !$dataRow[0]) {
                continue;
            }

            if ($this->saveSupplier($dataRow)) {
                $success++;
            }
        }
        var_dump($success);
        die();
    }
    
    public function readCSV($line_of_text)
    {
//        $this->import_limit = $data['import_limit'];
//        $this->store_id = $data['store_id'];
//        $file_handle = fopen($csvFile, 'r');
        $this->store_id = 1;
        $i = 0;
        $decline = array();
        $available = array();
        $success = 0;
        $parent_flag = 0;
        $invalid = 0;
        $line_number = 2;
        $total_order = 0;
//        Mage::helper('exporter')->unlinkFile();
//        Mage::helper('exporter')->header();
        for ($j = 0; $j <= count($line_of_text); $j++) {
            if ($i != 0) {
                if (isset($line_of_text[$i][0]) && $line_of_text[$i][0] != '' && $parent_flag == 0) {
                    $this->insertOrderData($line_of_text[$i]);
                    $parent_flag = 1;
                    $total_order++;
                } else if (isset($line_of_text[$i][0]) && isset($line_of_text[$i][91]) && $line_of_text[$i][91] != '' && $parent_flag == 1 && $line_of_text[$i][0] == '') {
                    $this->insertOrderItem($line_of_text[$i]);
                } else if ($parent_flag == 1) {
                    
                    try {
                        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                        $message = $objectManager->create(\Magestore\MigrateData\Model\Order\CreateOrder::class)->createOrder($this->order_info, $this->order_item_info, $this->store_id);
                        $objectManager->create(\Magestore\MigrateData\Model\Order\CreateOrder::class)->removeOrderStatusHistory();
                    } catch (Exception $e) {
//                        Mage::helper('exporter')->logException($e, $this->order_info['increment_id'], 'order', $line_number);
//                        Mage::helper('exporter')->footer();
                        $decline[] = $this->order_info['increment_id'];
                        $message = 0;
                    }
                    
                    if ($message == 1)
                        $success++;
                    
                    if ($message == 2) {
//                        Mage::helper('exporter')->logAvailable($this->order_info['increment_id'], 'order', $line_number);
//                        Mage::helper('exporter')->footer();
                        $decline[] = $this->order_info['increment_id'];
                    }
                    
                    $this->order_info = array();
                    $this->order_item_info = array();
                    $this->order_item_flag = 0;
                    
                    if (isset($line_of_text[$i]) && is_array($line_of_text[$i])) {
                        $this->insertOrderData($line_of_text[$i]);
                        $parent_flag = 1;
                        $line_number = $i + 1;
                        $total_order++;
                    }
                }
            }
            $i++;
        }
        
//        $isPrintable = Mage::helper('exporter')->isPrintable();
        if ($success)
            var_dump('Total ' . $success . ' order(s) imported successfully!');
        
//        if ($decline || $isPrintable)
//            Mage::getModel('core/session')->addError(Mage::helper('exporter')->__('Click <a href="' . Mage::helper("adminhtml")->getUrl("*/exporter/exportLog") . '">here</a> to view the error log'));
//
//        fclose($file_handle);

//        return array($success, $decline);
    }
    
    public function insertOrderData($orders_data)
    {
        $sales_order_arr = array();
        $sales_order_item_arr = array();
        $sales_order = $this->getSalesTable();
        $sales_payment = $this->getSalesPayment();
        $sales_shipping = $this->getSalesBilling();
        $sales_billing = $this->getSalesBilling();
        $sales_order_item = $this->getSalesItem();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
//        $model = Mage::getModel('sales/order');
        $model = $objectManager->create(\Magento\Sales\Model\Order::class);
        $i = 0;
        $j = 0;
        $k = 0;
        $l = 0;
        $m = 0;
        
        foreach ($orders_data as $order) {
            try {
                if (count($sales_order) > $i) {
                    if ($sales_order[$i] == 'shipping_method') {
                        $order = json_decode($order);
                        $sales_order_arr[$sales_order[$i]] = $order->shipping_method;
                        $sales_order_arr['shipping_description'] = $order->shipping_description;
                    } else {
                        $sales_order_arr[$sales_order[$i]] = $order;
                    }
                } elseif (count($sales_billing) > $j) {
                    $sales_billing[$j] . $sales_order_arr['billing_address'][$sales_billing[$j]] = $order;
                    $j++;
                } elseif (count($sales_shipping) > $k) {
                    $sales_order_arr['shipping_address'][$sales_shipping[$k]] = $order;
                    $k++;
                } elseif (count($sales_payment) > $l) {
                    $sales_order_arr['payment'][$sales_payment[$l]] = $order;
                    $l++;
                } elseif (count($sales_order_item) > $m) {
                    $sales_order_item_arr[$sales_order_item[$m]] = $order;
                    $m++;
                }
    
                $i++;
            } catch (\Exception $e) {
                var_dump($e->getMessage());
                echo "<pre>";
                var_dump($order);
            }
            
        }
        $this->order_info = $sales_order_arr;
        $this->order_item_info[$this->order_item_flag] = $sales_order_item_arr;
        $this->order_item_flag++;
    }
    
    public function insertOrderItem($orders_data)
    {
        $sales_order_item_arr = array();
        $sales_order_item = $this->getSalesItem();
        $i = 0;
        for ($j = 91; $j < count($orders_data); $j++) {
            if (count($sales_order_item) > $i)
                $sales_order_item_arr[$sales_order_item[$i]] = $orders_data[$j];
            $i++;
        }
        
        $this->order_item_info[$this->order_item_flag] = $sales_order_item_arr;
        $this->order_item_flag++;
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
    
    public function getSalesBilling()
    {
        return array(
            'customer_address_id',
            'prefix',
            'firstname',
            'middlename',
            'lastname',
            'suffix',
            'street',
            'city',
            'region',
            'country_id',
            'postcode',
            'telephone',
            'company',
            'fax');
    }
    
    public function getSalesPayment()
    {
        return array('method');
    }
    
    public function getSalesItem()
    {
        return array(
            'product_sku',
            'product_name',
            'qty_ordered',
            'qty_invoiced',
            'qty_shipped',
            'qty_refunded',
            'qty_canceled',
            'product_type',
            'original_price',
            'base_original_price',
            'row_total',
            'base_row_total',
            'row_weight',
            'price_incl_tax',
            'base_price_incl_tax',
            'product_tax_amount',
            'product_base_tax_amount',
            'product_tax_percent',
            'product_discount',
            'product_base_discount',
            'product_discount_percent',
            'is_child',
            'product_option');
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
    
    public function getRequiredCsvFieldsProduct()
    {
        return [
            0 => 'sku',
            1 => '_store',
            2 => '_attribute_set',
            3 => '_type',
            4 => '_category',
            5 => '_root_category',
            6 => '_product_websites',
            7 => 'age',
            8 => 'alcohol_percent',
            9 => 'allowed_to_quotemode',
            10 => 'am_hide_from_html_sitemap',
            11 => 'bin_location',
            12 => 'bss_weight',
            13 => 'care_details',
            14 => 'citywinecellar_acp_disabled',
            15 => 'color_family',
            16 => 'cost',
            17 => 'country',
            18 => 'country_of_manufacture',
            19 => 'created_at',
            20 => 'custom_design',
            21 => 'custom_design_from',
            22 => 'custom_design_to',
            23 => 'custom_layout_update',
            24 => 'description',
            25 => 'dominantvar',
            26 => 'exclude_from_sitemap',
            27 => 'gallery',
            28 => 'gdf_url',
            29 => 'gift_message_available',
            30 => 'has_options',
            31 => 'image',
            32 => 'image_label',
            33 => 'in_active',
            34 => 'is_imported',
            35 => 'item_lookup_code',
            36 => 'item_not_discountable',
            37 => 'kosher',
            38 => 'media_gallery',
            39 => 'meta_description',
            40 => 'meta_keyword',
            41 => 'meta_title',
            42 => 'minimal_price',
            43 => 'msrp',
            44 => 'msrp_display_actual_price_type',
            45 => 'msrp_enabled',
            46 => 'name',
            47 => 'news_from_date',
            48 => 'news_to_date',
            49 => 'nosulf',
            50 => 'options_container',
            51 => 'orgbio',
            52 => 'original_sku',
            53 => 'page_layout',
            54 => 'price',
            55 => 'price_a',
            56 => 'price_b',
            57 => 'price_c',
            58 => 'producer',
            59 => 'pub1',
            60 => 'pub2',
            61 => 'pub3',
            62 => 'pub4',
            63 => 'pub5',
            64 => 'pubdesc1',
            65 => 'pubdesc2',
            66 => 'pubdesc3',
            67 => 'pubdesc4',
            68 => 'pubdesc5',
            69 => 'quantity_discount_id',
            70 => 'rate',
            71 => 'rate1',
            72 => 'rate2',
            73 => 'rate3',
            74 => 'rate4',
            75 => 'rate5',
            76 => 'region',
            77 => 'required_options',
            78 => 'reviewer',
            79 => 'sales_event',
            80 => 'sale_type',
            81 => 'screwcap',
            82 => 'searchindex_weight',
            83 => 'shipping_price',
            84 => 'ship_box',
            85 => 'ship_box_tolerance',
            86 => 'ship_case_quantity',
            87 => 'ship_height',
            88 => 'ship_length',
            89 => 'ship_possible_boxes',
            90 => 'ship_price_com',
            91 => 'ship_price_res',
            92 => 'ship_separately',
            93 => 'ship_width',
            94 => 'short_description',
            95 => 'sibling_product_skus',
            96 => 'size',
            97 => 'size_family',
            98 => 'size_fit',
            99 => 'small_image',
            100 => 'small_image_label',
            101 => 'special_from_date',
            102 => 'special_price',
            103 => 'special_shipping_group',
            140 => 'special_to_date',
            105 => 'split_product',
            106 => 'status',
            107 => 'subregion',
            108 => 'sub_category',
            109 => 'tax_class_id',
            110 => 'thumbnail',
            111 => 'thumbnail_label',
            112 => 'unit_of_measure',
            113 => 'updated_at',
            114 => 'url_key',
            115 => 'url_path',
            116 => 'use_simple_product_pricing',
            117 => 'visibility',
            118 => 'volume_weight',
            119 => 'webitem',
            120 => 'web_item',
            121 => 'weight',
            122 => 'year',
            123 => 'qty',
            124 => 'min_qty',
            125 => 'use_config_min_qty',
            126 => 'is_qty_decimal',
            127 => 'backorders',
            128 => 'use_config_backorders',
            129 => 'min_sale_qty',
            130 => 'use_config_min_sale_qty',
            131 => 'max_sale_qty',
            132 => 'use_config_max_sale_qty',
            133 => 'is_in_stock',
            134 => 'notify_stock_qty',
            135 => 'use_config_notify_stock_qty',
            136 => 'manage_stock',
            137 => 'use_config_manage_stock',
            138 => 'stock_status_changed_auto',
            139 => 'use_config_qty_increments',
            140 => 'qty_increments',
            141 => 'use_config_enable_qty_inc',
            142 => 'enable_qty_increments',
            143 => 'is_decimal_divided',
            144 => '_links_related_sku',
            145 => '_links_related_position',
            146 => '_links_crosssell_sku',
            147 => '_links_crosssell_position',
            148 => '_links_upsell_sku',
            149 => '_links_upsell_position',
            150 => '_associated_sku',
            151 => '_associated_default_qty',
            152 => '_associated_position',
            153 => '_tier_price_website',
            154 => '_tier_price_customer_group',
            155 => '_tier_price_qty',
            156 => '_tier_price_price',
            157 => '_group_price_website',
            158 => '_group_price_customer_group',
            159 => '_group_price_price',
            160 => '_media_attribute_id',
            161 => '_media_image',
            162 => '_media_lable',
            163 => '_media_position',
            164 => '_media_is_disabled',
            165 => '_custom_option_store',
            166 => '_custom_option_type',
            167 => '_custom_option_title',
            168 => '_custom_option_is_required',
            169 => '_custom_option_price',
            170 => '_custom_option_sku',
            171 => '_custom_option_max_characters',
            172 => '_custom_option_sort_order',
            173 => '_custom_option_row_title',
            174 => '_custom_option_row_price',
            175 => '_custom_option_row_sku',
            176 => '_custom_option_row_sort',
            177 => '_super_products_sku',
            178 => '_super_attribute_code',
            179 => '_super_attribute_option',
            180 => '_super_attribute_price_corr'
        ];
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
            0 => 'supplier_code',
            1 => 'supplier_name',
            2 => 'contact_name',
            3 => 'contact_email',
            4 => 'description',
            5 => 'status',
            6 => 'telephone',
            7 => 'fax',
            8 => 'street',
            9 => 'city',
            10 => 'country_id',
            11 => 'region_id',
            12 => 'postcode',
            13 => 'website',
            14 => 'account_number',
            15 => 'tax_number',
            16 => 'terms'
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
    
    /**
     * Save supplier
     *
     * @param $dataRow
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function saveSupplier($dataRow)
    {
        $supplierCode = $dataRow[0];
        $supplierName = $dataRow[1];
        $contactName = $dataRow[2];
        $contactEmail = $dataRow[3];
        $description = $dataRow[4];
        $status = $dataRow[5];
        $telephone = $dataRow[6];
        $fax = $dataRow[7];
        $street = $dataRow[8];
        $city = $dataRow[9];
        $countryId = $dataRow[10];
        $regionId = $dataRow[11];
        $postcode = $dataRow[12];
        $website = $dataRow[13];
        $accountNumber = $dataRow[14];
        $taxNumber = $dataRow[15];
        $terms = $dataRow[16];
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        /** @var \Magento\Directory\Model\Region $region */
        $region = $objectManager->create(\Magento\Directory\Model\Region::class);
        if ($regionId) {
            $regionId = $region->loadByCode($regionId, $countryId)->getId();
        }
        
        if (!$status) {
            $status = \Magestore\SupplierSuccess\Service\SupplierService::STATUS_ENABLE;
        }
        
        /** @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier $supplierResourceModel */
        $supplierResourceModel = $objectManager->create(
            \Magestore\SupplierSuccess\Model\ResourceModel\Supplier::class
        );
        /** @var \Magestore\SupplierSuccess\Model\Supplier $supplier */
        $supplier = $objectManager->create(
            \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Collection::class
        )
                                  ->addFieldToFilter('supplier_code', $supplierCode)
                                  ->setPageSize(1)
                                  ->setCurPage(1)
                                  ->getFirstItem();
        
        $supplier->setSupplierCode($supplierCode)
                 ->setSupplierName($supplierName)
                 ->setContactName($contactName)
                 ->setContactEmail($contactEmail)
                 ->setDescription($description)
                 ->setStatus($status)
                 ->setTelephone($telephone)
                 ->setFax($fax)
                 ->setStreet($street)
                 ->setCity($city)
                 ->setCountryId($countryId)
                 ->setRegionId($regionId)
                 ->setPostcode($postcode)
                 ->setWebsite($website)
                 ->setAccountNumber($accountNumber)
                 ->setTaxNumber($taxNumber)
                 ->setTerms($terms);
        
        $supplierResourceModel->save($supplier);
        
        return true;
        
    }
    
}
