<?php

namespace Laconica\Rewards\Model;

use Amasty\Rewards\Api;
use Amasty\Rewards\Model\ConfigFactory;
use Amasty\Rewards\Model\Date;
use Amasty\Rewards\Model\Quote\RewardsQuoteTune;
use Amasty\Rewards\Model\TransportFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class RewardsProvider extends \Amasty\Rewards\Model\RewardsProvider
{

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     */
    private $customerRepositoryInterface;

    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        Date $date,
        ConfigFactory $rewardsConfigFactory,
        TransportFactory $transportFactory,
        Api\Data\ExpirationArgumentsInterfaceFactory $expirationArgFactory,
        Api\HistoryRepositoryInterface $historyRepository,
        Api\RewardsRepositoryInterface $rewardsRepository,
        Api\ExpirationDateRepositoryInterface $expirationDateRepository,
        RewardsQuoteTune $rewardsQuoteTune
    ) {
        parent::__construct($date, $rewardsConfigFactory, $transportFactory, $expirationArgFactory, $historyRepository, $rewardsRepository, $expirationDateRepository, $rewardsQuoteTune);
        $this->customerRepositoryInterface = $customerRepositoryInterface;
    }

    public function addPoints($amount, $customerId, $action, $comment, $expire)
    {
        if (!$this->checkCustomer($customerId)) {
            return;
        }
        parent::addPoints($amount, $customerId, $action, $comment, $expire);
    }

    public function deductPoints($amount, $customerId, $action, $comment = null)
    {
        if (!$this->checkCustomer($customerId)) {
            return;
        }
        parent::deductPoints($amount, $customerId, $action, $comment);
    }

    /**
     * Additional customer exist check
     */
    private function checkCustomer($customerId)
    {
        if(!$customerId){
            return false;
        }
        try {
            $customer = $this->customerRepositoryInterface->getById($customerId);
            return (!$customer || !$customer->getId()) ? false : true;
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

}