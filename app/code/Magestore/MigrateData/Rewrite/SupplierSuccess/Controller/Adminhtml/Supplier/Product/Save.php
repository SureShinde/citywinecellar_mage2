<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\MigrateData\Rewrite\SupplierSuccess\Controller\Adminhtml\Supplier\Product;

class Save extends \Magestore\SupplierSuccess\Controller\Adminhtml\Supplier\Product\Save
{
    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
//        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
//        $supplierProductCollection = $objectManager->create(\Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product\Collection::class);
//        $supplierProductCollection->addFieldToFilter('item_lookup_code', array('nin' =>''));
//        foreach ($supplierProductCollection as $supplierProduct) {
//            $productId = $supplierProduct->getProductId();
//            $product = $objectManager->create(\Magento\Catalog\Model\Product::class)
//                ->load($productId);
//            $supplierProduct->setItemLookupCode($product->getItemLookupCode())
//                ->save();
//        }
//        die('111');
        
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $data = $this->getRequest()->getPostValue();
        if (isset($data['selected']) || (isset($data['excluded']))) {
            $supplierId = $this->getRequest()->getParam('supplier_id');
            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
            $productCollection = \Magento\Framework\App\ObjectManager::getInstance()->create(
                '\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory'
            )->create();
            $productCollection->addAttributeToSelect(['name','item_lookup_code']);
            $filter = \Magento\Framework\App\ObjectManager::getInstance()->create(
                'Magento\Ui\Component\MassAction\Filter'
            );
            $productCollection = $filter->getCollection($productCollection);
            $dataProductUpdate = [];
            $productIds = [];
            /** @var \Magento\Catalog\Model\Product $product */
            foreach ($productCollection as $product) {
                $dataUpdate['supplier_id'] = $supplierId;
                $dataUpdate['product_id'] = $product->getId();
                $dataUpdate['product_sku'] = $product->getSku();
                $dataUpdate['product_name'] = $product->getName();
                $dataUpdate['item_lookup_code'] = $product->getItemLookupCode();
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
