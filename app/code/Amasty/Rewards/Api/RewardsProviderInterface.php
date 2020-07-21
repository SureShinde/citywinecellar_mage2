<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Api;

use Amasty\Rewards\Api\Data\ExpirationArgumentsInterface;
use Amasty\Rewards\Api\Data\RuleInterface;
use InvalidArgumentException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;

/**
 * Interface RewardsProviderInterface
 *
 * @api
 */
interface RewardsProviderInterface
{
    /**
     * Function to add rewards points to customer account by his ID according to Rewards Rule.
     * Argument $amount should be greater than zero or null.
     * If $amount/$comment is null, the value will be taken from the $rule.
     *
     * @param RuleInterface $rule
     * @param int $customerId
     * @param int $storeId
     * @param int|null $amount
     * @param string|null $comment
     *
     * @return void
     *
     * @throws LocalizedException
     * @throws InvalidArgumentException
     */
    public function addPointsByRule($rule, $customerId, $storeId, $amount = null, $comment = null);

    /**
     * Function to deduct rewards points from customer account by his ID.
     * Argument $amount must be greater than zero.
     *
     * @param float $amount
     * @param int $customerId
     * @param string $action
     * @param string|null $comment
     *
     * @return void
     *
     * @throws LocalizedException
     * @throws InvalidArgumentException
     */
    public function deductPoints($amount, $customerId, $action, $comment = null);

    /**
     * Function to add rewards points to customer account by his ID.
     * Argument $amount must be greater than zero.
     *
     * @param float $amount
     * @param int $customerId
     * @param string $action
     * @param string $comment
     * @param ExpirationArgumentsInterface $expire
     *
     * @return void
     *
     * @throws LocalizedException
     * @throws InvalidArgumentException
     */
    public function addPoints($amount, $customerId, $action, $comment, $expire);
}
