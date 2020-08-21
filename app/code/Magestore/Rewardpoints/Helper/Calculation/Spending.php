<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Rewardpoints\Helper\Calculation;

/**
 * RewardPoints Spending Calculation Helper
 */
class Spending extends \Magestore\Rewardpoints\Helper\Calculation\AbstractCalculation
{

    const XML_PATH_MAX_POINTS_PER_ORDER = 'rewardpoints/spending/max_points_per_order';
    const XML_PATH_FREE_SHIPPING = 'rewardpoints/spending/free_shipping';
    const XML_PATH_SPEND_FOR_SHIPPING = 'rewardpoints/spending/spend_for_shipping';
    const XML_PATH_ORDER_REFUND_STATUS = 'rewardpoints/spending/order_refund_state';
    const XML_PATH_MAX_POINTS_DEFAULT = 'rewardpoints/spending/max_point_default';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Checkout\Model\SessionFactory
     */
    protected $_checkoutSessionFactory;

    /**
     * @var \Magestore\Rewardpoints\Model\RateFactory
     */
    protected $_rateModelFactory;

    /**
     * Event manager
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * Spending constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magestore\Rewardpoints\Helper\Config $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Checkout\Model\SessionFactory $checkoutSessionFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Customer\Model\SessionFactory $customerSessionFactory
     * @param \Magestore\Rewardpoints\Model\RateFactory $rateModelFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magestore\Rewardpoints\Helper\Config $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\SessionFactory $checkoutSessionFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Model\SessionFactory $customerSessionFactory,
        \Magestore\Rewardpoints\Model\RateFactory $rateModelFactory
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_checkoutSessionFactory = $checkoutSessionFactory;
        $this->_rateModelFactory = $rateModelFactory;
        $this->_eventManager = $context->getEventManager();
        parent::__construct($context, $storeManager, $customerSessionFactory, $checkoutSessionFactory, $objectManager);
    }

    /**
     * Get Max point that customer can used to spend for an order
     *
     * @param mixed $store
     * @return int
     */
    public function getMaxPointsPerOrder($store = null)
    {
        $maxPerOrder = (int)$this->_scopeConfig->getConfig(self::XML_PATH_MAX_POINTS_PER_ORDER, $store);
        if ($maxPerOrder > 0) {
            return $maxPerOrder;
        }
        return 0;
    }

    /**
     * Get Total Point that customer used to spent for the order
     *
     * @return int
     */
    public function getTotalPointSpent()
    {
        $container = new \Magento\Framework\DataObject([
            'total_point_spent' => 0
        ]);

        $this->_eventManager->dispatch('rewardpoints_calculation_spending_get_total_point', [
            'container' => $container,
        ]);
        return $this->getPointItemSpent() + $this->getCheckedRulePoint()
            + $this->getSliderRulePoint() + $container->getTotalPointSpent();
    }

    /**
     * Get discount (Base Currency) by points of each product item on the shopping cart with $item is null,
     *
     * Result is the total discount of all items
     *
     * @param \Magento\Quote\Model\Quote\Item|null $item
     * @return float
     */
    public function getPointItemDiscount($item = null)
    {
        $container = new \Magento\Framework\DataObject([
            'point_item_discount' => 0
        ]);
        $this->_eventManager->dispatch('rewardpoints_calculation_spending_point_item_discount', [
            'item' => $item,
            'container' => $container,
        ]);
        return $container->getPointItemDiscount();
    }

    /**
     * Get point that customer used to spend for each product item with $item is null,
     *
     * Result is the total points used for all items
     *
     * @param \Magento\Quote\Model\Quote\Item|null $item
     * @return int
     */
    public function getPointItemSpent($item = null)
    {
        $container = new \Magento\Framework\DataObject([
            'point_item_spent' => 0
        ]);
        $this->_eventManager->dispatch('rewardpoints_calculation_spending_point_item_spent', [
            'item' => $item,
            'container' => $container,
        ]);
        return $container->getPointItemSpent();
    }

