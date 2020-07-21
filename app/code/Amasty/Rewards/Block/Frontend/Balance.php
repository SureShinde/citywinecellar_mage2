<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Block\Frontend;

use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\SessionFactory as CustomerSessionFactory;
use Amasty\Rewards\Model\Config as ConfigProvider;
use Amasty\Rewards\Api\RewardsRepositoryInterface;

class Balance extends Template
{
    /**
     * @var CustomerSessionFactory
     */
    private $sessionFactory;

    /**
     * @var RewardsRepositoryInterface
     */
    private $rewardsRepository;

    /**
     * @var ConfigProvider
     */
    private $config;

    public function __construct(
        Template\Context $context,
        CustomerSessionFactory $sessionFactory,
        RewardsRepositoryInterface $rewardsRepository,
        ConfigProvider $config,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->sessionFactory = $sessionFactory;
        $this->rewardsRepository = $rewardsRepository;
        $this->config = $config;
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        if (!$this->hasData('customer_id')) {
            $this->setData('customer_id', $this->sessionFactory->create()->getCustomerId());
        }

        return $this->getData('customer_id')
            && $this->config->isEnabled($this->_storeManager->getStore()->getId())
            && $this->config->isRewardsBalanceVisible($this->_storeManager->getStore()->getId());
    }

    /**
     * @return float
     */
    public function getCustomerBalance()
    {
        return $this->rewardsRepository->getCustomerRewardBalance($this->getData('customer_id')) ?: 0;
    }

    /**
     * @return string|null
     */
    public function getBalanceLabel()
    {
        return $this->config->getBalanceLabel($this->_storeManager->getStore()->getId());
    }

    public function getTemplate()
    {
        if (!$this->isVisible()) {
            return '';
        }

        return parent::getTemplate();
    }
}
