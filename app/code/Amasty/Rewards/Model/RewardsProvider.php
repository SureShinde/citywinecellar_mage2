<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Model;

use Amasty\Rewards\Api;
use Amasty\Rewards\Helper\Data;
use Amasty\Rewards\Model\Quote\RewardsQuoteTune;
use Magento\Framework\Exception\LocalizedException;

/**
 * Provide data for Rewards
 */
class RewardsProvider implements Api\RewardsProviderInterface
{
    /**#@+
     * Keys to $usedRewards array
     */
    const REWARD_ID = 'reward_id';

    const REST_AMOUNT = 'rest_amount';
    /**#@-*/

    /**
     * @var Date
     */
    private $date;

    /**
     * @var ConfigFactory
     */
    private $rewardsConfigFactory;

    /**
     * @var TransportFactory
     */
    private $transportFactory;

    /**
     * @var Api\Data\ExpirationArgumentsInterfaceFactory
     */
    private $expirationArgFactory;

    /**
     * @var Api\HistoryRepositoryInterface
     */
    private $historyRepository;

    /**
     * @var Api\RewardsRepositoryInterface
     */
    private $rewardsRepository;

    /**
     * @var Api\ExpirationDateRepositoryInterface
     */
    private $expirationDateRepository;

    /**
     * @var RewardsQuoteTune
     */
    private $rewardsQuoteTune;

    public function __construct(
        Date $date,
        ConfigFactory $rewardsConfigFactory,
        TransportFactory $transportFactory,
        Api\Data\ExpirationArgumentsInterfaceFactory $expirationArgFactory,
        Api\HistoryRepositoryInterface $historyRepository,
        Api\RewardsRepositoryInterface $rewardsRepository,
        Api\ExpirationDateRepositoryInterface $expirationDateRepository,
        RewardsQuoteTune $rewardsQuoteTune
    ) {
        $this->date = $date;
        $this->rewardsConfigFactory = $rewardsConfigFactory;
        $this->transportFactory = $transportFactory;
        $this->expirationArgFactory = $expirationArgFactory;
        $this->historyRepository = $historyRepository;
        $this->rewardsRepository = $rewardsRepository;
        $this->expirationDateRepository = $expirationDateRepository;
        $this->rewardsQuoteTune = $rewardsQuoteTune;
    }

    /**
     * @inheritdoc
     */
    public function addPointsByRule($rule, $customerId, $storeId, $amount = null, $comment = null)
    {
        if ($amount === null) {
            $amount = $rule->getAmount();
        }

        if ($comment === null) {
            $comment = $rule->getStoreLabel($storeId);
        }
        /** @var Api\Data\ExpirationArgumentsInterface $expire */
        $expire = $this->expirationArgFactory->create();
        $expire
            ->setIsExpire($this->isRewardsExpire($rule->getExpirationBehavior(), $storeId))
            ->setDays($this->getExpirationDays($rule, $storeId));

        $this->addPoints($amount, $customerId, $rule->getAction(), $comment, $expire);

        /** @var Api\Data\HistoryInterface $historyRewards */
        $historyRewards = $this->historyRepository->getEmptyModel();
        $historyRewards->setCustomerId($customerId);
        $historyRewards->setActionId($rule->getRuleId());
        $historyRewards->setParams($rule->getParams());
        $this->historyRepository->save($historyRewards);
    }

    /**
     * @inheritdoc
     */
    public function deductPoints($amount, $customerId, $action, $comment = null)
    {
        if ($amount <= 0) {
            throw new LocalizedException(__('You are trying to deduct negative or null amount of rewards point(s).'));
        }

        if (!$amount || !$customerId || !$action) {
            throw new \InvalidArgumentException('Required parameter is not set.');
        }

        $currentBalance = $this->rewardsRepository->getCustomerRewardBalance($customerId);

        if ($comment === null) {
            $comment = $this->getCommentOnDeduct($action);
        }

        if ($amount > $currentBalance) {
            throw new LocalizedException(__('Too much point(s) used.'));
        }

        $usedRewards = $this->getUsedRewards($amount, $customerId);

        foreach ($usedRewards as $rewards) {
            if (!$rewards[self::REST_AMOUNT]) {
                $this->expirationDateRepository->deleteById($rewards[self::REWARD_ID]);
            } else {
                /** @var Api\Data\ExpirationDateInterface $expirationRewards */
                $expirationRewards = $this->expirationDateRepository->getById($rewards[self::REWARD_ID]);
                $expirationRewards->setAmount($rewards[self::REST_AMOUNT]);
                $this->expirationDateRepository->save($expirationRewards);
            }
        }

        $this->commonAction(-$amount, $customerId, $comment, $action);
    }

