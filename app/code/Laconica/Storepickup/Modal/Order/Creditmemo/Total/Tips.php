<?php

namespace Laconica\Storepickup\Modal\Order\Creditmemo\Total;

use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;

class Tips extends AbstractTotal
{
    /**
     * @param Creditmemo $creditmemo
     * @return $this
     */
    public function collect(Creditmemo $creditmemo)
    {
        $tips = $creditmemo->getOrder()->getTips();
        if ($tips > 0) {
            $creditmemo->setTips($tips);
            $creditmemo->setBaseTips($tips);

            $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $tips);
            $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $tips);
        }

        return $this;
    }
}
