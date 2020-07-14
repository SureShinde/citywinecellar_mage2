<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\MigrateData\Controller\Adminhtml\Migratedata\Supplierproduct;

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
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $csvProcessor = $objectManager->create(\Magento\Framework\File\Csv::class);
        
        $params = $this->getRequest()->getParams();
        $file = $this->getRequest()->getFiles('migrate_supplier_product');
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
            if ($this->saveSupplierProduct($dataRow)) {
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
            1 => 'product_sku',
            2 => 'product_supplier_sku',
            3 => 'cost',
            4 => 'tax',
            5 => 'minimum_order',
            6 => 'master_pack_quantity'
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
     * Save supplier product
     *
     * @param $dataRow
     * @return bool
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function saveSupplierProduct($dataRow)
    {
        $supplierCode = $dataRow[0];
        $productSku = $dataRow[1];
        $productSupplierSku = $dataRow[2];
        $cost = $dataRow[3];
        $tax = $dataRow[4];
        $minimumOrder = $dataRow[5];
        $masterPackQuantity = $dataRow[6];
        
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        /** @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product $supplierProductResourceModel */
        $supplierProductResourceModel = $objectManager->create(
            \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product::class
        );
        
        /** @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\PricingList $supplierPricingResourceModel */
        $supplierPricingResourceModel = $objectManager->create(
            \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\PricingList::class
        );
        /** @var \Magestore\SupplierSuccess\Model\Supplier $supplier */
        $supplier = $objectManager->create(
            \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Collection::class
        )
                                  ->addFieldToFilter('supplier_code', $supplierCode)
                                  ->setPageSize(1)
                                  ->setCurPage(1)
                                  ->getFirstItem();
//        if (!$supplier->getId()) {
//            $supplier = $objectManager->create(\Magestore\SupplierSuccess\Model\Supplier::class);
//        }
        
        if ($supplier->getId()) {
            $supplierId = $supplier->getId();
            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
            $productCollection = $objectManager->create(
                \Magento\Catalog\Model\ResourceModel\Product\Collection::class
            );
            /** @var \Magento\Catalog\Model\Product $product */
            $product = $productCollection->addAttributeToSelect(['entity_id', 'sku', 'name'])
                ->addAttributeToFilter('sku', $productSku)
                ->setPageSize(1)
                ->setCurPage(1)
                ->getFirstItem();
            if ($product->getId()) {
                $productId = $product->getId();
                $productName = $product->getName();
                
                /** @var \Magestore\SupplierSuccess\Model\Supplier\Product $supplierProduct */
                $supplierProduct = $objectManager->create(
                    \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product\Collection::class
                )
                                          ->addFieldToFilter('supplier_id', $supplierId)
                                          ->addFieldToFilter('product_id', $productId)
                                          ->setPageSize(1)
                                          ->setCurPage(1)
                                          ->getFirstItem();
                
                $supplierProduct->setSupplierId($supplierId)
                    ->setProductId($productId)
                    ->setProductSku($productSku)
                    ->setProductName($productName)
                    ->setProductSupplierSku($productSupplierSku)
                    ->setCost($cost)
                    ->setTax($tax)
                    ->setMinimumOrder($minimumOrder)
                    ->setMasterPackQuantity($masterPackQuantity);
                $supplierProductResourceModel->save($supplierProduct);
                
                /** @var \Magestore\SupplierSuccess\Model\Supplier\PricingList $supplierPricing */
                $supplierPricing = $objectManager->create(
                    \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\PricingList\Collection::class
                )
                                                 ->addFieldToFilter('supplier_id', $supplierId)
                                                 ->addFieldToFilter('product_id', $productId)
                                                 ->addFieldToFilter('minimal_qty', $minimumOrder)
                                                 ->setPageSize(1)
                                                 ->setCurPage(1)
                                                 ->getFirstItem();
                $supplierPricing->setSupplierId($supplierId)
                    ->setProductId($productId)
                    ->setProductSku($productSku)
                    ->setProductName($productName)
                    ->setProductSupplierSku($productSupplierSku)
                    ->setMinimalQty($minimumOrder)
                    ->setCost($cost);
    
                $supplierPricingResourceModel->save($supplierPricing);
                
                return true;
            } else {
                var_dump($productSku);
            }
        }
        
        return false;
        
    }
    
}
