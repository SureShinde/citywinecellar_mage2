<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Invoice\Refund;

use Magestore\PurchaseOrderSuccess\Api\Data\InvoiceInterface;
use Magestore\PurchaseOrderSuccess\Api\Data\RefundInterface;

/**
 * Class RefundService
 * @package Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Invoice\Refund
 */
class RefundService 
{

    /**
     * @var \Magestore\PurchaseOrderSuccess\Model\Repository\PurchaseOrder\InvoiceRepository
     */
    protected $invoiceRepository;
    
    public function __construct(
        \Magestore\PurchaseOrderSuccess\Model\Repository\PurchaseOrder\InvoiceRepository $invoiceRepository
    ) {
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * Prepare refund data from submit
     * 
     * @param array $params
     * @return array
     */
    public function prepareRefundData(InvoiceInterface $invoice, $params = []){
        $maxRefund = $invoice->getGrandTotalInclTax()-$invoice->getTotalDue()-$invoice->getTotalRefund();
        if($params['refund_amount'] > $maxRefund)
            $params['refund_amount'] = $maxRefund;
        return [
            RefundInterface::PURCHASE_ORDER_INVOICE_ID => $params['purchase_order_invoice_id'],
            RefundInterface::REFUND_AMOUNT => $params['refund_amount'],
            RefundInterface::REASON => $params['reason'],
            RefundInterface::REFUND_AT => $params['refund_at'],
        ];
    }

    /**
     * Collect invoice total by payment
     * 
     * @param PaymentInterface $payment
     * @return $this
     */
    public function collectInvoiceByRefund(InvoiceInterface $invoice, RefundInterface $refund){
        $invoice->setTotalRefund($invoice->getTotalRefund()+$refund->getRefundAmount());
        $this->invoiceRepository->save($invoice);
        return $this;
    }
}