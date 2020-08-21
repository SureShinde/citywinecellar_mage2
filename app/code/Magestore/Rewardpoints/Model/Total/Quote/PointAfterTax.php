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
 * Quote total - point after tax model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PointAfterTax extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * @var \Magento\Checkout\Model\SessionFactory
     */
    protected $_checkOutSessionFactory;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * @var \Magestore\Rewardpoints\Helper\Config
     */
    protected $_helper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magestore\Rewardpoints\Helper\Data
     */
    protected $_helperData;

    /**
     * @var \Magestore\Rewardpoints\Helper\Block\Spend
     */
    protected $_blockSpend;

    /**
     * @var \Magestore\Rewardpoints\Helper\Calculation\Spending
     */
    protected $_calculationSpending;

    /**
     * @var \Magestore\Rewardpoints\Helper\Customer
     */
    protected $_helperCustomer;

    /**
     * @var \Magento\Tax\Helper\Data
     */
    protected $_taxHelperData;

    /**
     * PointAfterTax constructor.
     * @param \Magestore\Rewardpoints\Helper\Config $globalConfig
     * @param \Magento\Checkout\Model\SessionFactory $sessionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magestore\Rewardpoints\Helper\Data $helperData
     * @param \Magestore\Rewardpoints\Helper\Block\Spend $blockSpend
     * @param \Magestore\Rewardpoints\Helper\Calculation\Spending $calculationSpending
     * @param \Magestore\Rewardpoints\Helper\Customer $helperCustomer
     * @param \Magento\Tax\Helper\Data $taxHelperData
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magestore\Rewardpoints\Helper\Config $globalConfig,
        \Magento\Checkout\Model\SessionFactory $sessionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magestore\Rewardpoints\Helper\Data $helperData,
        \Magestore\Rewardpoints\Helper\Block\Spend $blockSpend,
        \Magestore\Rewardpoints\Helper\Calculation\Spending $calculationSpending,
        \Magestore\Rewardpoints\Helper\Customer $helperCustomer,
        \Magento\Tax\Helper\Data $taxHelperData
    ) {

        $this->setCode('rewardpoint');
        $this->_helper = $globalConfig;
        $this->_checkOutSessionFactory = $sessionFactory;
        $this->_storeManager = $storeManager;
        $this->_priceCurrency = $priceCurrency;
        $this->_helperData = $helperData;
        $this->_blockSpend = $blockSpend;
        $this->_calculationSpending = $calculationSpending;
        $this->_helperCustomer = $helperCustomer;
        $this->_taxHelperData = $taxHelperData;
    }

    /**
     * Check Output
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     * @param \Magento\Checkout\Model\Session $session
     * @return $this|bool
     */
    public function checkOutput($quote, $address, $session)
    {
        $applyTaxAfterDiscount = (bool) $this->_helper->getConfig(
            \Magento\Tax\Model\Config::CONFIG_XML_PATH_APPLY_AFTER_DISCOUNT,
            $quote->getStoreId()
        );
        if ($applyTaxAfterDiscount) {
            $this->_processHiddenTaxes($address);
            return true;
        }
        if (!$this->_helperData->isEnable($quote->getStoreId())) {
            return true;
        }
        if ($quote->isVirtual() && $address->getAddressType() == 'shipping') {
            return true;
        }
        if (!$quote->isVirtual() && $address->getAddressType() == 'billing') {
            return true;
        }
        if (!$session->getData('use_point')) {
            return $this;
        }
        return false;
    }
    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);
        $address = $shippingAssignment->getShipping()->getAddress();
        $session = $this->_checkOutSessionFactory->create();
        if ($this->checkOutput($quote, $address, $session)) {
            return $this;
        }
        $rewardSalesRules = $session->getRewardSalesRules();
        $rewardCheckedRules = $session->getRewardCheckedRules();
        if (!$rewardSalesRules && !$rewardCheckedRules) {
            return $this;
        }
        
        $helper = $this->_calculationSpending;
        $baseTotal = $helper->getQuoteBaseTotal($quote, $address);
        $maxPoints = $this->_helperCustomer->getBalance();
        if ($maxPointsPerOrder = $helper->getMaxPointsPerOrder($quote->getStoreId())) {
            $maxPoints = min($maxPointsPerOrder, $maxPoints);
        }
        $maxPoints -= $helper->getPointItemSpent();
        if ($maxPoints <= 0 || !$this->_helperCustomer->isAllowSpend($quote->getStoreId())) {
            $session->setRewardCheckedRules([]);
            $session->setRewardSalesRules([]);
            return $this;
        }
        $baseDiscount = 0;
        $pointUsed = 0;
        // Checked Rules Discount First
        if (is_array($rewardCheckedRules)) {
            $newRewardCheckedRules = [];
            foreach ($rewardCheckedRules as $ruleData) {
                if ($baseTotal < 0.0001) {
                    break;
                }
                $rule = $helper->getQuoteRule($ruleData['rule_id']);
                if (!$rule || !$rule->getId() || $rule->getSimpleAction() != 'fixed') {
                    continue;
                }
                if ($maxPoints < $rule->getPointsSpended()) {
                    $session->addNotice(
                        __(
                            'You cannot spend more than %s points per order',
                            $helper->getMaxPointsPerOrder($quote->getStoreId())
                        )
                    );
                    continue;
                }
                $points = $rule->getPointsSpended();
                $ruleDiscount = $helper->getQuoteRuleDiscount($quote, $rule, $points);
                if ($ruleDiscount < 0.0001) {
                    continue;
                }
                $baseTotal -= $ruleDiscount;
                $maxPoints -= $points;
                $baseDiscount += $ruleDiscount;
                $pointUsed += $points;
                $newRewardCheckedRules[$rule->getId()] = [
                    'rule_id' => $rule->getId(),
                    'use_point' => $points,
                    'base_discount' => $ruleDiscount,
                ];
                $this->_prepareDiscountForTaxAmount($shippingAssignment, $ruleDiscount, $points, $rule);
                if ($rule->getStopRulesProcessing()) {
                    break;
                }
            }
            $session->setRewardCheckedRules($newRewardCheckedRules);
        }
        // Sales Rule (slider) discount Last
        if (is_array($rewardSalesRules)) {
            $newRewardSalesRules = [];
            if ($baseTotal > 0.0 && isset($rewardSalesRules['rule_id'])) {
                $rule = $helper->getQuoteRule($rewardSalesRules['rule_id']);
                if ($rule && $rule->getId() && $rule->getSimpleAction() == 'by_price') {
                    $points = min($rewardSalesRules['use_point'], $maxPoints);
                    $ruleDiscount = $helper->getQuoteRuleDiscount($quote, $rule, $points);
                    if ($ruleDiscount > 0.0) {
                        $baseTotal -= $ruleDiscount;
                        $maxPoints -= $points;
                        $baseDiscount += $ruleDiscount;
                        $pointUsed += $points;
                        $newRewardSalesRules =[
                            'rule_id' => $rule->getId(),
                            'use_point' => $points,
                            'base_discount' => $ruleDiscount,
                        ];
                        if ($rule->getId() == 'rate') {
                            $this->_prepareDiscountForTaxAmount($shippingAssignment, $ruleDiscount, $points);
                        } else {
                            $this->_prepareDiscountForTaxAmount($shippingAssignment, $ruleDiscount, $points, $rule);
                        }
                    }
                }
            }
            $session->setRewardSalesRules($newRewardSalesRules);
        }
        // verify quote total data
        if ($baseTotal < 0.0001) {
            $baseTotal = 0.0;
            $baseDiscount = $helper->getQuoteBaseTotal($quote, $address);
        }
        if ($baseDiscount) {
            $this->setDiscount($baseDiscount, $total, $address, $pointUsed, $quote);
        }
        return $this;
    }

    /**
     * Set Discount
     *
     * @param float $baseDiscount
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     * @param int $pointUsed
     * @param \Magento\Quote\Model\Quote $quote
     */
    public function setDiscount($baseDiscount, $total, $address, $pointUsed, $quote)
    {
        $discount =  $this->_priceCurrency->convert($baseDiscount);
        $total->addTotalAmount('rewardpoints', -$discount);
        $total->addBaseTotalAmount('rewardpoints', -$baseDiscount);
        $total->setBaseGrandTotal($address->getBaseGrandTotal() - $baseDiscount);
        $total->setGrandTotal($address->getGrandTotal() - $discount);
        $total->setRewardpointsSpent($address->getRewardpointsSpent() + $pointUsed);
        $total->setRewardpointsBaseDiscount($address->getRewardpointsBaseDiscount() + $baseDiscount);
        $total->setRewardpointsDiscount($address->getRewardpointsDiscount() + $discount);
        $quote->setRewardpointsSpent($total->getRewardpointsSpent());
        $quote->setRewardpointsBaseDiscount($total->getRewardpointsBaseDiscount());
        $quote->setRewardpointsDiscount($total->getRewardpointsDiscount());
        $address->setMagestoreBaseDiscount($address->getMagestoreBaseDiscount() + $baseDiscount);
        $quote->setMagestoreBaseDiscount($quote->getRewardpointsBaseDiscount() + $baseDiscount);
    }

    /**
     * Prepare Discount Amount used for Tax
     *
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param float $baseDiscount
     * @param int $points
     * @param null|\Magento\Framework\DataObject $rule
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function _prepareDiscountForTaxAmount(
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        $baseDiscount,
        $points,
        $rule = null
    ) {
        $address = $shippingAssignment->getShipping()->getAddress();
        $items = $address->getAllItems();
        if (!count($items)) {
            return $this;
        }
        // Calculate total item prices
        $baseItemsPrice = 0;
        $spendHelper = $this->_calculationSpending;
        $baseParentItemsPrice = [];
        foreach ($items as $item) {
            if ($item->getParentItemId()) {
                continue;
            }
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                $baseParentItemsPrice[$item->getId()] = 0;
                foreach ($item->getChildren() as $child) {
                    if ($rule !== null && !$rule->getActions()->validate($child)) {
                        continue;
                    }
                    $baseParentItemsPrice[$item->getId()] = $item->getQty()
                        * ($child->getQty() * $spendHelper->_getItemBasePrice($child))
                        - $child->getBaseDiscountAmount() - $child->getMagestoreBaseDiscount();
                }
                $baseItemsPrice += $baseParentItemsPrice[$item->getId()];
            } elseif ($item->getProduct()) {
                if ($rule !== null && !$rule->getActions()->validate($item)) {
                    continue;
                }
                $baseItemsPrice += $item->getQty() * $spendHelper->_getItemBasePrice($item)
                    - $item->getBaseDiscountAmount() - $item->getMagestoreBaseDiscount();
            }
        }
        if ($baseItemsPrice < 0.0001) {
            return $this;
        }
        $discountForShipping = $this->_helper->getConfig(
            \Magestore\Rewardpoints\Helper\Calculation\Spending::XML_PATH_SPEND_FOR_SHIPPING,
            $address->getQuote()->getStoreId()
        );
        if ($baseItemsPrice < $baseDiscount && $discountForShipping) {
            $baseDiscountForShipping = $baseDiscount - $baseItemsPrice;
            $baseDiscount = $baseItemsPrice;
        } else {
            $baseDiscountForShipping = 0;
        }
        // Update discount for each item
        foreach ($items as $item) {
            if ($item->getParentItemId()) {
                continue;
            }
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                $parentItemBaseDiscount = $baseDiscount * $baseParentItemsPrice[$item->getId()] / $baseItemsPrice;
                foreach ($item->getChildren() as $child) {
                    if ($parentItemBaseDiscount <= 0) {
                        break;
                    }
                    if ($rule !== null && !$rule->getActions()->validate($child)) {
                        continue;
                    }
                    $baseItemPrice = $item->getQty() * ($child->getQty() * $spendHelper->_getItemBasePrice($child))
                        - $child->getBaseDiscountAmount() - $child->getMagestoreBaseDiscount();
                    $itemBaseDiscount = min($baseItemPrice, $parentItemBaseDiscount);
                    $parentItemBaseDiscount -= $itemBaseDiscount;
                    $itemDiscount = $this->_priceCurrency->convert($itemBaseDiscount);
                    $pointSpent = round($points * $baseItemPrice / $baseItemsPrice, 0, PHP_ROUND_HALF_DOWN);
                    $child->setRewardpointsBaseDiscount($child->getRewardpointsBaseDiscount() + $itemBaseDiscount)
                        ->setBaseDiscountAmount($child->getBaseDiscountAmount() + $itemBaseDiscount)
                        ->setRewardpointsDiscount($child->getRewardpointsDiscount() + $itemDiscount)
                        ->setMagestoreBaseDiscount($child->getMagestoreBaseDiscount() + $itemBaseDiscount)
                        ->setRewardpointsSpent($child->getRewardpointsSpent() + $pointSpent);
                }
            } elseif ($item->getProduct()) {
                if ($rule !== null && !$rule->getActions()->validate($item)) {
                    continue;
                }
                $baseItemPrice = $item->getQty() * $spendHelper->_getItemBasePrice($item)
                    - $item->getBaseDiscountAmount() - $item->getMagestoreBaseDiscount();
                $itemBaseDiscount = $baseDiscount * $baseItemPrice / $baseItemsPrice;
                $itemDiscount = $this->_priceCurrency->convert($itemBaseDiscount);
                $pointSpent = round($points * $baseItemPrice / $baseItemsPrice, 0, PHP_ROUND_HALF_DOWN);
                $item->setRewardpointsBaseDiscount($item->getRewardpointsBaseDiscount() + $itemBaseDiscount)
                    ->setBaseDiscountAmount($item->getBaseDiscountAmount() + $itemBaseDiscount)
                    ->setRewardpointsDiscount($item->getRewardpointsDiscount() + $itemDiscount)
                    ->setMagestoreBaseDiscount($item->getMagestoreBaseDiscount() + $itemBaseDiscount)
                    ->setRewardpointsSpent($item->getRewardpointsSpent() + $pointSpent);
            }
        }
        if ($baseDiscountForShipping) {
            $this->baseDiscountForShipping($address, $baseDiscountForShipping);
        }
        return $this;
    }

    /**
     * Base Discount For Shipping
     *
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     * @param float $baseDiscountForShipping
     */
    public function baseDiscountForShipping($address, $baseDiscountForShipping)
    {
        $shippingAmount = $address->getShippingAmountForDiscount();
        if ($shippingAmount !== null) {
            $baseShippingAmount = $address->getBaseShippingAmountForDiscount();
        } else {
            $baseShippingAmount = $address->getBaseShippingAmount();
        }
        $baseShipping = $baseShippingAmount - $address->getBaseShippingDiscountAmount()
            - $address->getMagestoreBaseDiscountForShipping();
        $itemBaseDiscount = ($baseDiscountForShipping <= $baseShipping) ? $baseDiscountForShipping : $baseShipping;
        $itemDiscount = $this->_priceCurrency->convert($itemBaseDiscount);
        $address->setRewardpointsBaseAmount($address->getRewardpointsBaseAmount() + $itemBaseDiscount)
            ->setBaseShippingDiscountAmount($address->getBaseShippingDiscountAmount() + $itemBaseDiscount)
            ->setRewardpointsAmount($address->getRewardpointsAmount() + $itemDiscount)
            ->setMagestoreBaseDiscountForShipping($address->getMagestoreBaseDiscountForShipping() + $itemBaseDiscount);
    }

    /**
     * Process Hidden Taxes
     *
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     */
    public function _processHiddenTaxes($address)
    {
        foreach ($address->getAllItems() as $item) {
            if ($item->getParentItemId()) {
                continue;
            }
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $child->setHiddenTaxAmount($child->getHiddenTaxAmount() + $child->getRewardpointsHiddenTaxAmount());
                    $child->setBaseHiddenTaxAmount(
                        $child->getBaseHiddenTaxAmount() + $child->getRewardpointsBaseHiddenTaxAmount()
                    );

                    $address->addTotalAmount('hidden_tax', $child->getRewardpointsHiddenTaxAmount());
                    $address->addBaseTotalAmount('hidden_tax', $child->getRewardpointsBaseHiddenTaxAmount());
                }
            } elseif ($item->getProduct()) {
                $item->setHiddenTaxAmount($item->getHiddenTaxAmount() + $item->getRewardpointsHiddenTaxAmount());
                $item->setBaseHiddenTaxAmount(
                    $item->getBaseHiddenTaxAmount() + $item->getRewardpointsBaseHiddenTaxAmount()
                );

                $address->addTotalAmount('hidden_tax', $item->getRewardpointsHiddenTaxAmount());
                $address->addBaseTotalAmount('hidden_tax', $item->getRewardpointsBaseHiddenTaxAmount());
            }
        }
        if ($address->getRewardpointsShippingHiddenTaxAmount()) {
            $address->addTotalAmount('shipping_hidden_tax', $address->getRewardpointsShippingHiddenTaxAmount());
            $address->addBaseTotalAmount('shipping_hidden_tax', $address->getRewardpointsBaseShippingHiddenTaxAmount());
        }
    }
}
