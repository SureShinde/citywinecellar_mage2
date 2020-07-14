<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Rewardpoints\Model\Plugin\Checkout;
/**
 * Class ShippingInformationManagement
 * @package Magestore\Rewardpoints\Plugin\Checkout
 */
class ShippingInformationManagement
{
    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * PaymentInformationManagement constructor.
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $shippingInformationManagement
     * @param $paymentDetails
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     * @return mixed
     */
    public function aroundSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $shippingInformationManagement,
        \Closure $proceed,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    )
    {
        $paymentDetails = $proceed($cartId, $addressInformation);
        $totals = $paymentDetails->getTotals();
        if ($totals->getBaseDiscountAmount()) {
            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $this->quoteRepository->getActive($cartId);
            $rewardpointsBaseDiscount = $quote->getRewardpointsBaseDiscount();
            if($rewardpointsBaseDiscount){
                $totals->setDiscountAmount($totals->getDiscountAmount() + $quote->getRewardpointsDiscount());
                $totals->setBaseDiscountAmount($totals->getBaseDiscountAmount() + $quote->getRewardpointsBaseDiscount());
            }
        }
        return $paymentDetails->setTotals($totals);
    }
}