    /**
     * @param float $amount
     * @param int $customerId
     * @param string $action
     * @param string $comment
     * @param Api\Data\ExpirationArgumentsInterface $expire
     *
     * @throws LocalizedException
     * @throws \InvalidArgumentException
     */
    public function addPoints($amount, $customerId, $action, $comment, $expire)
    {
        if ($amount <= 0) {
            throw new LocalizedException(__('You are trying to add negative or null amount of rewards point(s).'));
        }

        if (!$amount || !$customerId || !$action) {
            throw new \InvalidArgumentException('Required parameter is not set.');
        }

        $days = false;

        if ($expire->isExpire() == true) {
            $days = $expire->getDays();
        }

        /** @var Api\Data\ExpirationDateInterface $expirationRewards */
        $expirationRewards = $this->expirationDateRepository->getCustomerNonExpireRewards($customerId);
        $expirationRewards->setCustomerId($customerId);

        if ($days !== false) {
            $expirationRewards->setAmount($amount);
            $expirationRewards->setDate($this->date->getDateWithOffsetByDays($days));
            $expirationRewards->setEntityId(null);
        } else {
            $expirationRewards->setAmount($amount + $expirationRewards->getAmount());
        }

        $this->expirationDateRepository->save($expirationRewards);

        $this->commonAction($amount, $customerId, $comment, $action, $expirationRewards->getEntityId());

        /** @var \Amasty\Rewards\Model\Transport $transport */
        $transport = $this->transportFactory->create();
        $transport->sendRewardsEarningNotification($amount, $customerId, $action);
    }

    /**
     * Common function for add or deduct rewards action
     * Which save rewards action into amasty_rewards_rewards table and set rewards amount in quote
     *
     * @param float $amount
     * @param int $customerId
     * @param string $comment
     * @param string $actionCode
     * @param int $expirationId
     *
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    private function commonAction($amount, $customerId, $comment, $actionCode, $expirationId = 0)
    {
        $currentBalance = $this->rewardsRepository->getCustomerRewardBalance($customerId);

        if ($actionCode == Data::ADMIN_ACTION) {
            $config = $this->rewardsConfigFactory->create();
            $actionCode = $config->getAdminActionName() ?: __('Admin Point Change');
        }

        /** @var Api\Data\RewardsInterface $modelRewards */
        $modelRewards =  $this->rewardsRepository->getEmptyModel();
        $modelRewards->setCustomerId($customerId);
        $modelRewards->setAmount($amount);
        $modelRewards->setAction($actionCode);
        $modelRewards->setComment($comment);
        $modelRewards->setPointsLeft($currentBalance + $amount);
        $modelRewards->setExpirationId($expirationId);
        $this->rewardsRepository->save($modelRewards);
        $this->rewardsQuoteTune->execute($modelRewards, $customerId);
    }

    /**
     * Get days, after which rewards should expire
     *
     * @param Api\Data\RuleInterface $rule
     *
     * @param string $storeId
     *
     * @return int|null
     */
    private function getExpirationDays($rule, $storeId)
    {
        if ($rule->getExpirationBehavior() == Api\Data\ExpirationDateInterface::EXPIRATION_CUSTOM) {
            $days = (int) $rule->getExpirationPeriod();
        } else {
            /** @var Config $rewardsConfig */
            $rewardsConfig = $this->rewardsConfigFactory->create();
            $days = $rewardsConfig->getExpirationPeriod($storeId);
        }

        return $days !== false ? $days : null;
    }

    /**
     * Return true if rewards should expire
     *
     * @param int $expirationBehavior
     *
     * @param string $store
     *
     * @return bool
     */
    private function isRewardsExpire($expirationBehavior, $store)
    {
        switch ($expirationBehavior) {
            case Api\Data\ExpirationDateInterface::EXPIRATION_NEVER:
                $isExpire = false;
                break;
            case Api\Data\ExpirationDateInterface::EXPIRATION_CUSTOM:
                $isExpire = true;
                break;
            default:
                /** @var Config $rewardsConfig */
                $rewardsConfig = $this->rewardsConfigFactory->create();
                $isExpire = (bool) $rewardsConfig->getExpirationBehavior($store);
                break;
        }

        return $isExpire;
    }

    /**
     * @param float $amount
     * @param int $customerId
     *
     * @return array
     * @throws LocalizedException
     */
    private function getUsedRewards($amount, $customerId)
    {
        /** @var array $customerRewards */
        $customerRewards = $this->expirationDateRepository->getByCustomerId($customerId, false);

        $usedRewards = [];

        foreach ($customerRewards as $rewards) {
            if ($rewards[Api\Data\ExpirationDateInterface::AMOUNT] >= $amount) {
                $usedRewards[] = [
                    self::REWARD_ID => $rewards[Api\Data\ExpirationDateInterface::ENTITY_ID],
                    self::REST_AMOUNT => $rewards[Api\Data\ExpirationDateInterface::AMOUNT] - $amount
                ];
                $amount = 0;

                break;
            } else {
                $usedRewards[] = [
                    self::REWARD_ID => $rewards[Api\Data\ExpirationDateInterface::ENTITY_ID],
                    self::REST_AMOUNT => 0
                ];

                $amount = round($amount - $rewards[Api\Data\ExpirationDateInterface::AMOUNT], 2);
            }
        }

        if ($amount) {
            throw new LocalizedException(__('Reward points balance mismatch: please contact store administrator.'));
        }

        return $usedRewards;
    }

    /**
     * @param string $actionCode
     *
     * @return \Magento\Framework\Phrase
     */
    private function getCommentOnDeduct($actionCode)
    {
        switch ($actionCode) {
            case Data::ADMIN_ACTION:
                return  __('Admin Point Change');
            case Data::REWARDS_SPEND_ACTION:
                return __('Order paid.');
            case Data::REWARDS_EXPIRED_ACTION:
                return __('Rewards expired by date.');
        }

        return __('The action is not determined.');
    }
}
