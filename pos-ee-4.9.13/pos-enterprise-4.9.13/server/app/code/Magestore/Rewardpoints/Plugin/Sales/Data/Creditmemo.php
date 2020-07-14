<?php
namespace Magestore\Rewardpoints\Plugin\Sales\Data;
/**
 * Class Order
 * @package Magestore\Rewardpoints\Plugin\Sales\Data\Creditmemo
 */
class Creditmemo {
    /**
     * @var \Magestore\Webpos\Api\Data\Sales\Order\CreditmemoExtensionInterfaceFactory
     */
    protected $creditmemoExtensionFactory;


    /**
     * Creditmemo constructor.
     * @param \Magestore\Webpos\Api\Data\Sales\Order\CreditmemoExtensionInterfaceFactory $creditmemoExtensionFactory
     */
    public function __construct(
        \Magestore\Webpos\Api\Data\Sales\Order\CreditmemoExtensionInterfaceFactory $creditmemoExtensionFactory
    ) {
        $this->creditmemoExtensionFactory = $creditmemoExtensionFactory;
    }

    /**
     * @param \Magestore\Webpos\Api\Data\Sales\Order\CreditmemoInterface $creditmemo
     * @param \Magestore\Webpos\Api\Data\Sales\Order\CreditmemoExtensionInterface|null $creditmemoExtension
     * @return \Magestore\Webpos\Api\Data\Sales\Order\CreditmemoExtensionInterface
     */
    public function afterGetExtensionAttributes(
        \Magestore\Webpos\Api\Data\Sales\Order\CreditmemoInterface $creditmemo,
        \Magestore\Webpos\Api\Data\Sales\Order\CreditmemoExtensionInterface $creditmemoExtension = null
    ) {
        if ($creditmemoExtension === null) {
            $creditmemoExtension = $this->creditmemoExtensionFactory->create();
        }

        $rewardpointsDiscount = $creditmemoExtension->getRewardpointsDiscount();
        if ($rewardpointsDiscount === null) {
            $rewardpointsDiscount = $creditmemo->getRewardpointsDiscount();
            $creditmemoExtension->setRewardpointsDiscount($rewardpointsDiscount);
        }

        $rewardpointsBaseDiscount = $creditmemoExtension->getRewardpointsBaseDiscount();
        if ($rewardpointsBaseDiscount === null) {
            $rewardpointsBaseDiscount = $creditmemo->getRewardpointsBaseDiscount();
            $creditmemoExtension->setRewardpointsBaseDiscount($rewardpointsBaseDiscount);
        }

        $rewardpointsEarn = $creditmemoExtension->getRewardpointsEarn();
        if ($rewardpointsEarn === null) {
            $rewardpointsEarn = $creditmemo->getRewardpointsEarn();
            $creditmemoExtension->setRewardpointsEarn($rewardpointsEarn);
        }

        $refundEarnPoint = $creditmemoExtension->getRefundEarnedPoints();
        if ($refundEarnPoint === null) {
            $refundEarnPoint = $creditmemo->getRefundEarnedPoints();
            $creditmemoExtension->setRefundEarnedPoints($refundEarnPoint);
        }

        $refundPoint = $creditmemoExtension->getRefundPoints();
        if ($refundPoint === null) {
            $refundPoint = $creditmemo->getRefundPoints();
            $creditmemoExtension->setRefundPoints($refundPoint);
        }

        $creditmemo->setExtensionAttributes($creditmemoExtension);
        return $creditmemoExtension;
    }
}
