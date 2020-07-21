<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Customer\Model\SessionFactory as CustomerSessionFactory;
use Amasty\Rewards\Api\RewardsRepositoryInterface;
use Magento\Framework\DataObject;

class Balance extends DataObject implements SectionSourceInterface
{
    /**
     * @var CustomerSessionFactory
     */
    private $sessionFactory;

    /**
     * @var RewardsRepositoryInterface
     */
    private $rewardsRepository;

    public function __construct(
        CustomerSessionFactory $sessionFactory,
        RewardsRepositoryInterface $rewardsRepository,
        array $data = []
    ) {
        parent::__construct($data);

        $this->sessionFactory = $sessionFactory;
        $this->rewardsRepository = $rewardsRepository;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getSectionData()
    {
        $result = [
            'balance' => 0
        ];
        $customerId = $this->sessionFactory->create()->getCustomerId();

        if ($customerId) {
            $result['balance'] = floatval($this->rewardsRepository->getCustomerRewardBalance($customerId) ?: 0);
        }

        return $result;
    }
}
