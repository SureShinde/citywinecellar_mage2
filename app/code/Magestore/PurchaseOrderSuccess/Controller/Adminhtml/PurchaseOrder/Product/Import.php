<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Controller\Adminhtml\PurchaseOrder\Product;

use Magento\Framework\Message\MessageInterface;

/**
 * Class Import
 * @package Magestore\PurchaseOrderSuccess\Controller\Adminhtml\PurchaseOrder\Product
 */
class Import extends \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\AbstractAction
{
    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item\ItemService
     */
    protected $itemService;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item\ImportService
     */
    protected $importService;

    /**
     * @var \Magestore\SupplierSuccess\Service\Supplier\ProductService
     */
    protected $supplierProductService;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\PurchaseOrderService
     */
    protected $purchaseService;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface
     */
    protected $orderRepositoryInterface;

    /**
     * Import constructor.
     * @param \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Context $context
     * @param \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item\ItemService $itemService
     * @param \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item\ImportService $importService
     * @param \Magestore\SupplierSuccess\Service\Supplier\ProductService $supplierProductService
     * @param \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\PurchaseOrderService $purchaseService
     */
    public function __construct(
        \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Context $context,
        \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item\ItemService $itemService,
        \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item\ImportService $importService,
        \Magestore\SupplierSuccess\Service\Supplier\ProductService $supplierProductService,
        \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\PurchaseOrderService $purchaseService,
        \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface $orderRepositoryInterface
    ) {
        parent::__construct($context);
        $this->itemService = $itemService;
        $this->importService = $importService;
        $this->supplierProductService = $supplierProductService;
        $this->purchaseService = $purchaseService;
        $this->orderRepositoryInterface = $orderRepositoryInterface;
    }

    /**
     * Save product to purchase order
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        try {
            $purchaseOrder = $this->orderRepositoryInterface->get($params['purchase_id']);
            $success = $this->importService->import(
                $this->getRequest()->getFiles('import_product'), $params['purchase_id'], $params['supplier_id']
            );
        }catch (\Exception $e){
            return $this->redirectForm(
                $params['type'], 
                $params['purchase_id'], 
                $e->getMessage(), 
                MessageInterface::TYPE_ERROR
            );
        }
        if($success>0){
            $this->purchaseService->updatePurchaseTotal($purchaseOrder);
            return $this->redirectForm(
                $params['type'], $params['purchase_id'],  __('%1 item has been imported.', $success)
            );
        }
        return $this->redirectForm(
            $params['type'],
            $params['purchase_id'],  
            __('No item has been imported.'), 
            MessageInterface::TYPE_WARNING
        );
    }
}