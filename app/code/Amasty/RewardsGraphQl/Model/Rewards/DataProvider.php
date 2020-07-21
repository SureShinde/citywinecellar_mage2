<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_RewardsGraphQl
 */


namespace Amasty\RewardsGraphQl\Model\Rewards;

use Amasty\Rewards\Api\RewardsRepositoryInterface;
use Amasty\RewardsGraphQl\Model\ResourceModel\Rewards\Collection;
use Amasty\RewardsGraphQl\Model\ResourceModel\Rewards\CollectionFactory;

class DataProvider
{
    /**
     * @var RewardsRepositoryInterface
     */
    private $rewardsRepository;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        RewardsRepositoryInterface $rewardsRepository,
        CollectionFactory $collectionFactory
    ) {
        $this->rewardsRepository = $rewardsRepository;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param int $customerId
     *
     * @return float
     */
    public function getCustomerBalance(int $customerId): float
    {
        return floatval($this->rewardsRepository->getCustomerRewardBalance($customerId));
    }

    /**
     * @param int $customerId
     * @param int $limit
     * @param int $page
     *
     * @return \Magento\Framework\DataObject[]
     */
    public function getHistoryItems(int $customerId, int $limit, int $page)
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addExpirationDate()
            ->addCustomerIdFilter($customerId)
            ->setPageSize($limit)
            ->setCurPage($page);

        return $collection->getItems();
    }

    /**
     * @param int $customerId
     *
     * @return int
     */
    public function getTotalHistoryRecordsCount(int $customerId): int
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addCustomerIdFilter($customerId);

        return $collection->getSize();
    }
}
