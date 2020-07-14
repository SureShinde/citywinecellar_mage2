<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Service\Supplier;

use Magestore\SupplierSuccess\Service\AbstractService;

class ProductService extends AbstractService
{

    /**
     * @param $data
     */
    public function assignProductToSupplier($data)
    {
        /** @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product $supplierProductResource */
        $supplierProductResource = $this->_supplierProductResourceFactory->create();
        $supplierProductResource->addProducts($data);
    }

    /**
     * @param $supplierId
     * @param array $productIds
     * @return \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product\Collection
     */
    public function getProductsBySupplierId($supplierId, $productIds = [])
    {
        /** @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product\Collection $productCollection */
        $productCollection = $this->_supplierProductCollectionFactory->create();
        $productCollection->addFieldToFilter('supplier_id', $supplierId);
        if (!empty($productIds)) {
            $productCollection->addFieldToFilter('product_id', ['in' => $productIds]);
        }
        return $productCollection;
    }

    /**
     * @param $supplierId
     * @param $file
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function importProductToSupplier($supplierId, $file)
    {
        if (!isset($file['tmp_name'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file upload attempt.'));
        }
        $dataFile = $this->csvProcessor->getData($file['tmp_name']);
        $importProductData = [];
        if (count($dataFile)) {
            foreach ($dataFile as $col => $row) {
                if ($col == 0) {
                    if (count($row)) {
                        foreach ($row as $index => $cell) {
                            $fields[$index] = (string)$cell;
                        }
                    }
                } elseif ($col > 0) {
                    if (count($row)) {
                        foreach ($row as $index => $cell) {
                            if (isset($fields[$index])) {
                                $importData[strtolower($fields[$index])] = $cell;
                            }
                        }
                        /** @var \Magento\Catalog\Api\ProductRepositoryInterface $productRepository */
                        $productRepository = $this->objectManager->get(
                            '\Magento\Catalog\Api\ProductRepositoryInterface'
                        );
                        /** @var \Magento\Catalog\Model\Product $product */
                        try {
                            $product = $productRepository->get($importData['product_sku']);
                            if ($product->getId()) {
                                $importData['supplier_id'] = $supplierId;
                                $importData['product_id'] = $product->getId();
                                $importData['product_name'] = $product->getName();
                                $importProductData[] = $importData;
                            }
                        }catch (\Exception $e){
                            continue;
                        }
                    }
                }
            }
            if (count($importProductData)) {
                /** @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product $supplierProductResource */
                $supplierProductResource = $this->_supplierProductResourceFactory->create();
                $supplierProductResource->addProducts($importProductData);
                return count($importProductData);
            }
        }
        return 0;
    }

    /**
     *
     * @return array = [
     *  'supplier_id' => int,
     *  'product_id' => int,
     *  'product_sku' => string,
     *  'product_name' => string,
     *  'product_supplier_sku' => string,
     *  'cost' => float,
     *  'tax' => float
     * ]
     */
    public function getRequiredCsvFields()
    {
        // indexes are specified for clarity, they are used during import
        return [
            ('supplier_id'),
            ('product_id'),
            ('product_sku'),
            ('product_name'),
            ('product_supplier_sku'),
            ('cost'),
            ('tax'),
        ];
    }

}