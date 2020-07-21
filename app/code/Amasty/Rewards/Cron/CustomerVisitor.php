<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Cron;

use Amasty\Rewards\Helper\Data as Helper;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;

class CustomerVisitor
{
    /**
     * @var \Amasty\Rewards\Model\ResourceModel\CustomerVisitor
     */
    private $customerVisitor;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Amasty\Rewards\Model\Date
     */
    private $date;

    /**
     * @var \Amasty\Rewards\Api\RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var \Amasty\Rewards\Api\RewardsProviderInterface\Proxy
     */
    private $rewardsProvider;

    /**
     * @var \Amasty\Rewards\Api\HistoryRepositoryInterface
     */
    private $historyRepository;

    public function __construct(
        \Amasty\Rewards\Model\Date $date,
        \Amasty\Rewards\Api\RuleRepositoryInterface $ruleRepository,
        \Amasty\Rewards\Api\RewardsProviderInterface $rewardsProvider,
        \Amasty\Rewards\Api\HistoryRepositoryInterface $historyRepository,
        \Amasty\Rewards\Model\ResourceModel\CustomerVisitor $customerVisitor,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {

        $this->date = $date;
        $this->ruleRepository = $ruleRepository;
        $this->historyRepository = $historyRepository;
        $this->rewardsProvider = $rewardsProvider;
        $this->customerVisitor = $customerVisitor;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param \Magento\Cron\Model\Schedule $schedule
     */
    public function execute(\Magento\Cron\Model\Schedule $schedule)
    {
        $rules = $this->ruleRepository->getRulesByAction(Helper::VISIT_ACTION, null, null);

        /** @var \Amasty\Rewards\Api\Data\RuleInterface $rule */
        foreach ($rules as $rule) {
            $toDate = $this->date->getDateWithOffsetByDays($rule->getInactiveDays() * (-1));
            $lostCustomers = $this->customerVisitor->getInactiveCustomers($toDate);

            /** @var array $customer */
            foreach ($lostCustomers as $customer) {
                $customerModel = $this->customerRepository->getById($customer['customer_id']);

                if (!$rule->validateByCustomer($customerModel)) {
                    continue;
                }

                /** @var int[] $appliedActions */
                $appliedActions = $this->historyRepository->getAppliedActionsId($customerModel->getId());

                if (!isset($appliedActions[$rule->getRuleId()])) {
                    $this->rewardsProvider->addPointsByRule(
                        $rule,
                        $customerModel->getId(),
                        $customerModel->getStoreId()
                    );
                } elseif ($rule->getRecurring() == Boolean::VALUE_YES) {
                    $lastReward = $this->historyRepository->getLastActionByCustomerId(
                        $customerModel->getId(),
                        $rule->getRuleId()
                    );

                    if ($toDate > $lastReward->getDate()) {
                        $this->rewardsProvider->addPointsByRule(
                            $rule,
                            $customerModel->getId(),
                            $customerModel->getStoreId()
                        );
                    }
                }
            }
        }
    }
}
