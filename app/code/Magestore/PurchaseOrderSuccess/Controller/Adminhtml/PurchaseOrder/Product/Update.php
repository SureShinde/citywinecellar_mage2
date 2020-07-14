<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Controller\Adminhtml\PurchaseOrder\Product;

/**
 * Class Update
 * @package Magestore\PurchaseOrderSuccess\Controller\Adminhtml\PurchaseOrder\Product
 */
class Update extends \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\AbstractAction
{
    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item\ItemService
     */
    protected $itemService;

    /**
     * @var \Magestore\SupplierSuccess\Service\Supplier\ProductService
     */
    protected $supplierProductService;
    
    public function __construct(
        \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Context $context,
        \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item\ItemService $itemService,
        \Magestore\SupplierSuccess\Service\Supplier\ProductService $supplierProductService
    ) {
        parent::__construct($context);
        $this->itemService = $itemService;
        $this->supplierProductService = $supplierProductService;
    }
    
    /**
     * Save product to purchase order
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
//        $params = $this->getRequest()->getParams();
//        $productData = $this->itemService->processUpdateProductParams($params);
//        $suppplierProductCollection = $this->supplierProductService
//            ->getProductsBySupplierId($params['supplier_id'], $productIds);
//        $this->itemService->addProductToPurchaseOrder($params['purchase_id'],$suppplierProductCollection->getData());
    }
}