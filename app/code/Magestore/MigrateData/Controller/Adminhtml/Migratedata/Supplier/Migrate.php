<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\MigrateData\Controller\Adminhtml\Migratedata\Supplier;

use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Type;
use Magestore\PurchaseOrderSuccess\Model\System\Config\ProductSource;

/**
 * Class Save
 *
 * @package Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Quotation
 */
class Migrate extends \Magento\Backend\App\Action
{
    
    /**
     * Quotation grid
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $csvProcessor = $objectManager->create(\Magento\Framework\File\Csv::class);
        
        $params = $this->getRequest()->getParams();
        $file = $this->getRequest()->getFiles('migrate_supplier');
        $importRawData = $csvProcessor->getData($file['tmp_name']);
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
            9 => 'street_2',
            10 => 'city',
            11 => 'country_id',
            12 => 'region_id',
            13 => 'postcode',
            14 => 'website',
            15 => 'account_number',
            16 => 'tax_number',
            17 => 'terms'
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
        $street2 = $dataRow[9];
        $city = $dataRow[10];
        $countryId = $dataRow[11];
        $regionId = $dataRow[12];
        $postcode = $dataRow[13];
        $website = $dataRow[14];
        $accountNumber = $dataRow[15];
        $taxNumber = $dataRow[16];
        $terms = $dataRow[17];
        
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
                 ->setStreet2($street2)
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
