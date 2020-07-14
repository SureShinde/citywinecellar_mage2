<?php

namespace Magestore\PurchaseOrderSuccess\Service\ReturnOrder\Item;

use Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemInterface;
use Magestore\SupplierSuccess\Api\Data\SupplierProductInterface;
use Magestore\SupplierSuccess\Service\Supplier\ProductService;

class ImportService {

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
     * @var \Magestore\PurchaseOrderSuccess\Api\ReturnOrderItemRepositoryInterface
     */
    protected $returnOrderItemRepository;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Model\ReturnOrder\ItemFactory
     */
    protected $returnItemFactory;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\ReturnOrderRepositoryInterface
     */
    protected $returnOrderRepository;

    /**
     * @var array
     */
    protected $updateFields = [
        ReturnOrderItemInterface::QTY_TRANSFERRED
    ];

    /**
     * ProductService constructor.
     * @param \Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item\CollectionFactory $itemCollectioFactory
     */
    public function __construct(
        \Magento\Framework\File\Csv $csvProcessor,
        ItemService $itemService,
        \Magestore\SupplierSuccess\Service\Supplier\ProductService $supplierProductService,
        \Magestore\PurchaseOrderSuccess\Api\ReturnOrderItemRepositoryInterface $returnOrderItemRepository,
        \Magestore\PurchaseOrderSuccess\Model\ReturnOrder\ItemFactory $returnItemFactory,
        \Magestore\PurchaseOrderSuccess\Api\ReturnOrderRepositoryInterface $returnOrderRepository
    ){
        $this->csvProcessor = $csvProcessor;
        $this->itemService = $itemService;
        $this->supplierProductService = $supplierProductService;
        $this->returnOrderItemRepository = $returnOrderItemRepository;
        $this->returnItemFactory = $returnItemFactory;
        $this->returnOrderRepository = $returnOrderRepository;
    }

    /**
     * @param $dataRow
     * @param $returnId
     * @param $supplierId
     * @return bool
     */
    public function savePurchaseItem($dataRow, $returnId, $supplierId)
    {
        $productSku = $dataRow[0];
        $qtyReturned = $dataRow[1];
        $returnOrder = $this->returnOrderRepository->get($returnId);
        $listProductOnCurrentWarehouse = $this->itemService->getProductIdOnCurrentWarehouse($returnOrder->getWarehouseId());
        $productId = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magestore\SupplierSuccess\Service\Supplier\ProductService')
            ->getProductsBySupplierId($supplierId)
            ->addFieldToFilter('product_sku', $productSku)
            ->setPageSize(1)
            ->setCurPage(1)
            ->getFirstItem()
            ->getProductId();

        if(!in_array($productId, $listProductOnCurrentWarehouse)) {
            return false;
        }

        // change qty import => qty returned always equal or litter than available qty on warehouse
//        $curProductQtyInWarehouse = $this->itemService->getQtyProductInWarehouse($returnOrder->getWarehouseId(), $productId);
//        if($qtyReturned > $curProductQtyInWarehouse) {
//            $qtyReturned = $curProductQtyInWarehouse;
//        }

        /**
         * @var \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemInterface $item
         */
        $item = $this->itemService->getProductsByReturnOrderId($returnId, [$productId])
            ->getFirstItem();
        if ($item->getReturnItemId()) {
            $item->setQtyReturned($qtyReturned);
            try {
                $this->returnOrderItemRepository->save($item);
            }catch (\Exception $e){
                return false;
            }
            return true;
        }
        return $this->saveNewItem($returnId, $supplierId, $productId, $qtyReturned);
    }

    /**
     * @param $returnId
     * @param $supplierId
     * @param $productId
     * @param $qtyReturned
     * @return bool
     */
    public function saveNewItem($returnId, $supplierId, $productId, $qtyReturned){
        /**
         * @var \Magestore\SupplierSuccess\Api\Data\SupplierProductInterface $product
         */
        $product = $this->supplierProductService->getProductsBySupplierId($supplierId, [$productId])
            ->getFirstItem();
        if(!$product->getId())
            return false;
        $item = $this->returnItemFactory->create();
        $item->setReturnId($returnId)
            ->setProductId($productId)
            ->setProductSku($product->getProductSku())
            ->setProductName($product->getProductName())
            ->setProductSupplierSku($product->getProductSupplierSku())
            ->setQtyReturned($qtyReturned)
            ->setId(null);
        try {
            $this->returnOrderItemRepository->save($item);
        }catch (\Exception $e){
            return false;
        }
        return true;
    }

    /**
     * @param $file
     * @param int $returnId
     * @param int $supplierId
     * @return $this
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function import($file, $returnId, $supplierId){
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
            if($this->savePurchaseItem($dataRow, $returnId, $supplierId)){
                $success++;
            }
        }
        $this->calculateQuantity($returnId);
        return $success;
    }

    public function calculateQuantity($returnId) {
        $returnOrder = $this->returnOrderRepository->get($returnId);
        $returnItems = $returnOrder->getItems();
        $qtyReturned = 0;
        foreach ($returnItems as $item) {
            $qtyReturned += $item->getQtyReturned()*1;
        }
        $returnOrder->setTotalQtyReturned($qtyReturned);
        $this->returnOrderRepository->save($returnOrder);
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

    public function getRequiredCsvFields()
    {
        // indexes are specified for clarity, they are used during import
        return [
            0 => 'PRODUCT_SKU',
            1 => 'QTY_RETURNED'
        ];
    }

    /**
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
            foreach ($dataRow as $fieldIndex => $fieldValue) {
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