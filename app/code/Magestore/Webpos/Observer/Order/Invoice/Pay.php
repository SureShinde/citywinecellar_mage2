<?php

namespace Magestore\Webpos\Observer\Order\Invoice;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class Pay implements ObserverInterface {

    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Sales\Order\Payment\CollectionFactory
     */
    protected $orderPaymentCollectionFactory;

    /**
     * Pay constructor.
     * @param \Magestore\Webpos\Model\ResourceModel\Sales\Order\Payment\CollectionFactory $orderPaymentCollectionFactory
     */
    public function __construct(
        \Magestore\Webpos\Model\ResourceModel\Sales\Order\Payment\CollectionFactory $orderPaymentCollectionFactory
    ){
        $this->orderPaymentCollectionFactory = $orderPaymentCollectionFactory;
    }

    public function execute(EventObserver $observer)
    {
        $invoice = $observer->getInvoice();
        $order = $invoice->getOrder();
        $grandTotal = $order->getGrandTotal();
        $baseGrandTotal = $order->getBaseGrandTotal();
        $posPayments = $this->orderPaymentCollectionFactory->create()->addFieldToFilter('order_id', $order->getId());
        $posTotalPaid = 0;
        $posBaseTotalPaid = 0;
        if ($posPayments->getSize()) {
            foreach ($posPayments as $posPayment) {
                $posTotalPaid += $posPayment->getAmountPaid();
                $posBaseTotalPaid += $posPayment->getBaseAmountPaid();
            }
        }

        $totalInvoiceAmount = $invoice->getGrandTotal();
        $baseTotalInvoiceAmount = $invoice->getBaseGrandTotal();

        $posPreTotalPaid = $order->getPosPreTotalPaid();
        $posBasePreTotalPaid = $order->getPosBasePreTotalPaid();

        if ($posPreTotalPaid > 0 && $posBasePreTotalPaid > 0) {
            $totalInvoiceAmount += $posPreTotalPaid;
            $baseTotalInvoiceAmount += $posBasePreTotalPaid;
            $posTotalPaid = $order->getTotalPaid() - $invoice->getGrandTotal();
            $posBaseTotalPaid = $order->getBaseTotalPaid() - $invoice->getBaseGrandTotal();
        }

        $order->setPosPreTotalPaid($totalInvoiceAmount);
        $order->setPosBasePreTotalPaid($baseTotalInvoiceAmount);

        $totalPaid = max($posTotalPaid, $totalInvoiceAmount);
        $baseTotalPaid = max($posBaseTotalPaid, $baseTotalInvoiceAmount);

        $totalPaid = min($totalPaid, $grandTotal);
        $baseTotalPaid = min($baseTotalPaid, $baseGrandTotal);

        $order->setTotalPaid($totalPaid);
        $order->setBaseTotalPaid($baseTotalPaid);

        return $this;
    }
}