<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Controller\Adminhtml\ReturnOrder\Product;

/**
 * Class Save
 * @package Magestore\PurchaseOrderSuccess\Controller\Adminhtml\ReturnOrder\Product
 */
class Save extends \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\ReturnOrder\AbstractAction
{
    /**
     * @var \Magestore\SupplierSuccess\Service\Supplier\ProductService
     */
    protected $supplierProductService;

    public function execute() {
        $this->initVariable();

        $params = $this->getRequest()->getParams();
        $productIds = $this->itemService->processIdsProductModal($params);
        $suppplierProductCollection = $this->supplierProductService
            ->getProductsBySupplierId($params['supplier_id'], $productIds);
        $this->itemService->addProductToReturnOrder($params['return_id'],$suppplierProductCollection->getData());
    }

    private function initVariable() {
        $this->supplierProductService = $this->_objectManager->get('Magestore\SupplierSuccess\Service\Supplier\ProductService');
    }
}