    /**
     * Pre collect total for quote/address and return quote total
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param null|\Magento\Quote\Model\Quote\Address $address
     * @param boolean $isApplyAfterTax
     * @return float
     */
    public function getQuoteBaseTotal($quote, $address = null, $isApplyAfterTax = false)
    {
        /**
         * $cacheKey = 'quote_base_total';
         * if ($this->hasCache($cacheKey)) {
         * return $this->getCache($cacheKey);
         * }
         */

        $baseTotal = $this->getQuoteBaseTotalWithoutShippingFee($quote, $address, $isApplyAfterTax);

        if ($this->_scopeConfig->getConfig(self::XML_PATH_SPEND_FOR_SHIPPING, $quote->getStoreId())) {
            $shippingAmount = $address->getShippingAmountForDiscount();
            if ($shippingAmount !== null) {
                $baseShippingAmount = $address->getBaseShippingAmountForDiscount();
            } else {
                $baseShippingAmount = $address->getBaseShippingAmount();
            }
            $baseTotal += $baseShippingAmount
                - $address->getBaseShippingDiscountAmount()
                + $address->getRewardpointsBaseDiscountForShipping();
            if ($isApplyAfterTax) {
                $baseTotal += $address->getBaseShippingTaxAmount();
            }
        }
        return $baseTotal;
    }

    /**
     * Pre collect total for quote/address and return quote total without shipping fee
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param null|\Magento\Quote\Model\Quote\Address $address
     * @param boolean $isApplyAfterTax
     * @return float
     */
    public function getQuoteBaseTotalWithoutShippingFee($quote, &$address = null, $isApplyAfterTax = false)
    {
        $baseTotal = 0;
        if ($address === null) {
            if ($quote->isVirtual()) {
                $address = $quote->getBillingAddress();
            } else {
                $address = $quote->getShippingAddress();
            }
        }
        foreach ($address->getAllItems() as $item) {
            if ($item->getParentItemId()) {
                continue;
            }
            $qty = $item->getTotalQty();
            $baseTotal += $this->getItemBasePrice($item) * $qty
                - $item->getBaseDiscountAmount()
                + $item->getRewardpointsBaseDiscount();
            if ($isApplyAfterTax) {
                $baseTotal += $item->getBaseTaxAmount();
            }
        }
        return $baseTotal;
    }

    /**
     * Return item base price
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @return float
     */
    public function getItemBasePrice($item)
    {
        $price = $item->getDiscountCalculationPrice();
        return ($price !== null) ? $price : $item->getBaseCalculationPrice();
    }

    /**
     * Return item price
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @return float
     */
    public function getItemPrice($item)
    {
        $price = $item->getDiscountCalculationPrice();
        return $price === null ? $item->getCalculationPrice() : $price;
    }

    /**
     * Get discount (Base Currency) by points that spent with check rule type
     *
     * @return float
     */
    public function getCheckedRuleDiscount()
    {
        $container = new \Magento\Framework\DataObject([
            'checked_rule_discount' => 0
        ]);
        $this->_eventManager->dispatch('rewardpoints_calculation_spending_checked_rule_discount', [
            'container' => $container,
        ]);
        return $container->getCheckedRuleDiscount();
    }

    /**
     * Get points used to spend for checked rules
     *
     * @return int
     */
    public function getCheckedRulePoint()
    {
        $container = new \Magento\Framework\DataObject([
            'checked_rule_point' => 0
        ]);
        $this->_eventManager->dispatch('rewardpoints_calculation_spending_checked_rule_point', [
            'container' => $container,
        ]);
        return $container->getCheckedRulePoint();
    }

    /**
     * Get discount (base currency) by points that spent with slider rule type
     *
     * @return float
     */
    public function getSliderRuleDiscount()
    {
        $rewardSalesRules = $this->_checkoutSessionFactory->create()->getRewardSalesRules();
        if (is_array($rewardSalesRules)
            && isset($rewardSalesRules['base_discount'])
            && $this->_checkoutSessionFactory->create()->getData('use_point')
        ) {
            return $rewardSalesRules['base_discount'];
        }
        return 0;
    }

    /**
     * Get points used to spend by slider rule
     *
     * @return int
     */
    public function getSliderRulePoint()
    {
        $rewardSalesRules = $this->_checkoutSessionFactory->create()->getRewardSalesRules();
        if (is_array($rewardSalesRules)
            && isset($rewardSalesRules['use_point'])
            && $this->_checkoutSessionFactory->create()->getData('use_point')
        ) {

            return $rewardSalesRules['use_point'];
        }
        return 0;
    }

