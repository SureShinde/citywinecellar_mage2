<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */

namespace Amasty\Rewards\Helper;

use Magento\Customer\Model\ResourceModel\Group\CollectionFactory;
use Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const ORDER_COMPLETED_ACTION = 'ordercompleted';

    const SUBSCRIPTION_ACTION = 'subscription';

    const BIRTHDAY_ACTION = 'birthday';

    const MONEY_SPENT_ACTION = 'moneyspent';

    const REGISTRATION_ACTION = 'registration';

    const REVIEW_ACTION = 'review';

    const VISIT_ACTION = 'visit';

    const ADMIN_ACTION = 'admin';

    const TRANSFER_ACTION = 'transfer'; // CUSTOM CODE

    const REWARDS_SPEND_ACTION = 'rewards_spend';

    const REWARDS_EXPIRED_ACTION = 'rewards_expired';

    const REFUND_ACTION = 'refund';

    const CANCEL_ACTION = 'cancel';

    const DISABLE_REWARD_CONFIG_PATH = 'points/disable_reward';

    const MINIMUM_POINTS_CONFIG_PATH = 'points/minimum_reward';

    const STORE_ID = 'store_id';

    const CUSTOMER_GROUP_ID = 'customer_group_id';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $session;

    /**
     * @var \Amasty\Rewards\Api\RewardsRepositoryInterface
     */
    private $rewardsRepository;

    /**
     * @var CollectionFactory
     */
    private $customerGroupCollectionFactory;

    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Checkout\Model\Session $session,
        \Amasty\Rewards\Api\RewardsRepositoryInterface $rewardsRepository,
        CollectionFactory $customerGroupCollectionFactory
    ) {
        parent::__construct($context);

        $this->scopeConfig = $context->getScopeConfig();
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->urlBuilder = $context->getUrlBuilder();
        $this->orderRepository = $orderRepository;
        $this->session = $session;
        $this->rewardsRepository = $rewardsRepository;
        $this->customerGroupCollectionFactory = $customerGroupCollectionFactory;
    }

    /**
     * @return array
     */
    public function getAllGroups()
    {
        $customerGroups = $this->customerGroupCollectionFactory->create()->load()->toOptionArray();

        $found = false;
        foreach ($customerGroups as $group) {
            if ($group['value'] == 0) {
                $found = true;
            }
        }
        if (!$found) {
            array_unshift($customerGroups, ['value' => 0, 'label' => __('NOT LOGGED IN')]);
        }

        return $customerGroups;
    }

    public function getStatuses()
    {
        return [
            '1' => __('Active'),
            '0' => __('Inactive'),
        ];
    }

    /**
     * @param int|string $orderId
     * @return null|string
     */
    public function getOrderIncrementIdById($orderId)
    {
        $order = $this->orderRepository->get($orderId);

        return $order->getIncrementId();
    }

    /**
     * @return array
     */
    public function getActions()
    {
        $actions = [
            self::ORDER_COMPLETED_ACTION => __('Order Completed'),
            self::SUBSCRIPTION_ACTION => __('Newsletter subscription'),
            self::BIRTHDAY_ACTION => __('Customer birthday'),
            self::MONEY_SPENT_ACTION => __('For every $X spent'),
            self::REGISTRATION_ACTION => __('Registration'),
            self::VISIT_ACTION => __('Inactive for a long time'),
            self::REVIEW_ACTION => __('Review written'),
            self::ADMIN_ACTION => __('Admin Point Change'),
            self::REWARDS_SPEND_ACTION => __('Order Paid'),
            self::REWARDS_EXPIRED_ACTION => __('Expiration'),
            self::REFUND_ACTION => __('Refund'),
            self::CANCEL_ACTION => __('Canceled')
        ];

        return $actions;
    }

    /**
     * @param string $path
     * @param null $store
     *
     * @return mixed
     */
    public function getStoreConfig($path, $store = null)
    {
        return $this->scopeConfig->getValue(
            'amrewards/' . $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES,
            $store
        );
    }

    /**
     * Rate cannot be null
     *
     * @param null $store
     *
     * @return float
     */
    public function getPointsRate($store = null)
    {
        return $this->getStoreConfig('points/rate', $store);
    }

    /**
     * @param null $store
     * @return bool
     */
    public function isDisableRewards($store = null)
    {
        return (bool)$this->getStoreConfig(self::DISABLE_REWARD_CONFIG_PATH, $store);
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getMinimumPointsValue($store = null)
    {
        return $this->getStoreConfig(self::MINIMUM_POINTS_CONFIG_PATH, $store);
    }

    /**
     * @param int|float $points
     *
     * @return int
     */
    public function roundPoints($points)
    {
        $roundRule = $this->getStoreConfig('points/round_rule');

        switch ($roundRule) {
            case 'up':
                return ceil($points);
            case 'down':
                return floor($points);
            default:
                return $points;
        }
    }

    /**
     * @return array
     */
    public function getRewardsData()
    {
        $customerId = $this->customerSession->getCustomerId();
        $appliedRewards = $this->session->getQuote() ? $this->session->getQuote()->getAmrewardsPoint() : 0;
        $pointsLeft = $this->rewardsRepository->getCustomerRewardBalance($customerId) - $appliedRewards;
        $pointsUsed = $this->session->getQuote()->getData('amrewards_point');

        if (!$pointsUsed) {
            $pointsUsed = 0;
        }

        return [
            'customerId' => $customerId,
            'pointsUsed' => $pointsUsed,
            'pointsLeft' => $pointsLeft,
            'pointsRate' => $this->getCurrencyPointsRate(),
            'currentCurrencyCode' => $this->storeManager->getStore()->getCurrentCurrency()->getCurrencyCode(),
            'rateForCurrency' => $this->getPointsRate(),
            'applyUrl' => $this->urlBuilder->getUrl('amrewards/index/rewardPost'),
            'cancelUrl' => $this->urlBuilder->getUrl(
                'amrewards/index/rewardPost',
                [
                    'remove' => 1,
                ]
            ),
            'minimumPointsValue' => $this->getMinimumPointsValue($this->storeManager->getStore()->getId())
        ];
    }

    /**
     * @return float
     */
    public function getCurrencyPointsRate()
    {
        $baseCurrency = $this->storeManager->getStore()->getBaseCurrency();
        $currentCurrencyCode = $this->storeManager->getStore()->getCurrentCurrencyCode();
        $rates = round($baseCurrency->getRate($currentCurrencyCode), 3);

        return $rates;
    }
}
