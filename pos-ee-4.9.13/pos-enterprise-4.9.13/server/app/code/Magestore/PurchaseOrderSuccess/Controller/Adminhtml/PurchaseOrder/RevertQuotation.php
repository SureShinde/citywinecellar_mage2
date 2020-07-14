<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Controller\Adminhtml\PurchaseOrder;

use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Type;
use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Status;

/**
 * Class Confirm
 * @package Magestore\PurchaseOrderSuccess\Controller\Adminhtml\PurchaseOrder
 */
class RevertQuotation extends \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\AbstractAction
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magestore_PurchaseOrderSuccess::revert_quotation';

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

    /**
     * Quotation grid
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $purchaseId = $this->getRequest()->getParam('purchase_order_id');
        $type = $this->getRequest()->getParam('type');
        if($this->purchaseOrderRepository->unConfirm($purchaseId)){
            $purchaseOrder = $this->purchaseOrderRepository->get($purchaseId);
            if($purchaseOrder->getSendEmail()){
                $supplier = $this->supplierRepository->getById($purchaseOrder->getSupplierId());
                $this->_registry->register('current_purchase_order', $purchaseOrder);
                $this->_registry->register('current_purchase_order_supplier', $supplier);
                if(!$this->purchaseService->sendEmailToSupplier($purchaseOrder, $supplier)){
                    $this->messageManager->addErrorMessage('Could not send email to supplier');
                }
            }
            return $this->redirectForm($type, $purchaseId, __('Quotation has been reverted'));
        }else{
            return $this->redirectForm(
                $type,
                $purchaseId, __('Can not revert Quotation'),
                'error',
                \Magento\Framework\Message\MessageInterface::TYPE_ERROR
            );
        }
    }

}