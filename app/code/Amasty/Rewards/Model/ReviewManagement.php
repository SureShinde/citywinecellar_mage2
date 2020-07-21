<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Model;

use Amasty\Rewards\Api\HistoryRepositoryInterface;
use Amasty\Rewards\Api\RewardsProviderInterface;
use Amasty\Rewards\Api\RuleRepositoryInterface;
use Amasty\Rewards\Helper\Data;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Review\Model\ResourceModel\Review;
use Magento\Review\Model\ReviewFactory;

class ReviewManagement
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var Review
     */
    private $resourceReview;

    /**
     * @var ReviewFactory
     */
    private $reviewFactory;

    /**
     * @var RewardsProviderInterface
     */
    private $rewardsProvider;

    /**
     * @var HistoryRepositoryInterface
     */
    private $historyRepository;

    /**
     * @var Config
     */
    private $configProvider;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        RewardsProviderInterface $rewardsProvider,
        RuleRepositoryInterface $ruleRepository,
        HistoryRepositoryInterface $historyRepository,
        ReviewFactory $reviewFactory,
        Review $resourceReview,
        Config $configProvider
    ) {
        $this->customerRepository = $customerRepository;
        $this->rewardsProvider = $rewardsProvider;
        $this->ruleRepository = $ruleRepository;
        $this->historyRepository = $historyRepository;
        $this->reviewFactory = $reviewFactory;
        $this->resourceReview = $resourceReview;
        $this->configProvider = $configProvider;
    }

    /**
     * @param int $reviewId
     */
    public function addReviewPoints($reviewId)
    {
        if (!$this->configProvider->isEnabled()) {
            return;
        }
        /** @var \Magento\Review\Model\Review $review */
        $review = $this->reviewFactory->create();
        $this->resourceReview->load($review, $reviewId);
        $customerId = $review->getCustomerId();

        if ($customerId) {
            /** @var \Magento\Customer\Model\Data\Customer $customer */
            $customer = $this->customerRepository->getById($customerId);

            $rules = $this->ruleRepository->getRulesByAction(
                Data::REVIEW_ACTION,
                $customer->getWebsiteId(),
                $customer->getGroupId()
            );

            foreach ($rules as $rule) {
                if ($this->historyRepository->getLastActionByCustomerId($customerId, $rule->getRuleId(), $reviewId)
                    ->getId()
                ) {
                    continue;
                }

                $rule->setParams($reviewId);
                $this->rewardsProvider->addPointsByRule($rule, $customerId, $review->getStoreId());
            }
        }
    }
}
