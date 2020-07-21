<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_RewardsGraphQl
 */


namespace Amasty\RewardsGraphQl\Model\Resolver;

use Magento\CustomerGraphQl\Model\Customer\CheckCustomerAccount;
use Magento\CustomerGraphQl\Model\Customer\GetCustomer;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\ObjectManagerInterface;

class ContextResolver
{
    /**
     * @var CheckCustomerAccount|GetCustomer
     */
    private $checkCustomerAccount;

    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        if (class_exists(GetCustomer::class)) {
            $this->checkCustomerAccount = $objectManager->create(GetCustomer::class);
        } else {
            $this->checkCustomerAccount = $objectManager->create(CheckCustomerAccount::class);
        }
    }

    /**
     * @param ContextInterface $context
     * @return int
     */
    public function getCustomerId($context): int
    {
        $currentUserId = $context->getUserId();
        $currentUserType = $context->getUserType();

        if ($this->checkCustomerAccount instanceof GetCustomer) {
            $this->checkCustomerAccount->execute($context);
        } else {
            $this->checkCustomerAccount->execute($currentUserId, $currentUserType);
        }

        return $currentUserId;
    }
}
