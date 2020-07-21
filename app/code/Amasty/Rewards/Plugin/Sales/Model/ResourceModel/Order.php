<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Plugin\Sales\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Sales\Model\Order as SalesOrder;
use Magento\Sales\Model\ResourceModel\Order as SalesOrderResource;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Order
{
    /**
     * For earn rewards
     */
    const NOT_AVAILABLE_ACTION = 'addComment';

    /**
     * @var SalesOrder
     */
    private $orderModel;

    /**
     * @var \Amasty\Rewards\Model\Config
     */
    private $rewardsConfig;

    /**
     * @var \Amasty\Rewards\Model\CalculationFactory
     */
    private $calculationFactory;

    /**
     * @var \Amasty\Rewards\Api\RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var \Amasty\Rewards\Model\ResourceModel\Quote
     */
    private $rewardQuoteResource;

    /**
     * @var \Amasty\Rewards\Api\RewardsProviderInterface
     */
    private $rewardsProvider;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory
     */
    private $quoteCollectionFactory;

    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    private $orderRepository;

    public function __construct(
        \Amasty\Rewards\Model\Config $rewardsConfig,
        \Amasty\Rewards\Model\CalculationFactory $calculationFactory,
        \Amasty\Rewards\Api\RuleRepositoryInterface $ruleRepository,
        \Amasty\Rewards\Model\ResourceModel\Quote $rewardQuoteResource,
        \Amasty\Rewards\Api\RewardsProviderInterface $rewardsProvider,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory,
        \Magento\Sales\Model\OrderRepository $orderRepository
    ) {
        $this->rewardsConfig = $rewardsConfig;
        $this->calculationFactory = $calculationFactory;
        $this->ruleRepository = $ruleRepository;
        $this->rewardQuoteResource = $rewardQuoteResource;
        $this->rewardsProvider = $rewardsProvider;
        $this->request = $request;
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param SalesOrderResource $subject
     * @param SalesOrderResource $result
     *
     * @return SalesOrderResource
     */
    public function afterSave(SalesOrderResource $subject, $result, AbstractModel $model)
    {
        if ($this->request->getParam('creditmemo')
            || !$this->rewardsConfig->isEnabled()
            || !$this->canEarn($model)
            || $model->getAmEarnRewardPoints() !== null
        ) {
            return $result;
        }

        $this->orderModel = $model;
        $store = $this->orderModel->getStore();
        $orderRules = $this->getOrderRules($store);
        $spentRules = $this->getSpentRules($store);
        $address = $this->getAddress();
        if ($orderRules || $spentRules) {
            $paymentMethod = $address->getPaymentMethod();
            $address->setPaymentMethod($this->orderModel->getPayment()->getMethod());
            $this->actionsCombine($spentRules, $orderRules, $store, $address);
            if ($paymentMethod) {
                $address->setPaymentMethod($paymentMethod);
            }
            if ($this->orderModel->getAmEarnRewardPoints() !== null) {
                $this->orderRepository->save($this->orderModel);
            }
        }

        return $result;
    }

    /**
     * @param $store
     *
     * @return \Amasty\Rewards\Api\Data\RuleInterface[]
     */
    private function getSpentRules($store)
    {
        return $this->ruleRepository->getRulesByAction(
            \Amasty\Rewards\Helper\Data::MONEY_SPENT_ACTION,
            $store->getWebsiteId(),
            $this->orderModel->getCustomerGroupId()
        );
    }

    /**
     * @param $store
     *
     * @return \Amasty\Rewards\Api\Data\RuleInterface[]
     */
    private function getOrderRules($store)
    {
        return $this->ruleRepository->getRulesByAction(
            \Amasty\Rewards\Helper\Data::ORDER_COMPLETED_ACTION,
            $store->getWebsiteId(),
            $this->orderModel->getCustomerGroupId()
        );
    }

    /**
     * @param array $spentRules
     * @param array $orderRules
     * @param \Magento\Store\Model\Store $store
     * @param \Magento\Quote\Model\Quote\Address $address
     */
    private function actionsCombine($spentRules, $orderRules, $store, $address)
    {
        $this->actionByOrderRules($orderRules, $store, $address);
        $this->actionsBySpentRules($spentRules, $store, $address);
    }

    /**
     * @param array $orderRules
     * @param \Magento\Store\Model\Store $store
     * @param \Magento\Quote\Model\Quote\Address $address
     */
    private function actionByOrderRules($orderRules, $store, $address)
    {
        /** @var $rule \Amasty\Rewards\Model\Rule */
        foreach ($orderRules as $rule) {
            if ($rule->validate($address)) {
                $currentPoints = $this->orderModel->getAmEarnRewardPoints();
                $this->orderModel->setAmEarnRewardPoints($currentPoints + $rule->getAmount());
                $this->rewardsProvider->addPointsByRule(
                    $rule,
                    $this->orderModel->getCustomerId(),
                    $store->getStoreId()
                );
            }
        }
    }

    /**
     * @param array $spentRules
     * @param \Magento\Store\Model\Store $store
     * @param \Magento\Quote\Model\Quote\Address $address
     */
    private function actionsBySpentRules($spentRules, $store, $address)
    {
        /** @var $rule \Amasty\Rewards\Model\Rule */
        foreach ($spentRules as $rule) {
            if ($rule->validate($address)) {
                /** @var \Amasty\Rewards\Model\Calculation $calculation */
                $calculation = $this->calculationFactory->create();

                if ($amount = $calculation->calculateSpentReward($address, $rule)) {
                    $currentPoints = $this->orderModel->getAmEarnRewardPoints();
                    $this->orderModel->setAmEarnRewardPoints($currentPoints + $amount);
                    $this->rewardsProvider->addPointsByRule(
                        $rule,
                        $this->orderModel->getCustomerId(),
                        $store->getStoreId(),
                        $amount
                    );
                }
            }
        }
    }

    /**
     * Return true if can earn rewards by MONEY_SPENT_ACTION or ORDER_COMPLETED_ACTION actions
     *
     * @param SalesOrder $orderModel
     *
     * @return bool
     */
    private function canEarn(SalesOrder $orderModel)
    {
        return $orderModel->getCustomerId() && $orderModel->getState() === SalesOrder::STATE_COMPLETE
            && $this->request->getActionName() != self::NOT_AVAILABLE_ACTION
            && !($this->rewardsConfig->isDisabledEarningByRewards($orderModel->getStoreId())
                && $this->rewardQuoteResource->getUsedRewards($orderModel->getQuoteId()));
    }

    /**
     * IMPORTANT: Do not use @see \Magento\Quote\Api\CartRepositoryInterface because
     * cart repository can load only quotes from current store
     *
     * @return \Magento\Quote\Model\Quote\Address
     */
    private function getAddress()
    {
        /** @var \Magento\Quote\Model\ResourceModel\Quote\Collection $collection */
        $collection = $this->quoteCollectionFactory->create();

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $collection
            ->addFieldToFilter('entity_id', $this->orderModel->getQuoteId())
            ->setPageSize(1)->getFirstItem();

        if ($quote->isVirtual()) {
            $address = $quote->getBillingAddress();
        } else {
            $address = $quote->getShippingAddress();
        }

        return $address;
    }
}
