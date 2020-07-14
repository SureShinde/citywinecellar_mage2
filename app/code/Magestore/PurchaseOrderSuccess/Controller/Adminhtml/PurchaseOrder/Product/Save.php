<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Controller\Adminhtml\PurchaseOrder\Product;

use Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Fieldset\PurchaseSumary\Total;

/**
 * Class Save
 *
 * @package Magestore\PurchaseOrderSuccess\Controller\Adminhtml\PurchaseOrder\Product
 * @SuppressWarnings(PHPMD.AllPurposeAction)
 */
class Save extends \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\AbstractAction
{
    const BLOCK_GRID = Total::class;
    const BLOCK_GRID_NAME = 'purchaseorder.total';
    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item\ItemService
     */
    protected $itemService;

    /**
     * @var \Magestore\SupplierSuccess\Service\Supplier\ProductService
     */
    protected $supplierProductService;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\PurchaseOrderService
     */
    protected $purchaseOrderService;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\ProductService
     */
    protected $productService;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface
     */
    protected $purchaseOrderRepository;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    protected $productSource;

    /**
     * Save constructor.
     * @param \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Context $context
     * @param \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface $purchaseOrderRepository
     * @param \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\PurchaseOrderService $purchaseOrderService
     * @param \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\ProductService $productService
     * @param \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item\ItemService $itemService
     * @param \Magestore\SupplierSuccess\Service\Supplier\ProductService $supplierProductService
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     */
    public function __construct(
        \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Context $context,
        \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface $purchaseOrderRepository,
        \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\PurchaseOrderService $purchaseOrderService,
        \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\ProductService $productService,
        \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item\ItemService $itemService,
        \Magestore\SupplierSuccess\Service\Supplier\ProductService $supplierProductService,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        parent::__construct($context);
        $this->purchaseOrderRepository = $purchaseOrderRepository;
        $this->purchaseOrderService = $purchaseOrderService;
        $this->productService = $productService;
        $this->itemService = $itemService;
        $this->supplierProductService = $supplierProductService;
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
        $this->productSource = $context->getProductSourceConfig();
    }
    
    /**
     * Save product to purchase order
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $productIds = $this->itemService->processIdsProductModal($params);
        if ($this->productSource == \Magestore\PurchaseOrderSuccess\Model\System\Config\ProductSource::TYPE_SUPPLIER) {
            $suppplierProductCollection = $this->supplierProductService
                ->getProductsBySupplierId($params['supplier_id'], $productIds);
            $this->itemService->addProductToPurchaseOrder(
                $params['purchase_id'],
                $suppplierProductCollection->getData(),
                $params
            );
        } else {
            $productData = $this->productService->prepareProductForPO($productIds, $params['supplier_id']);
            $this->itemService->addProductToPurchaseOrder($params['purchase_id'], $productData, $params);
        }
        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        $id = $params['purchase_id'];
        $purchaseOrder = $this->purchaseOrderRepository->get($id);
        $this->purchaseOrderService->updatePurchaseTotal($purchaseOrder);
        try {
            $this->_registry->register('current_purchase_order', $purchaseOrder);
        } catch (\Exception $e) {
            $this->_logger->info($e->getMessage());
        }
        return $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(
                static::BLOCK_GRID,
                static::BLOCK_GRID_NAME
            )->toHtml()
        );
    }
}
