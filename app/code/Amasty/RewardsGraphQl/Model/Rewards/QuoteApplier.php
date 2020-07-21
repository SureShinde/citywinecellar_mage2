<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_RewardsGraphQl
 */


namespace Amasty\RewardsGraphQl\Model\Rewards;

use Amasty\Rewards\Api\CheckoutRewardsManagementInterface;
use GraphQL\Error\Error;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartRepositoryInterface;

class QuoteApplier
{
    /**
     * @var CheckoutRewardsManagementInterface
     */
    private $management;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    public function __construct(
        CheckoutRewardsManagementInterface $management,
        CartRepositoryInterface $cartRepository
    ) {
        $this->management = $management;
        $this->cartRepository = $cartRepository;
    }

    /**
     * @param int $customerId
     * @param float $amount
     *
     * @return string
     *
     * @throws Error
     */
    public function apply(int $customerId, float $amount)
    {
        try {
            $cartId = $this->cartRepository->getActiveForCustomer($customerId)->getId();

            if ($amount) {
                $pointsData = $this->management->set($cartId, $amount);
                $result = $pointsData['notice'];
            } else {
                $this->management->remove($cartId);
                $result = __('Removed.');
            }
        } catch (LocalizedException $exception) {
            $result = $exception->getMessage();
        } catch (\Exception $exception) {
            throw new Error(__('Can not perform operation.'));
        }

        return $result;
    }
}
