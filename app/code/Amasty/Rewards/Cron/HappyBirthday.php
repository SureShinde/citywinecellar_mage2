<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Cron;

use Amasty\Rewards\Helper\Data as Helper;

class HappyBirthday
{
    /**
     * @var \Amasty\Rewards\Model\Date
     */
    private $date;

    /**
     * @var \Amasty\Rewards\Model\Config
     */
    private $rewardsConfig;

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
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    private $customerCollectionFactory;

    public function __construct(
        \Amasty\Rewards\Model\Date $date,
        \Amasty\Rewards\Model\Config $rewardsConfig,
        \Amasty\Rewards\Api\HistoryRepositoryInterface $historyRepository,
        \Amasty\Rewards\Api\RewardsProviderInterface $rewardsProvider,
        \Amasty\Rewards\Api\RuleRepositoryInterface $ruleRepository,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
    ) {
        $this->date = $date;
        $this->rewardsConfig = $rewardsConfig;
        $this->historyRepository = $historyRepository;
        $this->rewardsProvider = $rewardsProvider;
        $this->ruleRepository = $ruleRepository;
        $this->customerCollectionFactory = $customerCollectionFactory;
    }

    /**
     * Clear expired persistent sessions
     *
     * @param \Magento\Cron\Model\Schedule $schedule
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Cron\Model\Schedule $schedule)
    {
        $days = $this->rewardsConfig->getBirthdayOffset();

        $date = $this->date->getDateWithOffsetByDays($days * (-1));

        $collection = $this->getCollection($date);

        /** @var \Magento\Customer\Model\Customer $customer */
        foreach ($collection->getItems() as $customer) {
            $customerId = $customer->getEntityId();

            $appliedActions = $this->historyRepository->getLastYearActionsId($customerId, $date);

            $rules = $this->ruleRepository->getRulesByAction(
                Helper::BIRTHDAY_ACTION,
                $customer->getWebsiteId(),
                $customer->getGroupId()
            );

            /** @var \Amasty\Rewards\Model\Rule $rule */
            foreach ($rules as $rule) {
                if (!isset($appliedActions[$rule->getId()])) {
                    $this->rewardsProvider->addPointsByRule($rule, $customer->getEntityId(), $customer->getStoreId());
                }
            }
        }

        return $this;
    }

    /**
     * @param string $date
     *
     * @return \Magento\Customer\Model\ResourceModel\Customer\Collection
     */
    protected function getCollection($date)
    {
        /** @var $customerCollection \Magento\Customer\Model\ResourceModel\Customer\Collection */
        $customerCollection = $this->customerCollectionFactory->create();

        $collection = $customerCollection
            ->addNameToSelect()
            ->addAttributeToSelect('email');

        $collection->getSelect()->where(
            new \Zend_Db_Expr(
                "DATE_FORMAT(`e`.`dob`, '%m-%d') = '" . $this->date->date('m-d', $date) . "'"
            )
        );

        return $collection;
    }
}
