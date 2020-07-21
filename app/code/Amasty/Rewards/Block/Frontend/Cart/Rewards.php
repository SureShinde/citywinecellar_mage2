<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Block\Frontend\Cart;

/**
 * Product View block
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Rewards extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Amasty\Rewards\Helper\Data
     */
    private $helper;

    /**
     * @var array
     */
    private $rewardsData;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Amasty\Rewards\Helper\Data $helper,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        array $data
    ) {
        $this->helper = $helper;
        parent::__construct($context, $data);
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * Retrieve customer data object
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->getRewardsData()['customerId'];
    }

    /**
     * @return mixed
     */
    public function getPoints()
    {
        return $this->priceCurrency->round($this->getRewardsData()['pointsLeft']);
    }

    /**
     * @return mixed
     */
    public function getUsedPoints()
    {
        return $this->priceCurrency->round($this->getRewardsData()['pointsUsed']);
    }

    /**
     * @return float
     */
    public function getPointsRate()
    {
        return $this->getRewardsData()['pointsRate'];
    }

    /**
     * @return mixed
     */
    public function getCurrentCurrencyCode()
    {
        $currentCurrency = $this->_storeManager->getStore()->getCurrentCurrency();

        return $currentCurrency->getCurrencyCode();
    }

    /**
     * @return int
     */
    public function getRateForCurrency()
    {
        return $this->getRewardsData()['rateForCurrency'];
    }

    /**
     * @return array
     */
    private function getRewardsData()
    {
        if (!isset($this->rewardsData)) {
            $this->rewardsData = $this->helper->getRewardsData();
        }

        return $this->rewardsData;
    }

    /**
     * @return int
     */
    public function getMinimumRewardsBalance()
    {
        $storeId = $this->_storeManager->getStore()->getId();

        return $this->priceCurrency->round($this->helper->getMinimumPointsValue($storeId));
    }
}
