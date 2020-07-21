<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Model;

class Config
{
    const REWARDS_SECTION = 'amrewards/';

    const CALCULATION_GROUP = 'calculation/';

    const NOTIFICATION_GROUP = 'notification/';

    const POINTS_GROUP = 'points/';

    const HIGHLIGHT_GROUP = 'highlight/';

    const GENERAL_GROUP = 'general/';

    const CUSTOMER_GROUP = 'customer/';

    const ENABLED = 'enabled';

    const TAX_FIELD = 'before_after_tax';

    const ENABLE_LIMIT_FIELD = 'enable_limit';

    const BIRTHDAY_OFFSET = 'days';

    const POINTS_RATE = 'rate';

    const POINTS_ROUND_RULE = 'round_rule';

    const DISABLE_REWARD = 'disable_reward';

    const MINIMUM_REWARDS = 'minimum_reward';

    const ADMIN_ACTION_NAME = 'adminaction';

    const LIMIT_AMOUNT_REWARD_FIELD = 'limit_amount_reward';

    const LIMIT_PERCENT_REWARD_FIELD = 'limit_percent_reward';

    const EARN_TEMPLATE = 'balance_earn_template';

    const EMAIL_SENDER = 'email_sender';

    const EARN_NOTICE = 'send_earn_notification';

    const EXPIRE_NOTICE = 'send_expire_notification';

    const EXPIRE_SEND_DAYS = 'expiry_day_send';

    const EXPIRE_TEMPLATE = 'points_expiring_template';

    const EXPIRATION_BEHAVIOR = 'expiration_behavior';

    const EXPIRATION_PERIOD = 'expiration_period';

    const CART = 'cart';

    const CHECKOUT = 'checkout';

    const COLOR = 'color';

    const PRODUCT = 'product';

    const CATEGORY = 'category';

    const GUEST = 'guest';

    const DESCRIPTION_ENABLE = 'description_enable';

    const DESCRIPTION_MESSAGE = 'description_message';

    const SHOW_BALANCE = 'show_balance';

