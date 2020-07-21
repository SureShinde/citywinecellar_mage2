<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Plugin\SalesRule\Model\Quote;

use Amasty\Rewards\Model\Config\Source\RedemptionLimitTypes;

class Discount
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Amasty\Rewards\Model\Rule
     */
    protected $rule;

    /**
     * @var \Magento\SalesRule\Model\Validator
     */
    protected $validator;

    /**
     * @var \Amasty\Rewards\Model\Config
     */
    private $config;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var \Amasty\Rewards\Model\Quote
     */
    private $rewardQuote;

    /**
     * @var \Amasty\Rewards\Model\Calculation
     */
    private $calculation;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Amasty\Rewards\Model\Rule $rule,
        \Magento\SalesRule\Model\Validator $validator,
        \Amasty\Rewards\Model\Config $config,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Amasty\Rewards\Model\Quote $rewardQuote,
        \Amasty\Rewards\Model\Calculation $calculation
    ) {
        $this->registry = $registry;
        $this->rule = $rule;
        $this->validator = $validator;
        $this->config = $config;
        $this->quoteRepository = $quoteRepository;
        $this->rewardQuote = $rewardQuote;
        $this->calculation = $calculation;
    }

    public function aroundCollect(
        \Magento\SalesRule\Model\Quote\Discount $subject,
        \Closure $closure,
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        if (!$this->config->isEnabled($quote->getStoreId())) {
            return $closure($quote, $shippingAssignment, $total);
        }
        /** @var $address \Magento\Quote\Model\Quote\Address */
        $address = $shippingAssignment->getShipping()->getAddress();

        $result = $closure($quote, $shippingAssignment, $total);

        $items = $shippingAssignment->getItems();

        if (!$items || !$quote->hasAmrewardsPoint()) {
            return $result;
        }

        $appliedPoints = $this->calculation->calculateDiscount($items, $total, $quote->getAmrewardsPoint());
        $currentUsedPoints = $this->registry->registry('ampoints_used');
        $isEnableLimit = $this->config->isEnableLimit($quote->getStoreId());

        if ($isEnableLimit == RedemptionLimitTypes::LIMIT_PERCENT) {
            $limitPercent = $this->config->getRewardPercentLimit($quote->getStoreId());
            $rate = $this->config->getPointsRate($quote->getStoreId());
            $basePoints = $appliedPoints/$rate;
            $allowedPercent = round(($total->getSubtotal() / 100 * $limitPercent) / $quote->getBaseToQuoteRate(), 2);

            if ($basePoints > $allowedPercent) {
                $itemsCount = $quote->getItemsCount();
                if ($itemsCount) {
                    $total->setDiscountAmount(0);
                    $total->setBaseDiscountAmount(0);
                }
                $this->rewardQuote->addReward($quote->getId(), 0);
            }
        }

        if ($appliedPoints > 0 && !$currentUsedPoints) {
            if ($appliedPoints != $quote->getAmrewardsPoint()) {
                $quote->setData('amrewards_point', $appliedPoints);
            }
            $this->registry->register('ampoints_used', $appliedPoints);
        }

        $this->rule->addDiscountDescription($address, $appliedPoints);
        $this->validator->prepareDescription($address);

        $total->setDiscountDescription($address->getDiscountDescription());

        $total->setSubtotalWithDiscount($total->getSubtotal() + $total->getDiscountAmount());
        $total->setBaseSubtotalWithDiscount($total->getBaseSubtotal() + $total->getBaseDiscountAmount());

        return $result;
    }
}
