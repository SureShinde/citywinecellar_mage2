<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Observer;

class CustomerRegisterSuccess implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Amasty\Rewards\Api\HistoryRepositoryInterface
     */
    private $historyRepository;

    /**
     * @var \Amasty\Rewards\Api\RewardsProviderInterface
     */
    private $rewardsProvider;

    /**
     * @var \Amasty\Rewards\Api\RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var \Amasty\Rewards\Model\Config
     */
    private $configProvider;

    public function __construct(
        \Amasty\Rewards\Api\HistoryRepositoryInterface $historyRepository,
        \Amasty\Rewards\Api\RewardsProviderInterface $rewardsProvider,
        \Amasty\Rewards\Api\RuleRepositoryInterface $ruleRepository,
        \Amasty\Rewards\Model\Config $configProvider
    ) {
        $this->historyRepository = $historyRepository;
        $this->rewardsProvider = $rewardsProvider;
        $this->ruleRepository = $ruleRepository;
        $this->configProvider = $configProvider;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->configProvider->isEnabled()) {
            return;
        }
        /**
         * @var $customer \Magento\Customer\Model\Data\Customer
         */
        $customer = $observer->getCustomer();

        /** @var int[] $appliedActions */
        $appliedActions = $this->historyRepository->getAppliedActionsId($customer->getId());

        $rules = $this->ruleRepository->getRulesByAction(
            \Amasty\Rewards\Helper\Data::REGISTRATION_ACTION,
            $customer->getWebsiteId(),
            \Magento\Customer\Api\Data\GroupInterface::NOT_LOGGED_IN_ID
        );

        /** @var \Amasty\Rewards\Model\Rule $rule */
        foreach ($rules as $rule) {
            if (!isset($appliedActions[$rule->getId()])) {
                $this->rewardsProvider->addPointsByRule($rule, $customer->getId(), $customer->getStoreId());
            }
        }
    }
}
