<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Block\Frontend;

use Amasty\Rewards\Api\Data\ExpirationDateInterface;
use Amasty\Rewards\Api\ExpirationDateRepositoryInterface;
use Amasty\Rewards\Model\Config;
use Amasty\Rewards\Model\Date;
use Amasty\Rewards\Model\ConstantRegistryInterface;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;

class Index extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var ExpirationDateRepositoryInterface
     */
    private $expirationDateRepository;

    /**
     * @var Date
     */
    private $date;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        Context $context,
        ExpirationDateRepositoryInterface $expirationDateRepository,
        Registry $registry,
        Date $date,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->coreRegistry = $registry;
        $this->expirationDateRepository = $expirationDateRepository;
        $this->date = $date;
        $this->config = $config;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Rewards'));
    }

    /**
     * @return bool|array
     */
    public function getRewardsExpiration()
    {
        if (!($customerId = $this->getCustomerId())) {
            return false;
        }

        try {
            return $this->expirationDateRepository->getByCustomerId($customerId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
            return false;
        }
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * @return mixed
     */
    public function getStatistic()
    {
        return $this->coreRegistry->registry(ConstantRegistryInterface::CUSTOMER_STATISTICS);
    }

    /**
     * @param array $rewardExpiration
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getDate(array $rewardExpiration)
    {
        if (!$date = $rewardExpiration[ExpirationDateInterface::DATE]) {
            return __('Not Expiring');
        }

        $storeCode = $this->_storeManager->getStore()->getCode();

        return $this->date->convertDate($date, $storeCode);
    }

    /**
     * @param array $expirationRow
     *
     * @return \Magento\Framework\Phrase|null
     */
    public function getDeadlineComment($expirationRow)
    {
        if (!$date = $expirationRow[ExpirationDateInterface::DATE]) {
            return null;
        }

        $storeCode = $this->_storeManager->getStore()->getCode();

        return __(
            '<b>%1</b> points will be deducted from your balance on <b>%2</b> because of expiration.',
            $expirationRow[ExpirationDateInterface::AMOUNT],
            $this->date->convertDate($date, $storeCode, \IntlDateFormatter::FULL)
        );
    }

    /**
     * @return string
     */
    public function getDescriptionMessage()
    {
        if (!$this->getData('description_message')) {
            $this->setData(
                'description_message',
                $this->config->getRewardsPointsDescription($this->_storeManager->getStore()->getId())
            );
        }

        return $this->getData('description_message');
    }
}
