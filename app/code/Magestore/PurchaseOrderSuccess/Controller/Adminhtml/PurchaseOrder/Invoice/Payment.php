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
class Payment extends \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\AbstractAction
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magestore_PurchaseOrderSuccess::invoice_purchase_order_payment';

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Invoice\InvoiceService
     */
    protected $invoiceService;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Invoice\Payment\PaymentService
     */
    protected $paymentService;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Model\Repository\PurchaseOrder\InvoiceRepository
     */
    protected $invoiceRepository;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Model\Repository\PurchaseOrder\Invoice\PaymentRepository
     */
    protected $paymentRepository;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Invoice\PaymentFactory
     */
    protected $paymentFactory;

    public function __construct(
        \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Context $context,
        \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Invoice\InvoiceService $invoiceService,
        \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Invoice\Payment\PaymentService $paymentService,
        \Magestore\PurchaseOrderSuccess\Model\Repository\PurchaseOrder\InvoiceRepository $invoiceRepository,
        \Magestore\PurchaseOrderSuccess\Model\Repository\PurchaseOrder\Invoice\PaymentRepository $paymentRepository,
        \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Invoice\PaymentFactory $paymentFactory
    ){
        parent::__construct($context);
        $this->invoiceService = $invoiceService;
        $this->paymentService = $paymentService;
        $this->invoiceRepository = $invoiceRepository;
        $this->paymentRepository = $paymentRepository;
        $this->paymentFactory = $paymentFactory;
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
            $this->messageManager->addErrorMessage(__('Please select a purchase order to register payment'));
            return $resultRedirect->setPath('*/purchaseOrder/');
        }
        if(!$params['purchase_order_invoice_id']){
            $this->messageManager->addErrorMessage(__('Please select an invoice to register payment.'));
            return $resultRedirect->setPath('*/purchaseOrder/view',['id'=>$params['purchase_order_id']]);
        }
        $invoice = $this->invoiceRepository->get($params['purchase_order_invoice_id']);
        $paymentData = $this->paymentService->preparePaymentData($invoice, $params);
        $payment = $this->paymentFactory->create();
        $payment->addData($paymentData);
        try {
            $this->paymentRepository->save($payment);
            $this->paymentService->collectInvoiceByPayment($invoice, $payment);
            $this->messageManager->addSuccessMessage(__('Register a payment successfully.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $resultRedirect->setPath(
            '*/purchaseOrder_invoice/view',
            ['id' => $params['purchase_order_invoice_id'], 'purchase_id' => $params['purchase_order_id']]
        );
    }
}