    /**
     * Get total point spent by rules on shopping cart
     *
     * @return int
     */
    public function getTotalRulePoint()
    {
        return $this->getCheckedRulePoint() + $this->getSliderRulePoint();
    }

    /**
     * Get quote spending rule by RuleID
     *
     * @param int|string $ruleId
     * @return \Magento\Framework\DataObject
     */
    public function getQuoteRule($ruleId = 'rate')
    {
        $cacheKey = "quote_rule_model:$ruleId";

        if (!$this->hasCache($cacheKey)) {
            if ($ruleId == 'rate') {
                $this->saveCache($cacheKey, $this->getSpendingRateAsRule());
                return $this->getCache($cacheKey);
            }
            $container = new \Magento\Framework\DataObject([
                'quote_rule_model' => null
            ]);
            $this->_eventManager->dispatch('rewardpoints_calculation_spending_quote_rule_model', [
                'container' => $container,
                'rule_id' => $ruleId,
            ]);

            $this->saveCache($cacheKey, $container->getQuoteRuleModel());

        }
        return $this->getCache($cacheKey);
    }

    /**
     * Get Spend Rates as a special rule (with id = 'rate')
     *
     * @return \Magento\Framework\DataObject|false
     */
    public function getSpendingRateAsRule()
    {

        $customerGroupId = $this->getCustomerGroupId();
        $websiteId = $this->getWebsiteId();
        $cacheKey = "rate_as_rule:$customerGroupId:$websiteId";
//        if ($this->hasCache($cacheKey)) {
//            return $this->getCache($cacheKey);
//        }
        $rate = $this->_rateModelFactory->create()->getRate(
            \Magestore\Rewardpoints\Model\Rate::POINT_TO_MONEY,
            $customerGroupId,
            $websiteId
        );
        if ($rate && $rate->getId()) {
            /**
             * end update
             */
            $this->saveCache($cacheKey, new \Magento\Framework\DataObject([
                'points_spended' => $rate->getPoints(),
                'base_rate' => $rate->getMoney(),
                'simple_action' => 'by_price',
                'id' => 'rate',
                'max_price_spended_type' => $rate->getMaxPriceSpendedType(), //Hai.Tran 13/11
                'max_price_spended_value' => $rate->getMaxPriceSpendedValue()//Hai.Tran 13/11
            ]));
        } else {
            $this->saveCache($cacheKey, false);
        }
        return $this->getCache($cacheKey);
    }

