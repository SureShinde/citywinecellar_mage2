<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Invoice\Payment;

use Magestore\PurchaseOrderSuccess\Api\Data\InvoiceInterface;
use Magestore\PurchaseOrderSuccess\Api\Data\PaymentInterface;

/**
 * Class PaymentService
 * @package Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Invoice\Payment
 */
class PaymentService 
{
    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\Config\PaymentMethod
     */
    protected $paymentConfigService;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Model\Repository\PurchaseOrder\InvoiceRepository
     */
    protected $invoiceRepository;
    
    public function __construct(
        \Magestore\PurchaseOrderSuccess\Service\Config\PaymentMethod $paymentConfigService,
        \Magestore\PurchaseOrderSuccess\Model\Repository\PurchaseOrder\InvoiceRepository $invoiceRepository
    ) {
        $this->paymentConfigService = $paymentConfigService;
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * Prepare payment data from submit
     * 
     * @param array $params
     * @return array
     */
    public function preparePaymentData(InvoiceInterface $invoice, $params = []){
        $params = $this->paymentConfigService->saveConfig($params);
        if($params['payment_amount'] > $invoice->getTotalDue())
            $params['payment_amount'] = $invoice->getTotalDue();
        return [
            PaymentInterface::PURCHASE_ORDER_INVOICE_ID => $params['purchase_order_invoice_id'],
            PaymentInterface::PAYMENT_AT => $params['payment_at'],
            PaymentInterface::PAYMENT_METHOD => $params['payment_method'],
            PaymentInterface::PAYMENT_AMOUNT => $params['payment_amount'],
            PaymentInterface::DESCRIPTION => $params['description'],

        ];
    }

    /**
     * Collect invoice total by payment
     * 
     * @param PaymentInterface $payment
     * @return $this
     */
    public function collectInvoiceByPayment(InvoiceInterface $invoice, PaymentInterface $payment){
        $invoice->setTotalDue($invoice->getTotalDue()-$payment->getPaymentAmount());
        $this->invoiceRepository->save($invoice);
        return $this;
    }
}