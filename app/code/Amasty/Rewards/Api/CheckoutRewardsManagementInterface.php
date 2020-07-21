<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Api;

interface CheckoutRewardsManagementInterface
{
    /**
     * Adds a reward points to a specified cart.
     *
     * @param int $cartId The cart ID.
     * @param float $points
     *
     * @return mixed
     */
    public function set($cartId, $points);

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param float $usedPoints
     *
     * @return mixed
     */
    public function collectCurrentTotals(\Magento\Quote\Model\Quote $quote, $usedPoints);

    /**
     * Deletes a points from a specified cart.
     *
     * @param int $cartId The cart ID.
     * @return bool
     */
    public function remove($cartId);
}