    /**
     * Get max points can used to spend for a quote
     *
     * @param \Magento\Framework\DataObject $rule
     * @param \Magento\Quote\Model\Quote $quote
     * @return int
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getRuleMaxPointsForQuote($rule, $quote)
    {
        $cacheKey = "rule_max_points_for_quote:{$rule->getId()}";
//        if ($this->hasCache($cacheKey)) {
//            return $this->getCache($cacheKey);
//        }
        if ($rule->getId() == 'rate') {
            if ($rule->getBaseRate() && $rule->getPointsSpended()) {
                $quoteTotal = $this->getQuoteBaseTotal($quote);
                //Hai.Tran 13/11/2013 add limit spend theo quote total
                //Tinh max point cho max total
                $maxPrice = $rule->getMaxPriceSpendedValue() > 0 ? $rule->getMaxPriceSpendedValue() : 0;
                if ($rule->getMaxPriceSpendedType() == 'by_price') {
                    $maxPriceSpend = $maxPrice;
                } elseif ($rule->getMaxPriceSpendedType() == 'by_percent') {
                    $maxPriceSpend = $quoteTotal * $maxPrice / 100;
                } else {
                    $maxPriceSpend = 0;
                }
                if ($quoteTotal > $maxPriceSpend && $maxPriceSpend > 0) {
                    $quoteTotal = $maxPriceSpend;
                }
                //End Hai.Tran 13/11/2013 add limit spend theo quote total

                $maxPoints = ceil(($quoteTotal - $this->getCheckedRuleDiscount()) / $rule->getBaseRate())
                    * $rule->getPointsSpended();
                if ($maxPerOrder = $this->getMaxPointsPerOrder($quote->getStoreId())) {
                    $maxPerOrder -= $this->getPointItemSpent();
                    $maxPerOrder -= $this->getCheckedRulePoint();
                    if ($maxPerOrder > 0) {
                        $maxPoints = min($maxPoints, $maxPerOrder);
                        $maxPoints = floor($maxPoints / $rule->getPointsSpended()) * $rule->getPointsSpended();
                    } else {
                        $maxPoints = 0;
                    }
                }
                $this->saveCache($cacheKey, $maxPoints);
            }
        } else {
            $container = new \Magento\Framework\DataObject([
                'rule_max_points' => 0
            ]);
            $this->_eventManager->dispatch('rewardpoints_calculation_spending_rule_max_points', [
                'rule' => $rule,
                'quote' => $quote,
                'container' => $container,
            ]);
            $this->saveCache($cacheKey, $container->getRuleMaxPoints());
        }
        if (!$this->hasCache($cacheKey)) {
            $this->saveCache($cacheKey, 0);
        }
        return $this->getCache($cacheKey);
    }

    /**
     * Get discount for quote when a rule is applied and recalculate real point used
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Framework\DataObject $rule
     * @param int $points
     * @return float
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getQuoteRuleDiscount($quote, $rule, &$points)
    {
        $cacheKey = "quote_rule_discount:{$rule->getId()}:$points";

        if ($this->hasCache($cacheKey)) {
            return $this->getCache($cacheKey);
        }

        if ($rule->getId() == 'rate') {

            if ($rule->getBaseRate() && $rule->getPointsSpended()) {

                $baseTotal = $this->getQuoteBaseTotal($quote) - $this->getCheckedRuleDiscount();

                /* Brian 26/1/2015 */
                $maxDiscountSpended = 0;
                if ($maxPriceSpended = $rule->getMaxPriceSpendedValue()) {
                    if ($rule->getMaxPriceSpendedType() == 'by_price') {
                        $maxDiscountSpended = $maxPriceSpended;
                    } elseif ($rule->getMaxPriceSpendedType() == 'by_percent') {
                        $maxDiscountSpended = $this->getQuoteBaseTotal($quote) * $maxPriceSpended / 100;
                    }
                }
                if ($maxDiscountSpended > 0) {
                    $baseTotal = min($maxDiscountSpended, $baseTotal);
                }
                /* end */
                $maxPoints = ceil($baseTotal / $rule->getBaseRate()) * $rule->getPointsSpended();

                if ($maxPerOrder = $this->getMaxPointsPerOrder($quote->getStoreId())) {
                    $maxPerOrder -= $this->getPointItemSpent();
                    $maxPerOrder -= $this->getCheckedRulePoint();
                    if ($maxPerOrder > 0) {
                        $maxPoints = min($maxPoints, $maxPerOrder);
                    } else {
                        $maxPoints = 0;
                    }
                }

                $points = min($points, $maxPoints);

                $points = floor($points / $rule->getPointsSpended()) * $rule->getPointsSpended();
                $this->saveCache(
                    $cacheKey,
                    min($points * $rule->getBaseRate() / $rule->getPointsSpended(), $baseTotal)
                );
            } else {
                $points = 0;
                $this->saveCache($cacheKey, 0);
            }
        } else {
            $container = new \Magento\Framework\DataObject([
                'quote_rule_discount' => 0,
                'points' => $points
            ]);
            $this->_eventManager->dispatch('rewardpoints_calculation_spending_quote_rule_discount', [
                'rule' => $rule,
                'quote' => $quote,
                'container' => $container,
            ]);
            $points = $container->getPoints();
            $this->saveCache($cacheKey, $container->getQuoteRuleDiscount());
        }
        return $this->getCache($cacheKey);
    }

    /**
     * Is Use Max Points Default
     *
     * @param null|int|string $store
     * @return mixed
     */
    public function isUseMaxPointsDefault($store = null)
    {
        return $this->_scopeConfig->getConfig(self::XML_PATH_MAX_POINTS_DEFAULT, $store);
    }

    /**
     * Is Use Point
     *
     * @return mixed
     */
    public function isUsePoint()
    {
        return $this->_checkoutSessionFactory->create()->getData('use_point');
    }
}