    const BALANCE_LABEL = 'balance_label';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $config;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $group
     * @param string $path
     * @param string|null $store
     *
     * @return string
     */
    private function getScopeValue($group, $path, $store = null)
    {
        return $this->config->getValue(
            self::REWARDS_SECTION . $group . $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $group
     * @param string $path
     * @param int|null $store
     *
     * @return bool
     */
    private function isSetFlag($group, $path, $store = null)
    {
        return $this->config->isSetFlag(
            self::REWARDS_SECTION . $group . $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Is Module Enabled
     *
     * @param int|null $store
     *
     * @return bool
     */
    public function isEnabled($store = null)
    {
        return $this->isSetFlag(self::GENERAL_GROUP, self::ENABLED, $store);
    }

    /**
     * Return true, when rewards cannot be earned by order using reward points.
     *
     * @param string $store
     *
     * @return bool
     */
    public function isDisabledEarningByRewards($store)
    {
        return (bool) $this->getScopeValue(self::POINTS_GROUP, self::DISABLE_REWARD, $store);
    }

    /**
     * @return mixed
     */
    public function getEarningCalculationMode()
    {
        return $this->getScopeValue(self::CALCULATION_GROUP, self::TAX_FIELD);
    }

    /**
     * @param string $store
     *
     * @return mixed
     */
    public function isEnableLimit($store)
    {
        return $this->getScopeValue(self::POINTS_GROUP, self::ENABLE_LIMIT_FIELD, $store);
    }

    /**
     * @param string $store
     *
     * @return mixed
     */
    public function getRewardAmountLimit($store)
    {
        return $this->getScopeValue(self::POINTS_GROUP, self::LIMIT_AMOUNT_REWARD_FIELD, $store);
    }

    /**
     * @param string $store
     *
     * @return mixed
     */
    public function getRewardPercentLimit($store)
    {
        return $this->getScopeValue(self::POINTS_GROUP, self::LIMIT_PERCENT_REWARD_FIELD, $store);
    }

    /**
     * @param $store
     *
     * @return string
     */
    public function getEarnTemplate($store)
    {
        return $this->getScopeValue(self::NOTIFICATION_GROUP, self::EARN_TEMPLATE, $store);
    }

    /**
     * @param $store
     *
     * @return string
     */
    public function getEmailSender($store)
    {
        return $this->getScopeValue(self::NOTIFICATION_GROUP, self::EMAIL_SENDER, $store);
    }

    /**
     * @param $store
     *
     * @return int
     */
    public function getSendEarnNotification($store)
    {
        return (int)$this->getScopeValue(self::NOTIFICATION_GROUP, self::EARN_NOTICE, $store);
    }

    /**
     * @param $store
     *
     * @return int
     */
    public function getSendExpireNotification($store)
    {
        return (int)$this->getScopeValue(self::NOTIFICATION_GROUP, self::EXPIRE_NOTICE, $store);
    }

    /**
     * @param $store
     *
     * @return int
     */
    public function getExpireTemplate($store)
    {
        return $this->getScopeValue(self::NOTIFICATION_GROUP, self::EXPIRE_TEMPLATE, $store);
    }

    /**
     * @param $store
     *
     * @return int
     */
    public function getExpireDaysToSend($store)
    {
        return (int)$this->getScopeValue(self::NOTIFICATION_GROUP, self::EXPIRE_SEND_DAYS, $store);
    }

    /**
     * @param string $store
     *
     * @return string
     */
    public function getExpirationBehavior($store)
    {
        return $this->getScopeValue(self::POINTS_GROUP, self::EXPIRATION_BEHAVIOR, $store);
    }

    /**
     * @param string $store
     *
     * @return int|bool
     */
    public function getExpirationPeriod($store)
    {
        $days = false;

        if ($this->getExpirationBehavior($store)) {
            $days = (int) $this->getScopeValue(self::POINTS_GROUP, self::EXPIRATION_PERIOD, $store);
        }

        return $days;
    }

    /**
     * @param string $store
     *
     * @return float
     */
    public function getPointsRate($store)
    {
        return $this->getScopeValue(self::POINTS_GROUP, self::POINTS_RATE, $store);
    }

    /**
     * @param string $store
     *
     * @return string
     */
    public function getRoundRule($store)
    {
        return $this->getScopeValue(self::POINTS_GROUP, self::POINTS_ROUND_RULE, $store);
    }

    /**
     * @param string $store
     *
     * @return string
     */
    public function getStoreLocale($store)
    {
        return $this->config->getValue(
            'general/locale/code',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES,
            $store
        );
    }

    /**
     * @return int
     */
    public function getBirthdayOffset()
    {
        return (int) $this->getScopeValue(self::GENERAL_GROUP, self::BIRTHDAY_OFFSET);
    }

    /**
     * @return string
     */
    public function getAdminActionName()
    {
        return $this->getScopeValue(self::GENERAL_GROUP, self::ADMIN_ACTION_NAME);
    }

    /**
     * @param string $store
     *
     * @return string
     */
    public function getMinPointsRequirement($store)
    {
        return $this->getScopeValue(self::POINTS_GROUP, self::MINIMUM_REWARDS, $store);
    }

    /**
     * @param string $store
     *
     * @return string
     */
    public function getHighlightCartVisibility($store)
    {
        return $this->getScopeValue(self::HIGHLIGHT_GROUP, self::CART, $store);
    }

    /**
     * @param string $store
     *
     * @return string
     */
    public function getHighlightCheckoutVisibility($store)
    {
        return $this->getScopeValue(self::HIGHLIGHT_GROUP, self::CHECKOUT, $store);
    }

    /**
     * @param string $store
     *
     * @return string
     */
    public function getHighlightProductVisibility($store)
    {
        return $this->getScopeValue(self::HIGHLIGHT_GROUP, self::PRODUCT, $store);
    }

    /**
     * @param string $store
     *
     * @return string
     */
    public function getHighlightCategoryVisibility($store)
    {
        return $this->getScopeValue(self::HIGHLIGHT_GROUP, self::CATEGORY, $store);
    }

    /**
     * @param string $store
     *
     * @return bool
     */
    public function isHighlightGuestVisibility($store)
    {
        return (bool) $this->getScopeValue(self::HIGHLIGHT_GROUP, self::GUEST, $store);
    }

    /**
     * @param string $store
     *
     * @return string
     */
    public function getHighlightColor($store)
    {
        return $this->getScopeValue(self::HIGHLIGHT_GROUP, self::COLOR, $store);
    }

    /**
     * @param string $store
     *
     * @return string|null
     */
    public function getRewardsPointsDescription($store)
    {
        if ($this->isSetFlag(self::CUSTOMER_GROUP, self::DESCRIPTION_ENABLE, $store)) {
            return $this->getScopeValue(self::CUSTOMER_GROUP, self::DESCRIPTION_MESSAGE, $store);
        }

        return null;
    }

    /**
     * @param string $store
     *
     * @return string|null
     */
    public function isRewardsBalanceVisible($store)
    {
        return $this->isSetFlag(self::CUSTOMER_GROUP, self::SHOW_BALANCE, $store);
    }

    /**
     * @param string $store
     *
     * @return string|null
     */
    public function getBalanceLabel($store)
    {
        return $this->getScopeValue(self::CUSTOMER_GROUP, self::BALANCE_LABEL, $store);
    }
}
