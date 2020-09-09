<?php

namespace Laconica\Storepickup\Modal\Order\Invoice\Total;

use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

class Tips extends AbstractTotal
{
    /**
     * @param Invoice $invoice
     * @return $this
     */
    public function collect(Invoice $invoice)
    {
        $tips = $invoice->getOrder()->getTips();
        if ($tips > 0) {
            $invoice->setTips($tips);
            $invoice->setBaseTips($tips);

            $invoice->setGrandTotal($invoice->getGrandTotal() + $tips);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $tips);
        }

        return $this;
    }
}
