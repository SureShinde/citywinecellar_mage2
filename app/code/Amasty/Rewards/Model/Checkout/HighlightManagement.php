<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Model\Checkout;

use Amasty\Rewards\Api\CheckoutHighlightManagementInterface;
use Amasty\Rewards\Api\Data\HighlightInterface;
use Amasty\Rewards\Api\Data\HighlightInterfaceFactory;
use Amasty\Rewards\Api\RuleRepositoryInterface;
use Amasty\Rewards\Helper\Data;
use Amasty\Rewards\Model\Calculation;
use Amasty\Rewards\Model\Config;
use Magento\Checkout\Model\SessionFactory as CheckoutSessionFactory;
use Magento\Customer\Model\SessionFactory as CustomerSessionFactory;
use Magento\Framework\Api\AbstractSimpleObject;
use Magento\Quote\Api\CartManagementInterface;

class HighlightManagement extends AbstractSimpleObject implements CheckoutHighlightManagementInterface
{
    /**
     * @var Config
     */
    private $rewardsConfig;

    /**
     * @var Calculation
     */
    private $calculation;

    /**
     * @var CheckoutSessionFactory
     */
    private $checkoutSessionFactory;

    /**
     * @var CustomerSessionFactory
     */
    private $customerSessionFactory;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    private $quote;

    /**
     * @var float|null
     */
    private $amount = null;

    /**
     * @var CartManagementInterface
     */
    private $cartManagement;

    /**
     * @var HighlightInterfaceFactory
     */
    private $highlightFactory;

    public function __construct(
        Config $rewardsConfig,
        Calculation $calculation,
        CheckoutSessionFactory $checkoutSessionFactory,
        CustomerSessionFactory $customerSessionFactory,
        RuleRepositoryInterface $ruleRepository,
        CartManagementInterface $cartManagement,
        HighlightInterfaceFactory $highlightFactory
    ) {
        $this->rewardsConfig = $rewardsConfig;
        $this->calculation = $calculation;
        $this->checkoutSessionFactory = $checkoutSessionFactory;
        $this->customerSessionFactory = $customerSessionFactory;
        $this->ruleRepository = $ruleRepository;
        $this->cartManagement = $cartManagement;
        $this->highlightFactory = $highlightFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getHighlightData()
    {
        $storeId = $this->getQuote()->getStoreId();

        return [
            'highlight' => [
                HighlightInterface::VISIBLE => $this->canShow(),
                HighlightInterface::CAPTION_COLOR => $this->rewardsConfig->getHighlightColor($storeId),
                HighlightInterface::CAPTION_TEXT => __('%1 Reward Points', $this->amount)
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fillData()
    {
        /** @var HighlightInterface $highlight */
        $highlight = $this->highlightFactory->create();
        $highlight->setData($this->getHighlightData()['highlight']);

        return $highlight;
    }

    /**
     * @inheritdoc
     */
    public function getHighlightByCustomerId($customerId)
    {
        $this->quote = $this->cartManagement->getCartForCustomer($customerId);
        $hide = $this->rewardsConfig->isDisabledEarningByRewards($this->getQuote()->getStoreId())
            && floatval($this->getQuote()->getAmrewardsPoint());

        if ($hide) {
            $this->amount = 0;
        }

        $this->calculateAmount();

        $hide = !$this->amount && !$hide;

        /** @var HighlightInterface $highlight */
        $highlight = $this->highlightFactory->create();
        $highlight->setCaptionColor($this->rewardsConfig->getHighlightColor($this->getQuote()->getStoreId()));
        $highlight->setVisible(!$hide);
        $highlight->setCaptionText(__('%1 Reward Points', $this->amount));

        return $highlight;
    }

    /**
     * Check customer is logged in website,
     * rewards can be earned by this order
     *
     * @return bool
     */
    private function canShow()
    {
        if (($this->rewardsConfig->isDisabledEarningByRewards($this->getQuote()->getStoreId())
                && floatval($this->getQuote()->getAmrewardsPoint()))
        ) {
            $this->amount = 0;
        }

        return (bool) $this->calculateAmount();
    }

    /**
     * {@inheritdoc}
     */
    public function isVisible($page)
    {
        switch ($page) {
            case self::CART:
                $result = $this->rewardsConfig->getHighlightCartVisibility($this->getQuote()->getStoreId());
                break;
            case self::CHECKOUT:
                $result = $this->rewardsConfig->getHighlightCheckoutVisibility($this->getQuote()->getStoreId());
                break;
            default:
                $result = false;
                break;
        }

        return $this->customerSessionFactory->create()->isLoggedIn() && $result;
    }

    /**
     * Calculate amount by MONEY_SPENT_ACTION and ORDER_COMPLETED_ACTION rules.
     *
     * @return float|null
     */
    private function calculateAmount()
    {
        if ($this->amount === null) {
            $amount = 0;
            $website = $this->getQuote()->getStore()->getWebsiteId();
            $customerGroup = $this->getQuote()->getCustomerGroupId()
                ?: $this->customerSessionFactory->create()->getCustomerGroupId();

            $rules = $this->ruleRepository->getRulesByAction(Data::MONEY_SPENT_ACTION, $website, $customerGroup);
            $address = $this->getAddress();

            /** @var \Amasty\Rewards\Api\Data\RuleInterface $rule */
            foreach ($rules as $rule) {
                if ($rule->validate($address)) {
                    $amount += $this->calculation->calculateSpentReward($address, $rule);
                }
            }

            $rules = $this->ruleRepository->getRulesByAction(Data::ORDER_COMPLETED_ACTION, $website, $customerGroup);

            /** @var \Amasty\Rewards\Api\Data\RuleInterface $rule */
            foreach ($rules as $rule) {
                if ($rule->validate($address)) {
                    $amount += $rule->getAmount();
                }
            }

            $this->amount = floatval(round($amount, 2));
        }

        return $this->amount;
    }

    /**
     * Return address from quote to use in calculation.
     *
     * @return \Magento\Quote\Model\Quote\Address
     */
    private function getAddress()
    {
        if ($this->getQuote()->isVirtual()) {
            $address = $this->getQuote()->getBillingAddress();
        } else {
            $address = $this->getQuote()->getShippingAddress();
        }

        return $address;
    }

    /**
     * Return current quote from session.
     *
     * @return \Magento\Quote\Model\Quote
     */
    private function getQuote()
    {
        if (!$this->quote) {
            /** @var \Magento\Checkout\Model\Session $checkoutSession */
            $checkoutSession = $this->checkoutSessionFactory->create();

            $this->quote = $checkoutSession->getQuote();
        }

        return $this->quote;
    }
}
