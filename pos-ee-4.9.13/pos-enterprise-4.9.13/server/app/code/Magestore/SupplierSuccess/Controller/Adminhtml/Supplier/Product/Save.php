<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Controller\Adminhtml\Supplier\Product;

class Save extends \Magestore\SupplierSuccess\Controller\Adminhtml\AbstractSupplier
{
    const ADMIN_RESOURCE = 'Magestore_SupplierSuccess::view_supplier';

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $data = $this->getRequest()->getPostValue();
        if (isset($data['selected']) || (isset($data['excluded']))) {
            $supplierId = $this->getRequest()->getParam('supplier_id');
            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
            $productCollection = \Magento\Framework\App\ObjectManager::getInstance()->create(
                '\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory'
            )->create();
            $productCollection->addAttributeToSelect('name');
            $filter = \Magento\Framework\App\ObjectManager::getInstance()->create(
                'Magento\Ui\Component\MassAction\Filter'
            );
            $productCollection = $filter->getCollection($productCollection);
//            if (isset($data['selected'])) {
//                $productCollection->addAttributeToFilter('entity_id', ['in' => $data['selected']]);
//            }
//            if (isset($data['excluded']) && $data['excluded'] != 'false') {
//                $supplierProductIds = [0];
//                /** @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product\Collection $supplierProductCollection */
//                $supplierProductCollection = $this->_supplierProductService->getProductsBySupplierId($supplierId);
//                if ($supplierProductCollection->getSize())
//                    $supplierProductIds = $supplierProductCollection->getColumnValues('product_id');
//                $productCollection->addAttributeToFilter('entity_id', ['nin' => $data['excluded']])
//                    ->addAttributeToFilter('entity_id', ['nin' => $supplierProductIds]);
//            }
//            if (isset($data['excluded']) && $data['excluded'] == 'false') {
//                if ($supplierId) {
//                    $supplierProductIds = [0];
//                    /** @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product\Collection $supplierProductCollection */
//                    $supplierProductCollection = $this->_supplierProductService->getProductsBySupplierId($supplierId);
//                    if ($supplierProductCollection->getSize())
//                        $supplierProductIds = $supplierProductCollection->getColumnValues('product_id');
//                    $productCollection->addAttributeToFilter('entity_id', ['nin' => $supplierProductIds]);
//                }
//            }
            $dataProductUpdate = [];
            $productIds = [];
            /** @var \Magento\Catalog\Model\Product $product */
            foreach ($productCollection as $product) {
                $dataUpdate['supplier_id'] = $supplierId;
                $dataUpdate['product_id'] = $product->getId();
                $dataUpdate['product_sku'] = $product->getSku();
                $dataUpdate['product_name'] = $product->getName();

                $productIds[] = $product->getId();
                $dataProductUpdate[] = $dataUpdate;
            }
            if (!empty($dataProductUpdate)) {
                try {
                    /** set new products that added to supplier to select */
                    $this->locator->setSession(\Magestore\SupplierSuccess\Api\Data\SupplierProductInterface::SUPPLIER_PRODUCT_ADD_NEW, $productIds);
                    $this->_supplierProductService->assignProductToSupplier($dataProductUpdate);
                    $this->messageManager->addSuccessMessage(__('New products in this supplier have been added.'));
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(__($e->getMessage()));
                }
            }
        }
    }
}
