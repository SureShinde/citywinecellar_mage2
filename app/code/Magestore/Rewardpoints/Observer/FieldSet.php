<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_OneStepCheckout
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Rewardpoints\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class ProductGetFinalPrice
 * @package Magestore\Affiliateplus\Observer
 */
class FieldSet implements ObserverInterface
{

    public $_helperPoint;
    public $_helperCustomer;
    public $_helperSpending;

    /**
     * FieldSet constructor.
     * @param \Magestore\Rewardpoints\Helper\Point $helperPoint
     * @param \Magestore\Rewardpoints\Helper\Customer $helperCustomer
     * @param \Magestore\Rewardpoints\Helper\Calculation\Spending $helperSpending
     */
    public function __construct(
        \Magestore\Rewardpoints\Helper\Point $helperPoint,
        \Magestore\Rewardpoints\Helper\Customer $helperCustomer,
        \Magestore\Rewardpoints\Helper\Calculation\Spending $helperSpending
    ){
        $this->_helperPoint = $helperPoint;
        $this->_helperCustomer = $helperCustomer;
        $this->_helperSpending = $helperSpending;
    }
     /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();

        if ($order->getCustomerIsGuest()) {
            return $this;
        }

        $order->setRewardpointsEarn($quote->getRewardpointsEarn())
            ->setRewardpointsSpent($quote->getRewardpointsSpent())
            ->setRewardpointsBaseDiscount($quote->getRewardpointsBaseDiscount())
            ->setRewardpointsDiscount($quote->getRewardpointsDiscount())
            ->setMagestoreBaseDiscount($quote->getMagestoreBaseDiscount())
            ->setMagestoreDiscount($quote->getMagestoreDiscount())
            ->setRewardpointsBaseDiscountForShipping($quote->getRewardpointsBaseDiscountForShipping())
            ->setRewardpointsDiscountForShipping($quote->getRewardpointsDiscountForShipping())
            ->setMagestoreBaseDiscountForShipping($quote->getMagestoreBaseDiscountForShipping())
            ->setMagestoreDiscountForShipping($quote->getMagestoreDiscountForShipping());
        // Validate point amount before place order
        $totalPointSpent = $this->_helperSpending->getTotalPointSpent();
        if (!$totalPointSpent) {
            return $this;
        }

        $balance = $this->_helperCustomer->getBalance();
        if ($balance < $totalPointSpent) {
            throw new \Exception(__(
                'Your points balance is not enough to spend for this order'
            ));
        }

        $minPoint = (int)$this->_helperPoint->getConfig(
            \Magestore\RewardPoints\Helper\Customer::XML_PATH_REDEEMABLE_POINTS,
            $quote->getStoreId()
        );
        if ($minPoint > $balance) {
            throw new \Exception(__(
                'Minimum points balance allows to redeem is %s',
                $this->_helperPoint->format($minPoint, $quote->getStoreId())
            ));
        }
    }
}
