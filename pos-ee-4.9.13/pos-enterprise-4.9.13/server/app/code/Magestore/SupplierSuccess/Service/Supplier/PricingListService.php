<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Service\Supplier;

use Magestore\SupplierSuccess\Service\AbstractService;

class PricingListService extends AbstractService
{

    /**
     * @return array
     */
    public function getSupplierOptions()
    {
        /** @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Collection $supplierColletion */
        $supplierColletion = $this->supplierCollectionFactory->create();
        $suppliers = [];
        /** @var \Magestore\SupplierSuccess\Model\Supplier $supplier */
        foreach ($supplierColletion as $supplier) {
            $suppliers[$supplier->getId()] = $supplier->getSupplierName() . '('.$supplier->getSupplierCode().')';
        }
        return $suppliers;
    }

    /**
     * @param $file
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function importPricingListToSupplier($file)
    {
        if (!isset($file['tmp_name'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file upload attempt.'));
        }
        $dataFile = $this->csvProcessor->getData($file['tmp_name']);
        $importPricingListData = [];
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
    //                        $product = $this->getProductBySku($importData['product_sku']);
                            if ($product->getId()) {
                                /** @var \Magestore\SupplierSuccess\Service\SupplierService $supplierService */
                                $supplierService = $this->objectManager->get(
                                    '\Magestore\SupplierSuccess\Service\SupplierService'
                                );
                                $supplier = $supplierService->getSupplierBySupplierCode($importData['supplier_code']);
                                if ($supplier->getId()) {
                                    $importData['supplier_id'] = $supplier->getId();
                                    unset($importData['supplier_code']);
                                    $importData['product_id'] = $product->getId();
                                    $importData['product_name'] = $product->getName();
                                    $importPricingListData[] = $importData;
                                }
                            }
                        }catch (\Exception $e) {

                        }
                    }
                }
            }
//            \Zend_Debug::dump($importPricingListData);die();
            if (count($importPricingListData)) {
                $this->addPricingList($importPricingListData);
            }
        }
    }

    /**
     * @param $data
     */
    public function addPricingList($data)
    {
        /** @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\PricingList $supplierPricingListResource */
        $supplierPricingListResource = $this->supplierPricingListResourceFactory->create();
        $supplierProductData = [];
        foreach ($data as $sp) {
            $sPData['supplier_id'] = $sp['supplier_id'];
            $sPData['product_id'] = $sp['product_id'];
            $sPData['product_sku'] = $sp['product_sku'];
            $sPData['product_name'] = $sp['product_name'];
            $supplierProductData[] = $sPData;
        }
        /** @var \Magestore\SupplierSuccess\Service\Supplier\ProductService $supplierProductService */
        $supplierProductService = $this->objectManager->get(
            '\Magestore\SupplierSuccess\Service\Supplier\ProductService'
        );
        $supplierProductService->assignProductToSupplier($supplierProductData);
        $supplierPricingListResource->addPricingList($data);
    }

    /**
     *
     * @return array = [
     *  'supplier_id' => int,
     *  'product_id' => int,
     *  'product_sku' => string,
     *  'product_name' => string,
     *  'product_supplier_sku' => string,
     *  'minimal_qty' => decimal,
     *  'cost' => float,
     *  'start_date' => date format Y-m-d
     *  'end_date' => date format Y-m-d
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
            ('minimal_qty'),
            ('cost'),
            ('start_date'),
            ('end_date')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getProductBySku($sku, $editMode = false, $storeId = null, $forceReload = false)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product =  $this->objectManager->get(
            '\Magento\Catalog\Model\ProductFactory'
        )->create();

        /** @var \Magento\Catalog\Model\ResourceModel\Product $resourceProduct */
        $resourceProduct =  $this->objectManager->get(
            '\Magento\Catalog\Model\ResourceModel\ProductFactory'
        )->create();
        $productId = $resourceProduct->getIdBySku($sku);
        if ($productId)
            $resourceProduct->load($product, $productId);
        return $product;
    }

    /**
     * @param $productSku
     * @param $supplierId
     * @param null $time
     * @return array
     */
    public function getProductCost($productSku = null, $supplierId, $time = null)
    {
        /** @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\PricingList\Collection $pricingListCollection */
        $pricingListCollection = $this->supplierPricingListCollectionFactory->create();
        return $pricingListCollection->getProductCost($productSku, $supplierId, $time);
    }
}