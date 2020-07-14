<?php
/**
 * Created by Magestore Developer.
 * Date: 1/26/2016
 * Time: 4:09 PM
 * Set final price to product
 */

namespace Magestore\Rewardpoints\Observer\Webpos\Sales\Creditmemo;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class PrepareDataAfter implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getData('creditmemo');
        $result = $observer->getResult();
        $data = $result->getData();

        $data['creditmemo']['refund_earned_points'] = $creditmemo->getExtensionAttributes()->getRefundEarnedPoints();
        $data['creditmemo']['refund_points'] = $creditmemo->getExtensionAttributes()->getRefundPoints();

        $result->setData($data);

        return $this;
    }
}
