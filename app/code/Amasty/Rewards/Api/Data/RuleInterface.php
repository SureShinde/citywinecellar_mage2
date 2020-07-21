<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Api\Data;

interface RuleInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const RULE_ID = 'rule_id';
    const IS_ACTIVE = 'is_active';
    const NAME = 'name';
    const CONDITIONS_SERIALIZED = 'conditions_serialized';
    const ACTION = 'action';
    const AMOUNT = 'amount';
    const SPENT_AMOUNT = 'spent_amount';
    const PROMO_SKUS = 'promo_skus';
    const INACTIVE_DAYS = 'inactive_days';
    const RECURRING = 'recurring';
    const EXPIRATION_BEHAVIOR = 'expiration_behavior';
    const EXPIRATION_PERIOD = 'expiration_period';
    /**#@-*/

    /**
     * Validate customer by his website and his group
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     *
     * @return bool
     */
    public function validateByCustomer($customer);

    /**
     * @return int
     */
    public function getRuleId();

    /**
     * @param int $ruleId
     *
     * @return \Amasty\Rewards\Api\Data\RuleInterface
     */
    public function setRuleId($ruleId);

    /**
     * @return int
     */
    public function getIsActive();

    /**
     * @param int $isActive
     *
     * @return \Amasty\Rewards\Api\Data\RuleInterface
     */
    public function setIsActive($isActive);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return \Amasty\Rewards\Api\Data\RuleInterface
     */
    public function setName($name);

    /**
     * @return string|null
     */
    public function getConditionsSerialized();

    /**
     * @param string|null $conditionsSerialized
     *
     * @return \Amasty\Rewards\Api\Data\RuleInterface
     */
    public function setConditionsSerialized($conditionsSerialized);

    /**
     * @return string|null
     */
    public function getAction();

    /**
     * @param string|null $action
     *
     * @return \Amasty\Rewards\Api\Data\RuleInterface
     */
    public function setAction($action);

    /**
     * @return float
     */
    public function getAmount();

    /**
     * @param float $amount
     *
     * @return \Amasty\Rewards\Api\Data\RuleInterface
     */
    public function setAmount($amount);

    /**
     * @return float
     */
    public function getSpentAmount();

    /**
     * @param float $spentAmount
     *
     * @return \Amasty\Rewards\Api\Data\RuleInterface
     */
    public function setSpentAmount($spentAmount);

    /**
     * @return string|null
     */
    public function getPromoSkus();

    /**
     * @param string|null $promoSkus
     *
     * @return \Amasty\Rewards\Api\Data\RuleInterface
     */
    public function setPromoSkus($promoSkus);

    /**
     * @return int
     */
    public function getInactiveDays();

    /**
     * @param int $days
     *
     * @return \Amasty\Rewards\Api\Data\RuleInterface
     */
    public function setInactiveDays($days);

    /**
     * @return int
     */
    public function getRecurring();

    /**
     * @param int $status
     *
     * @return \Amasty\Rewards\Api\Data\RuleInterface
     */
    public function setRecurring($status);

    /**
     * @param int $behavior
     *
     * @return \Amasty\Rewards\Api\Data\RuleInterface
     */
    public function setExpirationBehavior($behavior);

    /**
     * @return int
     */
    public function getExpirationBehavior();

    /**
     * @param int $period
     *
     * @return \Amasty\Rewards\Api\Data\RuleInterface
     */
    public function setExpirationPeriod($period);

    /**
     * @return int
     */
    public function getExpirationPeriod();

    /**
     * Get Rule label by specified store
     *
     * @param int $storeId
     *
     * @return string|bool
     */
    public function getStoreLabel($storeId);
}
