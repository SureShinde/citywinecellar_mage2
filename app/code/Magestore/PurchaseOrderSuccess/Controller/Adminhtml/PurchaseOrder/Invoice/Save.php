<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Controller\Adminhtml\PurchaseOrder\Invoice;

/**
 * Class Save
 * @package Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Quotation
 */
class Save extends \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\AbstractAction
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magestore_PurchaseOrderSuccess::invoice_purchase_order';

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\PurchaseOrderService
     */
    protected $purchaseOrderService;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Invoice\InvoiceService
     */
    protected $invoiceService;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\DateTime
     */
    protected $dateFilter;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    public function __construct(
        \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Context $context,
        \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\PurchaseOrderService $purchaseOrderService,
        \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Invoice\InvoiceService $invoiceService,
        \Magento\Framework\Stdlib\DateTime\Filter\DateTime $dateFilter,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
    ){
        parent::__construct($context);
        $this->purchaseOrderService = $purchaseOrderService;
        $this->invoiceService = $invoiceService;
        $this->dateFilter = $dateFilter;
        $this->localeDate = $localeDate;
    }

    /**
     * Quotation grid
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        if ($this->localeDate->date()->format('Y-m-d') != $params['billed_at']) {
            $filterValues = ['billed_at' => $this->dateFilter];
            $inputFilter = new \Zend_Filter_Input(
                $filterValues,
                [],
                $params
            );
            $params = $inputFilter->getUnescaped();
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        if(!$params['purchase_order_id']){
            $this->messageManager->addErrorMessage(__('Please select a purchase order to create invoice'));
            return $resultRedirect->setPath('*/purchaseOrder/');
        }
        if(!isset($params['dynamic_grid'])){
            $this->messageManager->addErrorMessage(__('Please create invoice for at least one product.'));
            return $resultRedirect->setPath('*/purchaseOrder/view',['id'=>$params['purchase_order_id']]);
        }
        $invoiceData = $this->invoiceService->processInvoiceParam($params['dynamic_grid']);
        if(empty($invoiceData)){
            $this->messageManager->addErrorMessage(__('Please create invoice for at least one product.'));
            return $resultRedirect->setPath('*/purchaseOrder/view',['id'=>$params['purchase_order_id']]);
        }
        try {
            $user = $this->_auth->getUser();
            $this->purchaseOrderService->createInvoice(
                $params['purchase_order_id'], $invoiceData, $params['billed_at'], $user->getUserName()
            );
            $this->messageManager->addSuccessMessage(__('Create invoice successfully.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $resultRedirect->setPath('*/purchaseOrder/view', ['id' => $params['purchase_order_id']]);
    }
}