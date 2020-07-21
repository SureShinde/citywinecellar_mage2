<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Block\Adminhtml\Sales;

class OrderCreate extends \Magento\Backend\Block\Widget
{
    /**
     * @var \Magento\Sales\Model\AdminOrder\Create
     */
    private $orderCreate;

    /**
     * @var \Amasty\Rewards\Api\RewardsRepositoryInterface
     */
    private $rewardsRepository;

    /**
     * @var \Amasty\Rewards\Model\Config
     */
    private $configProvider;

    /**
     * @var \Amasty\Rewards\Model\ResourceModel\Quote
     */
    private $rewardQuoteResource;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        \Amasty\Rewards\Api\RewardsRepositoryInterface $rewardsRepository,
        \Amasty\Rewards\Model\Config $configProvider,
        \Amasty\Rewards\Model\ResourceModel\Quote $rewardQuoteResource,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->orderCreate = $orderCreate;
        $this->rewardsRepository = $rewardsRepository;
        $this->configProvider = $configProvider;
        $this->rewardQuoteResource = $rewardQuoteResource;
    }

    /**
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->orderCreate->getQuote();
    }

    /**
     * @return bool
     */
    public function isCanUsePoint()
    {
        return $this->getQuote()->getCustomerId() && $this->getCustomerRewardsBalance();
    }

    /**
     * @return float
     */
    public function getRate()
    {
        return $this->configProvider->getPointsRate($this->getQuote()->getStoreId());
    }

    /**
     * @return float
     */
    public function getCustomerRewardsBalance()
    {
        if (!$this->hasData('amrewards_points_balance')) {
            $points = $this->rewardsRepository->getCustomerRewardBalance($this->getQuote()->getCustomerId());
            $this->setData('amrewards_points_balance', $points);
        }

        return $this->getData('amrewards_points_balance');
    }

    /**
     * @return float
     */
    public function getUsedRewards()
    {
        if (!$this->hasData('amrewards_point')) {
            $points = $this->rewardQuoteResource->getUsedRewards($this->getQuote()->getId());
            $this->setData('amrewards_point', $points);
        }

        return $this->getData('amrewards_point');
    }
}
