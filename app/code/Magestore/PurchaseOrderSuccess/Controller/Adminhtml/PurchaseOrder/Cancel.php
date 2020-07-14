<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Controller\Adminhtml\PurchaseOrder;

use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Type;

/**
 * Class Save
 * @package Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Quotation
 */
class Cancel extends \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\AbstractAction
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magestore_PurchaseOrderSuccess::cancel_purchase_order';
    
    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface
     */
    protected $purchaseOrderRepository;

    /**
     * @var \Magestore\SupplierSuccess\Api\SupplierRepositoryInterface
     */
    protected $supplierRepository;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\PurchaseOrderService
     */
    protected $purchaseService;

    
    public function __construct(
        \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Context $context,
        \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface $purchaseOrderRepository,
        \Magestore\SupplierSuccess\Api\SupplierRepositoryInterface $supplierRepository,
        \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\PurchaseOrderService $purchaseService
    ){
        parent::__construct($context);
        $this->purchaseOrderRepository = $purchaseOrderRepository;
        $this->supplierRepository = $supplierRepository;
        $this->purchaseService = $purchaseService;
    }

    protected function _isAllowed()
    {
        if($this->getRequest()->getParam('type') == Type::TYPE_QUOTATION) {
            return $this->_authorization->isAllowed('Magestore_PurchaseOrderSuccess::cancel_quotation');
        }

        return parent::_isAllowed();
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
        $typeLabel = $this->getTypeLabel($type);
        if($this->purchaseOrderRepository->cancel($purchaseId)){
            $purchaseOrder = $this->purchaseOrderRepository->get($purchaseId);
            if($purchaseOrder->getSendEmail()){
                $supplier = $this->supplierRepository->getById($purchaseOrder->getSupplierId());
                $this->_registry->register('current_purchase_order', $purchaseOrder);
                $this->_registry->register('current_purchase_order_supplier', $supplier);
                if(!$this->purchaseService->sendEmailToSupplier($purchaseOrder, $supplier)){
                    $this->messageManager->addErrorMessage('Could not send email to supplier');
                }
            }
            return $this->redirectForm($type, $purchaseId, __('%1 has been canceled.', $typeLabel));
        }else{
            return $this->redirectForm(
                $type, 
                $purchaseId, 
                __('Can not cancel this %1.', $typeLabel),
                \Magento\Framework\Message\MessageInterface::TYPE_ERROR
            );
        }
    }
    
}