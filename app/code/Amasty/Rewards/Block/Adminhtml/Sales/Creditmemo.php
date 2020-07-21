<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Block\Adminhtml\Sales;

class Creditmemo extends \Magento\Backend\Block\Template
{
    const REFUND_KEY = 'reward_points_to_refund';

    const EARNED_POINTS_KEY = 'earned_points';

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Amasty\Rewards\Model\ResourceModel\Quote
     */
    private $rewardQuoteResource;

    /**
     * @var \Amasty\Rewards\Model\Config
     */
    private $configProvider;

    /**
     * @var \Amasty\Rewards\Api\RewardsRepositoryInterface
     */
    private $rewardsRepository;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    private $currencyHelper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Amasty\Rewards\Model\ResourceModel\Quote $rewardQuoteResource,
        \Amasty\Rewards\Model\Config $configProvider,
        \Amasty\Rewards\Api\RewardsRepositoryInterface $rewardsRepository,
        \Magento\Framework\Pricing\Helper\Data $currencyHelper,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->rewardQuoteResource = $rewardQuoteResource;
        $this->configProvider = $configProvider;
        $this->rewardsRepository = $rewardsRepository;
        parent::__construct($context, $data);
        $this->currencyHelper = $currencyHelper;
    }

    /**
     * @return \Magento\Sales\Model\Order\Creditmemo
     */
    public function getCreditmemo()
    {
        return $this->coreRegistry->registry('current_creditmemo');
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getCreditmemo()->getOrder();
    }

    /**
     * Check whether can refund reward points to customer
     *
     * @return bool
     */
    public function canRefundRewardPoints()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getCreditmemo()->getOrder();

        if ($order->getCustomerIsGuest()) {
            return false;
        }

        return true;
    }

    /**
     * Json configuration for tooltip
     *
     * @return string json
     */
    public function getTooltipConfig()
    {
        $tooltipConfig = [
            'tooltip' => [
                'trigger' => '[data-tooltip-trigger=rewards_tooltip]',
                'action' => 'click',
                'delay' => 0,
                'track' => false,
                'position' => 'top'
            ]
        ];

        return str_replace('"', "'", \Zend_Json::encode($tooltipConfig));
    }

    /**
     * @return float
     */
    public function getCustomerRewardsBalance()
    {
        return $this->rewardsRepository->getCustomerRewardBalance($this->getOrder()->getCustomerId());
    }

    /**
     * @return float
     */
    public function getCustomerDeductPoints()
    {
        return $this->currencyHelper->currency($this->getOrder()->getAmEarnRewardPoints(), false, false);
    }

    /**
     * @return float
     */
    public function getRate()
    {
        return $this->configProvider->getPointsRate($this->getCreditmemo()->getStoreId());
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     *
     * @return float
     */
    public function getOrderRewardPoints($order)
    {
        if (!$this->hasData('amrewards_point')) {
            $points = $this->rewardQuoteResource->getUsedRewards($order->getQuoteId());
            $this->setData('amrewards_point', $points);
        }

        return $this->getData('amrewards_point');
    }

    /**
     * @return float
     */
    public function getRefundRewardPointsBalance()
    {
        return $this->getOrderRewardPoints($this->getCreditmemo()->getOrder());
    }
}
