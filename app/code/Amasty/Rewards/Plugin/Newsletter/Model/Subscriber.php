<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Plugin\Newsletter\Model;

use Magento\Newsletter\Model\Subscriber as SourceSubscriber;

class Subscriber
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

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
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Amasty\Rewards\Api\HistoryRepositoryInterface $historyRepository,
        \Amasty\Rewards\Api\RewardsProviderInterface $rewardsProvider,
        \Amasty\Rewards\Api\RuleRepositoryInterface $ruleRepository,
        \Amasty\Rewards\Model\Config $configProvider
    ) {
        $this->registry = $registry;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->historyRepository = $historyRepository;
        $this->rewardsProvider = $rewardsProvider;
        $this->ruleRepository = $ruleRepository;
        $this->configProvider = $configProvider;
    }

    public function afterSave(
        SourceSubscriber $subject,
        $result
    ) {
        if ($subject->getCustomerId()
            && $this->configProvider->isEnabled()
            && $subject->getSubscriberStatus() === SourceSubscriber::STATUS_SUBSCRIBED
        ) {
            $this->addRewardPoints($subject->getCustomerId());
        }

        return $result;
    }

    /**
     * @param int $customerId
     */
    protected function addRewardPoints($customerId)
    {
        $websiteId = $this->storeManager->getWebsite()->getId();
        $customerGroupId = $this->customerSession->getCustomerGroupId();

        if (!$websiteId || !$customerId) {
            return;
        }

        // fix new customers group ID
        if ($customerGroupId === 0) {
            $customerGroupId = 1;
        }

        /** @var int[] $appliedActions */
        $appliedActions = $this->historyRepository->getAppliedActionsId($customerId);

        $rules = $this->ruleRepository->getRulesByAction(
            \Amasty\Rewards\Helper\Data::SUBSCRIPTION_ACTION,
            $websiteId,
            $customerGroupId
        );

        /** @var \Amasty\Rewards\Model\Rule $rule */
        foreach ($rules as $rule) {
            if (!isset($appliedActions[$rule->getId()])) {
                $this->rewardsProvider->addPointsByRule(
                    $rule,
                    $customerId,
                    $this->customerSession->getCustomer()->getStoreId()
                );
            }
        }
    }
}
