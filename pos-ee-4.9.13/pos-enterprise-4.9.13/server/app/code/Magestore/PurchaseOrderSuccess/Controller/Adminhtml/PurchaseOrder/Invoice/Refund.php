<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Controller\Adminhtml\PurchaseOrder\Invoice;

/**
 * Class Payment
 * @package Magestore\PurchaseOrderSuccess\Controller\Adminhtml\PurchaseOrder\Invoice
 */
class Refund extends \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\AbstractAction
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magestore_PurchaseOrderSuccess::invoice_purchase_order_refund';

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Invoice\InvoiceService
     */
    protected $invoiceService;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Invoice\Refund\RefundService
     */
    protected $refundService;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Model\Repository\PurchaseOrder\InvoiceRepository
     */
    protected $invoiceRepository;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Model\Repository\PurchaseOrder\Invoice\RefundRepository
     */
    protected $refundRepository;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Invoice\RefundFactory
     */
    protected $refundFactory;

    public function __construct(
        \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Context $context,
        \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Invoice\InvoiceService $invoiceService,
        \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Invoice\Refund\RefundService $refundService,
        \Magestore\PurchaseOrderSuccess\Model\Repository\PurchaseOrder\InvoiceRepository $invoiceRepository,
        \Magestore\PurchaseOrderSuccess\Model\Repository\PurchaseOrder\Invoice\RefundRepository $refundRepository,
        \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Invoice\RefundFactory $refundFactory
    ){
        parent::__construct($context);
        $this->invoiceService = $invoiceService;
        $this->refundService = $refundService;
        $this->invoiceRepository = $invoiceRepository;
        $this->refundRepository = $refundRepository;
        $this->refundFactory = $refundFactory;
    }
    
    /**
     * Quotation grid
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $resultRedirect = $this->resultRedirectFactory->create();
        if(!$params['purchase_order_id']){
            $this->messageManager->addErrorMessage(__('Please select a purchase order to submit refund'));
            return $resultRedirect->setPath('*/purchaseOrder/');
        }
        if(!$params['purchase_order_invoice_id']){
            $this->messageManager->addErrorMessage(__('Please select an invoice to submit refund.'));
            return $resultRedirect->setPath('*/purchaseOrder/view',['id'=>$params['purchase_order_id']]);
        }
        $invoice = $this->invoiceRepository->get($params['purchase_order_invoice_id']);
        $refundData = $this->refundService->prepareRefundData($invoice, $params);
        $refund = $this->refundFactory->create();
        $refund->addData($refundData);
        try {
            $this->refundRepository->save($refund);
            $this->refundService->collectInvoiceByRefund($invoice, $refund);
            $this->messageManager->addSuccessMessage(__('Submit refund successfully.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $resultRedirect->setPath(
            '*/purchaseOrder_invoice/view',
            ['id' => $params['purchase_order_invoice_id'], 'purchase_id' => $params['purchase_order_id']]
        );
    }
}