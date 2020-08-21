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

namespace Magestore\Rewardpoints\Model\Total\Quote;

/**
 * Class Point
 *
 * Reward Point Total quote
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class Point extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    protected $_code = 'rewardpoint';
    /**
     * @var \Magestore\Rewardpoints\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magestore\Rewardpoints\Helper\Customer
     */
    protected $helperCustomer;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magestore\Rewardpoints\Helper\Calculation\Spending
     */
    protected $calculationSpending;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var float
     */
    protected $baseDiscountItems;

    /**
     * @var \Magestore\Rewardpoints\Helper\Block\Spend
     */
    protected $helperSpend;

    /**
     * @var
     */
    protected $baseTotalItems;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * @var \Magento\Backend\Model\Session\QuoteFactory
     */
    protected $quoteSessionBackendFactory;

    /**
     * Point constructor.
     *
     * @param \Magestore\Rewardpoints\Helper\Data $helperData
     * @param \Magestore\Rewardpoints\Helper\Customer $helperCustomer
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magestore\Rewardpoints\Helper\Block\Spend $helperSpent
     * @param \Magestore\Rewardpoints\Helper\Calculation\Spending $calculationSpending
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Backend\Model\Session\QuoteFactory $quoteSessionBackendFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        \Magestore\Rewardpoints\Helper\Data $helperData,
        \Magestore\Rewardpoints\Helper\Customer $helperCustomer,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magestore\Rewardpoints\Helper\Block\Spend $helperSpent,
        \Magestore\Rewardpoints\Helper\Calculation\Spending $calculationSpending,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\App\State $appState,
        \Magento\Backend\Model\Session\QuoteFactory $quoteSessionBackendFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        $this->helperData = $helperData;
        $this->helperCustomer = $helperCustomer;
        $this->helperSpend = $helperSpent;
        $this->checkoutSession = $checkoutSession;
        $this->calculationSpending = $calculationSpending;
        $this->priceCurrency = $priceCurrency;
        $this->quoteRepository = $quoteRepository;
        $this->appState = $appState;
        $this->quoteSessionBackendFactory = $quoteSessionBackendFactory;
    }

    /**
     * Check Output
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address $address
     * @return bool
     */
    public function checkOutput($quote, $address)
    {
        if (!$this->helperData->isEnable($quote->getStoreId())) {
            return true;
        }
        if ($quote->isVirtual() && $address->getAddressType() == 'shipping') {
            return true;
        }
        if (!$quote->isVirtual() && $address->getAddressType() == 'billing') {
            return true;
        }
        if (!$this->checkoutSession->getData('use_point')) {
            return true;
        }
        return false;
    }

    /**
     * Collect reward points total
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this|Point
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);
        $address = $shippingAssignment->getShipping()->getAddress();
        if ($this->checkOutput($quote, $address)) {
            return $this;
        }
        $rewardSalesRules = $this->checkoutSession->getRewardSalesRules();
        if (!$rewardSalesRules) {
            return $this;
        }

        $maxPoints = $this->helperCustomer->getBalance();
        $baseTotal = $this->calculationSpending->getQuoteBaseTotal($quote, $address);

        if ($maxPointsPerOrder = $this->calculationSpending->getMaxPointsPerOrder($quote->getStoreId())) {
            $maxPoints = min($maxPointsPerOrder, $maxPoints);
        }
        $maxPoints -= $this->calculationSpending->getPointItemSpent();
        if ($maxPoints <= 0 || !$this->helperCustomer->isAllowSpend($quote->getStoreId())) {
            $this->checkoutSession->setRewardSalesRules([]);
            return $this;
        }
        $baseDiscount = 0;
        $pointUsed = 0;

        // Sales Rule (slider) discount Last
        if (is_array($rewardSalesRules)) {
            if ($baseTotal > 0.0 && isset($rewardSalesRules['rule_id'])) {
                $rule = $this->calculationSpending->getQuoteRule($rewardSalesRules['rule_id']);
                if ($rule && $rule->getId() && $rule->getSimpleAction() == 'by_price') {
                    $rulesData = $this->helperSpend->getRulesData($this->helperSpend->getSliderRules());
                    if ($this->appState->getAreaCode() == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {
                        $quote = $this->quoteSessionBackendFactory->create()->getQuote();
                    } else {
                        $quoteId = $this->checkoutSession->getQuoteId();
                        $quote = $this->quoteRepository->get($quoteId);
                    }
                    if (isset($rulesData) && $quote->getUseMaxPoint()) {
                        $usePoint = $rulesData['rate']['sliderOption']['maxPoints'];
                        $rewardSalesRules['use_point'] = max($rewardSalesRules['use_point'], $usePoint);
                    }
                    $points = min($rewardSalesRules['use_point'], $maxPoints);
                    $ruleDiscount = $this->calculationSpending->getQuoteRuleDiscount($quote, $rule, $points);
                    if ($ruleDiscount > 0.0) {
                        $baseTotal -= $ruleDiscount;
                        $baseDiscount += $ruleDiscount;
                        $pointUsed += $points;
                        $newRewardSalesRules = [
                            'rule_id' => $rule->getId(),
                            'use_point' => $points,
                            'base_discount' => $ruleDiscount,
                        ];
                        $this->checkoutSession->setRewardSalesRules($newRewardSalesRules);
                        $this->processDiscount($quote, $address, $total, $ruleDiscount, $points);
                    }
                }
            }
        }
        // verify quote total data
        if ($baseTotal < 0.0001) {
            $baseDiscount = $this->calculationSpending->getQuoteBaseTotal($quote, $address);
        }
        if ($baseDiscount) {
            $this->setBaseDiscount($baseDiscount, $total, $quote, $pointUsed);
        }
        return $this;
    }

    /**
     * Process discount for total
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address $address
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @param float $ruleDiscount
     * @param string $points
     */
    public function processDiscount(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Model\Quote\Address $address,
        \Magento\Quote\Model\Quote\Address\Total $total,
        $ruleDiscount,
        $points
    ) {
        $baseTotalWithoutShipping = $this->calculationSpending->getQuoteBaseTotalWithoutShippingFee($quote, $address);
        $maxDiscountItems = min($ruleDiscount, $baseTotalWithoutShipping);
        // Fix round issue
        $this->baseDiscountItems = 0;
        $this->baseTotalItems = 0;
        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($address->getAllItems() as $item) {
            if ($item->getParentItemId()) {
                continue;
            }
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                $this->calculateDiscountItem($total, $item, $baseTotalWithoutShipping, $maxDiscountItems, $points);
                $this->distributeDiscount($item);
                foreach ($item->getChildren() as $child) {
                    $this->aggregateItemDiscount($child, $total);
                }
            } else {
                $this->calculateDiscountItem($total, $item, $baseTotalWithoutShipping, $maxDiscountItems, $points);
                $this->aggregateItemDiscount($item, $total);
            }
        }

        $this->calculateDiscountShipping($address, $total, $ruleDiscount, $maxDiscountItems);
    }

    /**
     * Calculate item discount
     *
     * @param \Magento\Quote\Model\Quote\Address\Total $total ,
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param float $baseTotalWithoutShipping
     * @param float $maxDiscountItems
     * @param int $points
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function calculateDiscountItem($total, $item, $baseTotalWithoutShipping, $maxDiscountItems, $points)
    {
        $store = $item->getQuote()->getStore();
        $itemPrice = $this->calculationSpending->getItemPrice($item);
        $baseItemPrice = $this->calculationSpending->getItemBasePrice($item);
        $qty = $item->getTotalQty();
        $baseDiscountAmount = $item->getBaseDiscountAmount();
        $baseItemPriceAfterDiscount = $baseItemPrice * $qty - $baseDiscountAmount;
        $this->baseTotalItems += $baseItemPriceAfterDiscount;
        if ($this->baseTotalItems == $baseTotalWithoutShipping) {
            $maximumItemDiscount = $maxDiscountItems - $this->baseDiscountItems;
        } else {
            $discountRate = $baseItemPriceAfterDiscount / $baseTotalWithoutShipping;
            $maximumItemDiscount = $maxDiscountItems * $discountRate;
        }
        $baseRewardDiscountAmount = min($baseItemPriceAfterDiscount, $maximumItemDiscount);
        $baseRewardDiscountAmount = $this->priceCurrency->round($baseRewardDiscountAmount);
        $this->baseDiscountItems += $baseRewardDiscountAmount;

        $rewardDiscountAmount = $this->priceCurrency->convert($maximumItemDiscount, $store);
        $rewardDiscountAmount = min($itemPrice * $qty - $item->getDiscountAmount(), $rewardDiscountAmount);
        $rewardDiscountAmount = $this->priceCurrency->round($rewardDiscountAmount);

        $pointSpent = round($points * $baseItemPrice / $baseTotalWithoutShipping, 0, PHP_ROUND_HALF_DOWN);

        $item->setRewardpointsBaseDiscount($item->getRewardpointsBaseDiscount() + $baseRewardDiscountAmount)
            ->setRewardpointsDiscount($item->getRewardpointsDiscount() + $rewardDiscountAmount)
            ->setMagestoreBaseDiscount($item->getMagestoreBaseDiscount() + $baseRewardDiscountAmount)
            ->setMagestoreDiscount($item->getMagestoreDiscount() + $rewardDiscountAmount)
            ->setRewardpointsSpent($item->getRewardpointsSpent() + $pointSpent);
        $item->setBaseDiscountAmount($item->getBaseDiscountAmount() + $baseRewardDiscountAmount);
        $item->setDiscountAmount($item->getDiscountAmount() + $rewardDiscountAmount);
    }

    /**
     * Calculate discount shipping
     *
     * @param \Magento\Quote\Model\Quote\Address $address
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @param float $ruleDiscount
     * @param float $maxDiscountItems
     */
    public function calculateDiscountShipping($address, $total, $ruleDiscount, $maxDiscountItems)
    {
        if ($ruleDiscount <= $maxDiscountItems) {
            return $this;
        }
        $shippingAmount = $address->getShippingAmountForDiscount();
        if ($shippingAmount !== null) {
            $baseShippingAmount = $address->getBaseShippingAmountForDiscount();
        } else {
            $baseShippingAmount = $address->getBaseShippingAmount();
        }
        $baseShippingAmount = $baseShippingAmount - $address->getBaseShippingDiscountAmount();
        $baseDiscountShipping = $ruleDiscount - $maxDiscountItems;
        $baseDiscountShipping = min($baseDiscountShipping, $baseShippingAmount);
        $discountShipping = $this->priceCurrency->convert($baseDiscountShipping);

        $total->setRewardpointsBaseDiscountForShipping(
            $total->getRewardpointsBaseDiscountForShipping() + $baseDiscountShipping
        );
        $total->setRewardpointsDiscountForShipping(
            $total->getRewardpointsDiscountForShipping() + $discountShipping
        );
        $total->setMagestoreBaseDiscountForShipping(
            $total->getMagestoreBaseDiscountForShipping() + $baseDiscountShipping
        );
        $total->setMagestoreDiscountForShipping(
            $total->getMagestoreDiscountForShipping() + $discountShipping
        );
        $total->setBaseShippingDiscountAmount(
            max(0, $total->getBaseShippingDiscountAmount() + $baseDiscountShipping)
        );
        $total->setShippingDiscountAmount(max(0, $total->getShippingDiscountAmount() + $discountShipping));
        $total->addTotalAmount($this->getCode(), -$baseDiscountShipping);
        $total->addBaseTotalAmount($this->getCode(), -$discountShipping);
    }

    /**
     * Aggregate item discount information to total data and related properties
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this
     */
    public function aggregateItemDiscount(
        \Magento\Quote\Model\Quote\Item\AbstractItem $item,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        $total->addTotalAmount($this->getCode(), -$item->getRewardpointsDiscount());
        $total->addBaseTotalAmount($this->getCode(), -$item->getRewardpointsBaseDiscount());
        return $this;
    }

    /**
     * Distribute discount at parent item to children items
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @return $this
     */
    public function distributeDiscount(\Magento\Quote\Model\Quote\Item\AbstractItem $item)
    {
        $parentBaseRowTotal = $item->getBaseRowTotal();
        $keys = [
            'discount_amount',
            'base_discount_amount',
            'original_discount_amount',
            'base_original_discount_amount',
            'rewardpoints_base_discount',
            'rewardpoints_discount',
            'magestore_base_discount',
            'magestore_discount'
        ];
        $roundingDelta = [];
        foreach ($keys as $key) {
            //Initialize the rounding delta to a tiny number to avoid floating point precision problem
            $roundingDelta[$key] = 0.0000001;
        }
        foreach ($item->getChildren() as $child) {
            $ratio = $child->getBaseRowTotal() / $parentBaseRowTotal;
            foreach ($keys as $key) {
                if (!$item->hasData($key)) {
                    continue;
                }
                $value = $item->getData($key) * $ratio;
                $roundedValue = $this->priceCurrency->round($value + $roundingDelta[$key]);
                $roundingDelta[$key] += $value - $roundedValue;
                $child->setData($key, $roundedValue);
            }
        }

        foreach ($keys as $key) {
            $item->setData($key, 0);
        }
        return $this;
    }

    /**
     * Set base discount
     *
     * @param float $baseDiscount
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @param \Magento\Quote\Model\Quote $quote
     * @param int $pointUsed
     */
    public function setBaseDiscount($baseDiscount, $total, $quote, $pointUsed)
    {
        $discount = $this->priceCurrency->convert($baseDiscount);
        $total->setDiscountAmount($total->getDiscountAmount() - $discount);
        $total->setBaseDiscountAmount($total->getBaseDiscountAmount() - $baseDiscount);
        $total->setRewardpointsSpent($total->getRewardpointsSpent() + $pointUsed);
        $total->setRewardpointsBaseDiscount($total->getRewardpointsBaseDiscount() + $baseDiscount);
        $total->setRewardpointsDiscount($total->getRewardpointsDiscount() + $discount);
        $total->setMagestoreBaseDiscount($total->getMagestoreBaseDiscount() + $baseDiscount);
        $total->setMagestoreDiscount($total->getMagestoreDiscount() + $discount);
        $total->setBaseSubtotalWithDiscount($total->getBaseSubtotalWithDiscount() - $baseDiscount);
        $total->setSubtotalWithDiscount($total->getSubtotalWithDiscount() - $discount);
        $quote->setRewardpointsSpent($total->getRewardpointsSpent());
        $quote->setRewardpointsBaseDiscount($total->getRewardpointsBaseDiscount());
        $quote->setRewardpointsDiscount($total->getRewardpointsDiscount());
        $quote->setMagestoreBaseDiscount($quote->getMagestoreBaseDiscount() + $baseDiscount);
        $quote->setMagestoreDiscount($quote->getMagestoreDiscount() + $discount);
        $quote->setRewardpointsBaseDiscountForShipping($total->getRewardpointsBaseDiscountForShipping());
        $quote->setRewardpointsDiscountForShipping($total->getRewardpointsDiscountForShipping());
        $quote->setMagestoreBaseDiscountForShipping($total->getMagestoreBaseDiscountForShipping());
        $quote->setMagestoreDiscountForShipping($total->getMagestoreDiscountForShipping());
    }

    /**
     * Add point label
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        $arrayRewardInformation = [];
        if ($quote->getRewardpointsDiscount() != 0) {
            array_push(
                $arrayRewardInformation,
                [
                    'code' => $this->getCode(),
                    'title' => __('Use Point'),
                    'value' => -$quote->getRewardpointsDiscount(),
                ]
            );
        }
        if ($quote->getData('rewardpoints_spent') != 0) {
            array_push(
                $arrayRewardInformation,
                [
                    'code' => 'rewardpoints_spent',
                    'title' => __('You will spend'),
                    'value' => $quote->getData('rewardpoints_spent'),
                ]
            );
        }
        return $arrayRewardInformation;
    }
}
