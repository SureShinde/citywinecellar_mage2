<?php
namespace Magestore\Rewardpoints\Plugin\Checkout\Order;
/**
 * Class Item
 * @package Magestore\Rewardpoints\Plugin\Checkout\Order
 */
class Item {
    /**
     * @var $itemExtensionFactory
     */
    protected $itemExtensionFactory;

    /**
     * Item constructor.
     * @param \Magestore\Webpos\Api\Data\Checkout\Order\ItemExtensionInterfaceFactory $itemExtensionFactory
     */
    public function __construct(
        \Magestore\Webpos\Api\Data\Checkout\Order\ItemExtensionInterfaceFactory $itemExtensionFactory
    ) {
        $this->itemExtensionFactory = $itemExtensionFactory;
    }

    /**
     * @param \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface $item
     * @param \Magestore\Webpos\Api\Data\Checkout\Order\ItemExtensionInterface|null $itemExtension
     * @return mixed
     */
    public function afterGetExtensionAttributes(
        \Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface $item,
        \Magestore\Webpos\Api\Data\Checkout\Order\ItemExtensionInterface $itemExtension = null
    ) {
        if ($itemExtension === null) {
            $itemExtension = $this->itemExtensionFactory->create();
        }

        $rewardpointsSpent = $itemExtension->getRewardpointsSpent();
        if ($rewardpointsSpent === null) {
            $rewardpointsSpent = $item->getRewardpointsSpent();
            $itemExtension->setRewardpointsSpent($rewardpointsSpent);
        }

        $rewardpointsEarn = $itemExtension->getRewardpointsEarn();
        if ($rewardpointsEarn === null) {
            $rewardpointsEarn = $item->getRewardpointsEarn();
            $itemExtension->setRewardpointsEarn($rewardpointsEarn);
        }

        $rewardpointsDiscount = $itemExtension->getRewardpointsDiscount();
        if ($rewardpointsDiscount === null) {
            $rewardpointsDiscount = $item->getRewardpointsDiscount();
            $itemExtension->setRewardpointsDiscount($rewardpointsDiscount);
        }

        $rewardpointsBaseDiscount = $itemExtension->getRewardpointsBaseDiscount();
        if ($rewardpointsBaseDiscount === null) {
            $rewardpointsBaseDiscount = $item->getRewardpointsBaseDiscount();
            $itemExtension->setRewardpointsBaseDiscount($rewardpointsBaseDiscount);
        }

        $item->setExtensionAttributes($itemExtension);
        return $itemExtension;
    }
}