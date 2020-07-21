<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Cron;

use Amasty\Rewards\Api\Data\ExpirationDateInterface;
use Amasty\Rewards\Api\RewardsProviderInterface;
use Amasty\Rewards\Model\ConfigFactory;
use Amasty\Rewards\Model\ConstantRegistryInterface as Constant;
use Amasty\Rewards\Model\Date;
use Amasty\Rewards\Model\ResourceModel\Expiration;
use Amasty\Rewards\Helper\Data as Helper;
use Amasty\Rewards\Model\TransportFactory;

class ExpirationPoints
{
    /**
     * @var Date
     */
    private $date;

    /**
     * @var ConfigFactory
     */
    private $configFactory;

    /**
     * @var Expiration
     */
    private $expirationResource;

    /**
     * @var TransportFactory
     */
    private $transportFactory;

    /**
     * @var RewardsProviderInterface
     */
    private $rewardsProvider;

    public function __construct(
        Date $date,
        ConfigFactory $configFactory,
        Expiration $expirationResource,
        TransportFactory $transportFactory,
        RewardsProviderInterface $rewardsProvider
    ) {
        $this->date = $date;
        $this->configFactory = $configFactory;
        $this->expirationResource = $expirationResource;
        $this->transportFactory = $transportFactory;
        $this->rewardsProvider = $rewardsProvider;
    }

    public function execute(\Magento\Cron\Model\Schedule $schedule)
    {
        $this->deductExpiredPoints();
        $this->checkExpiration();
    }

    /**
     * Deduct expired points from customer rewards balance
     */
    private function deductExpiredPoints()
    {
        $rows = $this->expirationResource->getSumExpirationToDate($this->date->getDateWithOffsetByDays(0));

        foreach ($rows as $row) {
            if (!empty($row['amount'])) {
                $this->rewardsProvider->deductPoints(
                    $row['amount'],
                    $row['customer_id'],
                    Helper::REWARDS_EXPIRED_ACTION
                );
            }
        }
    }

    /**
     * Check expiration date by subscribed customers and stores
     */
    private function checkExpiration()
    {
        $customers = $this->expirationResource->getAllSubscribedCustomers();

        if (!$customers) {
            return;
        }

        $rewardConfig = $this->configFactory->create();
        $storeDays = [];
        $selectedCustomers = [];
        $maxDay = 0;

        foreach ($customers as $customer) {
            if (isset($storeDays[$customer[Constant::STORE_ID]])) {
                $selectedCustomers[] = $customer[ExpirationDateInterface::CUSTOMER_ID];
            } elseif ($rewardConfig->getSendExpireNotification($customer[Constant::STORE_ID])
            ) {
                $storeDays[$customer[Constant::STORE_ID]] =
                    $rewardConfig->getExpireDaysToSend($customer[Constant::STORE_ID]);
                $selectedCustomers[] = $customer[ExpirationDateInterface::CUSTOMER_ID];

                if ($maxDay < $storeDays[$customer[Constant::STORE_ID]]) {
                    $maxDay = $storeDays[$customer[Constant::STORE_ID]];
                }
            }
        }

        $expirationRows = $this->expirationResource->getFilteredRows(
            $this->date->getDateWithOffsetByDays(0),
            $this->date->getDateWithOffsetByDays($maxDay),
            $selectedCustomers
        );

        if (!$expirationRows) {
            return;
        }

        $rowsByCustomer = [];

        foreach ($expirationRows as &$expirationRow) {
            $rowsByCustomer[$expirationRow[ExpirationDateInterface::CUSTOMER_ID]][] = $expirationRow;
        }

        $transport = $this->transportFactory->create();

        foreach ($rowsByCustomer as $rowByCustomer) {
            $day = isset($storeDays[current($rowByCustomer)[Constant::STORE_ID]])
                ? $storeDays[current($rowByCustomer)[Constant::STORE_ID]] : 0;
            $transport->sendExpireNotification($rowByCustomer, $day);
        }
    }
}
