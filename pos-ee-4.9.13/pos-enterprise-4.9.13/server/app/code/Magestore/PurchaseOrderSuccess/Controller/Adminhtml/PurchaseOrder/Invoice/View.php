<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Controller\Adminhtml\PurchaseOrder\Invoice;

/**
 * Class View
 * @package Magestore\PurchaseOrderSuccess\Controller\Adminhtml\PurchaseOrder\Invoice
 */
class View extends \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\AbstractAction
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magestore_PurchaseOrderSuccess::invoice_purchase_order_view';

    /**
     * @var
     */
    protected $invoiceRepository;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface
     */
    protected $purchaseOrderRepository;

    /**
     * View constructor.
     * @param \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Context $context
     * @param \Magestore\PurchaseOrderSuccess\Api\InvoiceRepositoryInterface $invoiceRepository
     * @param \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface $purchaseOrderRepository
     */
    public function __construct(
        \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Context $context,
        \Magestore\PurchaseOrderSuccess\Api\InvoiceRepositoryInterface $invoiceRepository,
        \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface $purchaseOrderRepository
    ){
        parent::__construct($context);
        $this->invoiceRepository = $invoiceRepository;
        $this->purchaseOrderRepository = $purchaseOrderRepository;
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
        if(!$params['purchase_id']){
            $this->messageManager->addErrorMessage(__('Please select a purchase order to view invoice'));
            return $resultRedirect->setPath('*/purchaseOrder/');
        }
        if(!isset($params['id'])){
            $this->messageManager->addErrorMessage(__('Please select an invoice to view.'));
            return $resultRedirect->setPath('*/purchaseOrder/view',['id'=>$params['purchase_id']]);
        }
        try{
            $invoice = $this->invoiceRepository->get($params['id']);
            $this->_registry->register('current_purchase_order_invoice', $invoice);
        }catch (\Exception $e){
            $this->messageManager->addErrorMessage(__('Please select an invoice to view.'));
            return $resultRedirect->setPath('*/purchaseOrder/view',['id'=>$params['purchase_id']]);
        }
        $resultPage = $this->_initAction();
        $purchaseCode = $this->purchaseOrderRepository->get($invoice->getPurchaseOrderId())->getPurchaseCode();
        $resultPage->getConfig()->getTitle()->prepend(__('View Invoice #%1 (Purchase Order #%2)', $invoice->getInvoiceCode(), $purchaseCode));
        return $resultPage;
    }
}