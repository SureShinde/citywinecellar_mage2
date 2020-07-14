<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item;

use Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface;
use Magestore\SupplierSuccess\Service\Supplier\ProductService;
use Magestore\PurchaseOrderSuccess\Model\System\Config\ProductSource;

/**
 * Class ItemService
 *
 * @package Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Product
 */
class ImportService
{
    /**
     * CSV Processor
     *
     * @var \Magento\Framework\File\Csv
     */
    protected $csvProcessor;

    /**
     * @var ItemService
     */
    protected $itemService;

    /**
     * @var \Magestore\SupplierSuccess\Service\Supplier\ProductService
     */
    protected $supplierProductService;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderItemRepositoryInterface
     */
    protected $purchaseOrderItemRepository;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\ItemFactory
     */
    protected $purchaseItemFactory;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\ProductService
     */
    protected $productService;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\Config\ProductConfig
     */
    protected $productConfig;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var array
     */
    protected $updateFields = [
        PurchaseOrderItemInterface::COST,
        PurchaseOrderItemInterface::TAX,
        PurchaseOrderItemInterface::DISCOUNT,
        PurchaseOrderItemInterface::QTY_ORDERRED
    ];

    /**
     * ImportService constructor.
     * @param \Magento\Framework\File\Csv $csvProcessor
     * @param ItemService $itemService
     * @param ProductService $supplierProductService
     * @param \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderItemRepositoryInterface $purchaseOrderItemRepository
     * @param \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\ItemFactory $purchaseItemFactory
     * @param \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\ProductService $productService
     * @param \Magestore\PurchaseOrderSuccess\Service\Config\ProductConfig $productConfig
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     */
    public function __construct(
        \Magento\Framework\File\Csv $csvProcessor,
        ItemService $itemService,
        \Magestore\SupplierSuccess\Service\Supplier\ProductService $supplierProductService,
        \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderItemRepositoryInterface $purchaseOrderItemRepository,
        \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\ItemFactory $purchaseItemFactory,
        \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\ProductService $productService,
        \Magestore\PurchaseOrderSuccess\Service\Config\ProductConfig $productConfig,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    ) {
        $this->csvProcessor = $csvProcessor;
        $this->itemService = $itemService;
        $this->supplierProductService = $supplierProductService;
        $this->purchaseOrderItemRepository = $purchaseOrderItemRepository;
        $this->purchaseItemFactory = $purchaseItemFactory;
        $this->productService = $productService;
        $this->productConfig = $productConfig;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    /**
     * Save new PO item
     *
     * @param array $dataRow
     * @param int $purchaseId
     * @param int $supplierId
     * @return bool
     */
    public function savePurchaseItem($dataRow, $purchaseId, $supplierId)
    {
        $productSku = $dataRow[0];
        $cost = $dataRow[1];
        $tax = $dataRow[2];
        $discount = $dataRow[3];
        $qtyOrderred = abs($dataRow[4]);
        if ($this->productConfig->getProductSource() == ProductSource::TYPE_SUPPLIER) {
            $productId = \Magento\Framework\App\ObjectManager::getInstance()
                ->create(\Magestore\SupplierSuccess\Service\Supplier\ProductService::class)
                ->getProductsBySupplierId($supplierId)
                ->addFieldToFilter('product_sku', $productSku)
                ->setPageSize(1)
                ->setCurPage(1)
                ->getFirstItem()
                ->getProductId();
        } else {
            $productId = $this->productCollectionFactory->create()
                ->addFieldToFilter('sku', $productSku)
                ->setPageSize(1)
                ->setCurPage(1)
                ->getFirstItem()
                ->getId();
        }

        /**
         * @var \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface $item
         */
        $item = $this->itemService->getProductsByPurchaseOrderId($purchaseId, [$productId])
            ->getFirstItem();
        if ($item->getId()) {
            $item->setCost($cost)->setTax($tax)->setDiscount($discount)->setQtyOrderred($qtyOrderred);
            try {
                $this->purchaseOrderItemRepository->save($item);
            } catch (\Exception $e) {
                return false;
            }
            return true;
        }
        return $this->saveNewItem($purchaseId, $supplierId, $productId, $cost, $tax, $discount, $qtyOrderred);
    }

    /**
     * Save a new PO item
     *
     * @param int $purchaseId
     * @param int $supplierId
     * @param int $productId
     * @param float $cost
     * @param float $tax
     * @param float $discount
     * @param float $qtyOrderred
     * @return bool
     */
    public function saveNewItem($purchaseId, $supplierId, $productId, $cost, $tax, $discount, $qtyOrderred)
    {
        /**
         * @var \Magestore\SupplierSuccess\Api\Data\SupplierProductInterface $product
         */
        if ($this->productConfig->getProductSource() == ProductSource::TYPE_SUPPLIER) {
            $product = $this->supplierProductService->getProductsBySupplierId($supplierId, [$productId])
                ->getFirstItem()->getData();
            if (!isset($product['supplier_product_id'])) {
                return false;
            }
        } else {
            $product = current($this->productService->prepareProductForPO([$productId], $supplierId));
            if (!isset($product['entity_id'])) {
                return false;
            }
        }
        $item = $this->purchaseItemFactory->create();
        $item->setPurchaseOrderId($purchaseId)
            ->setProductId($productId)
            ->setProductSku($product['product_sku'])
            ->setProductName($product['product_name'])
            ->setProductSupplierSku($product['product_supplier_sku'])
            ->setQtyOrderred($qtyOrderred)
            ->setOriginalCost($product['cost'])
            ->setCost($cost)
            ->setTax($tax)
            ->setDiscount($discount)
            ->setId(null);
        try {
            $this->purchaseOrderItemRepository->save($item);
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Import execute
     *
     * @param mixed $file
     * @param int $purchaseId
     * @param int $supplierId
     * @return $this
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function import($file, $purchaseId, $supplierId)
    {
        if (!isset($file['tmp_name'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file upload attempt.'));
        }
        $success = 0;
        $importRawData = $this->csvProcessor->getData($file['tmp_name']);
        $fileFields = $importRawData[0];
        $validFields = $this->filterFileFields($fileFields);
        $invalidFields = array_diff_key($fileFields, $validFields);
        $importData = $this->filterImportData($importRawData, $invalidFields, $validFields);
        foreach ($importData as $rowIndex => $dataRow) {
            // skip headers
            if ($rowIndex == 0) {
                continue;
            }
            if ($this->savePurchaseItem($dataRow, $purchaseId, $supplierId)) {
                $success++;
            }
        }
        return $success;
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
            0 => 'PRODUCT_SKU',
            1 => 'COST',
            2 => 'TAX',
            3 => 'DISCOUNT',
            4 => 'QTY_ORDERRED'
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
}
