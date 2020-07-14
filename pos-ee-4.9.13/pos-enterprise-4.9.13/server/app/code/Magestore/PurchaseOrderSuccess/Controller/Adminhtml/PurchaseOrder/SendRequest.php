<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Controller\Adminhtml\PurchaseOrder;

/**
 * Class SendRequest
 * @package Magestore\PurchaseOrderSuccess\Controller\Adminhtml\PurchaseOrder
 */
class SendRequest extends \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\AbstractAction
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magestore_PurchaseOrderSuccess::send_purchase_order_request';
    
    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface
     */
    protected $purchaseOrderRepository;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\PurchaseOrderService
     */
    protected $purchaseOrderService;

    /**
     * @var \Magestore\SupplierSuccess\Api\SupplierRepositoryInterface
     */
    protected $supplierRepository;

    /**
     * SendRequest constructor.
     * @param \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Context $context
     * @param \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface $purchaseOrderRepository
     * @param \Magestore\SupplierSuccess\Api\SupplierRepositoryInterface $supplierRepository
     * @param \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\PurchaseOrderService $purchaseOrderService
     */
    public function __construct(
        \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Context $context,
        \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface $purchaseOrderRepository,
        \Magestore\SupplierSuccess\Api\SupplierRepositoryInterface $supplierRepository,
        \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\PurchaseOrderService $purchaseOrderService
    ){
        parent::__construct($context);
        $this->purchaseOrderRepository = $purchaseOrderRepository;
        $this->supplierRepository = $supplierRepository;
        $this->purchaseOrderService = $purchaseOrderService;
    }

    /**
     * Quotation grid
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $purchaseId = $this->getRequest()->getParam('purchase_id');
        $type = $this->getRequest()->getParam('type');
        $purchaseOrder = $this->purchaseOrderRepository->get($purchaseId);
        if($purchaseOrder && $purchaseOrder->getPurchaseOrderId()){
            $this->_registry->register('current_purchase_order', $purchaseOrder);
            try{
                $supplier = $this->supplierRepository->getById($purchaseOrder->getSupplierId());
                $this->_registry->register('current_purchase_order_supplier', $supplier);
                if($this->purchaseOrderService->sendEmailToSupplier($purchaseOrder, $supplier))
                    return $this->redirectForm($type, $purchaseId, __('An email has been sent to supplier'));
                else
                    return $this->redirectForm(
                        $type, 
                        $purchaseId, 
                        __('Could not send email to supplier'),
                        \Magento\Framework\Message\MessageInterface::TYPE_ERROR
                    );
            }catch (\Exception $e){
                return $this->redirectForm(
                    $type,
                    $purchaseId,
                    __('Could not find supplier email address'),
                    \Magento\Framework\Message\MessageInterface::TYPE_ERROR
                );
            }
        }else{
            return $this->redirectForm(
                $type, 
                $purchaseId, 
                __('Could not send email to supplier'),
                \Magento\Framework\Message\MessageInterface::TYPE_ERROR
            );
        }
    }

}