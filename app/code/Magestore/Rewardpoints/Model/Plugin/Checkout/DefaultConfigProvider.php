<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Rewardpoints\Model\Plugin\Checkout;;

/**
 * Class DefaultConfigProvider
 * @package Magestore\Rewardpoints\Model\Plugin\Checkout
 */
class DefaultConfigProvider
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * DefaultConfigProvider constructor.
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession
    )
    {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param \Magento\Checkout\Model\DefaultConfigProvider $configProvider
     * @param $output
     * @return mixed
     */
    public function afterGetConfig(
        \Magento\Checkout\Model\DefaultConfigProvider $configProvider,
        $output
    )
    {
        if (isset($output['totalsData'])) {
            $totalData = $output['totalsData'];
            if (isset($totalData['discount_amount']) && $totalData['discount_amount'] != 0) {
                $quote = $this->checkoutSession->getQuote();
                if ($quote->getRewardpointsBaseDiscount() != 0) {
                    $totalData['discount_amount'] = $totalData['discount_amount'] + $quote->getRewardpointsDiscount();
                    $totalData['base_discount_amount'] = $totalData['base_discount_amount'] + $quote->getRewardpointsBaseDiscount();
                    $output['totalsData'] = $totalData;
                }
            }
        }
        return $output;
    }
}