<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Model;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Weee\Model\Config as WeeeConfig;
use Magento\Tax\Model\Calculation as TaxCalculation;

class Calculation
{
    /**
     * @var Config
     */
    private $rewardsConfig;

    /**
     * @var Config
     */
    private $configWee;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var TaxCalculation
     */
    private $taxCalculation;

    public function __construct(
        Config $rewardsConfig,
        WeeeConfig $configWee,
        PriceCurrencyInterface $priceCurrency,
        StoreManagerInterface $storeManager,
        TaxCalculation $taxCalculation
    ) {
        $this->rewardsConfig = $rewardsConfig;
        $this->configWee = $configWee;
        $this->priceCurrency = $priceCurrency;
        $this->storeManager = $storeManager;
        $this->taxCalculation = $taxCalculation;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address $address
     * @param \Amasty\Rewards\Model\Rule $rule
     *
     * @return float
     */
    public function calculateSpentReward(\Magento\Quote\Model\Quote\Address $address, $rule)
    {
        $spentAmount = $rule->getSpentAmount();
        $rewardAmount = $rule->getAmount();
        $calculationMode = $this->rewardsConfig->getEarningCalculationMode();
        $cartAmount = 0;

        $items = $address->getQuote()->getAllItems();
        $promoSkus = $rule->getPromoSkusArray();

        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($items as $item) {
            if (!$promoSkus || in_array($item->getSku(), $rule->getPromoSkusArray(), true)) {
                if ($item->getProduct()->getTypeId() == 'bundle') {
                    continue;
                }
                $cartAmount +=
                    $item->getBaseRowTotal() - $item->getBaseDiscountAmount()
                    + $item->getBaseDiscountTaxCompensationAmount();

                if ($calculationMode === Rule::AFTER_TAX) {
                    $cartAmount += $item->getBaseTaxAmount();

                    if ($this->configWee->isEnabled()) {
                        $cartAmount += $item->getBaseWeeeTaxAppliedRowAmnt();
                    }
                }
            }
        }

        $result = floor($cartAmount / $spentAmount) * $rewardAmount;

        return $result;
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface[] $products
     * @param int $customerId
     * @param \Amasty\Rewards\Api\Data\RuleInterface $rule $rule
     *
     * @return float
     */
    public function calculatePointsByProduct($products, $customerId, $rule)
    {
        $taxPercent = 0;
        $totalAmount = 0;
        $productAmount = count($products);

        $promoSkus = $rule->getPromoSkusArray();
        $currentCurrency = $this->storeManager->getStore()->getCurrentCurrency();
        $calculationMode = $this->rewardsConfig->getEarningCalculationMode();

        $this->storeManager->getStore()->setCurrentCurrency($this->storeManager->getStore()->getBaseCurrency());

        foreach ($products as $product) {
            if ($productAmount > 1 && $product->getTypeId() != 'simple') {
                continue;
            }

            $amount = 0;

            if (!$promoSkus || in_array($product->getSku(), $promoSkus, true)) {
                if ($calculationMode == Rule::AFTER_TAX) {
                    $addressRequestObject = $this->taxCalculation->getRateRequest(
                        null,
                        null,
                        null,
                        $this->storeManager->getStore(),
                        $customerId
                    );
                    $addressRequestObject->setProductClassId($product->getTaxClassId());

                    $taxPercent = $this->taxCalculation->getRate($addressRequestObject);
                }

                $amount = $product->getFinalPrice() * max($product->getQty(), $product->getCartQty());

                $amount *= (1 + $taxPercent/100);
            }
            $totalAmount += round($amount, 2);
        }

        $this->storeManager->getStore()->setCurrentCurrency($currentCurrency);

        return floor($totalAmount / $rule->getSpentAmount()) * $rule->getAmount();
    }

    /**
     * @param array $items
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @param float $points
     *
     * @return int
     */
    public function calculateDiscount($items, $total, $points)
    {
        $allCartPrice = 0;
        $storeId = $this->storeManager->getStore()->getId();

        $rate = $this->rewardsConfig->getPointsRate($storeId);

        usort($items, [$this, 'sortItems']);

        $basePoints = $points / $rate;
        $itemCount = 0;

        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            $allCartPrice += $this->getRealItemPrice($item);
            $itemCount++;
        }

        $roundRule = $this->rewardsConfig->getRoundRule($storeId);

        if ($allCartPrice < $basePoints) {
            if ($roundRule == 'down') {
                $basePoints = floor($allCartPrice);
            } else {
                $basePoints = $allCartPrice;
            }
        }

        $itemDiscount = [];

        if ($basePoints > 0) {
            $percent = $basePoints * 100 / $allCartPrice;
        } else {
            $percent = 0;
        }

        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }

            $itemPrice = $this->getRealItemPrice($item);
            $discount = $itemPrice * $percent / 100;
            $basePoints -= $discount;
            $itemDiscount[$item->getId()] = $discount;
        }

        if ($basePoints > 0) {
            $itemDiscount[$item->getId()] +=$basePoints;
        }

        $discountValue = 0;

        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            $this->discountItem($item, $total, $itemDiscount[$item->getId()]);
            $discountValue += $itemDiscount[$item->getId()];
        }

        $appliedPoints = $discountValue * $rate;

        if ($roundRule == 'up') {
            $appliedPoints = ceil($appliedPoints);
        }

        return $appliedPoints;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     *
     * @return float
     */
    private function getRealItemPrice($item)
    {
        $realPrice = $item->getBasePrice() * $item->getQty() - $item->getBaseDiscountAmount();

        return max(0, $realPrice);
    }

    /**
     * Sorting items before apply reward points
     * cheapest should go first
     *
     * @param \Magento\Quote\Model\Quote\Item $itemA
     * @param \Magento\Quote\Model\Quote\Item $itemB
     *
     * @return int
     */
    private function sortItems($itemA, $itemB)
    {
        if ($this->getRealItemPrice($itemA) > $this->getRealItemPrice($itemB)) {
            return 1;
        }

        if ($this->getRealItemPrice($itemA) < $this->getRealItemPrice($itemB)) {
            return -1;
        }

        return 0;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @param int|float $discount
     */
    protected function discountItem($item, $total, $discount)
    {
        $item->setBaseDiscountAmount($item->getBaseDiscountAmount() + $discount);
        $discountAmount = $this->priceCurrency->convert($discount, $this->storeManager->getStore());
        $item->setDiscountAmount($item->getDiscountAmount() + $discountAmount);
        $total->addTotalAmount('discount', -$discountAmount);
        $total->addBaseTotalAmount('discount', -$discount);
    }
}
