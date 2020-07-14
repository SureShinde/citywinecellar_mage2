<?php
namespace Magestore\Rewardpoints\Plugin\Integration;
/**
 * Class LoyaltyRepository
 * @package Magestore\Rewardpoints\Plugin\Integration
 */
class LoyaltyRepository {

    /**
     * @var \Magento\Framework\Api\SearchCriteriaInterface
     */
    protected $searchCriteria;

    /**
     * @var \Magento\Customer\Api\Data\CustomerExtensionInterfaceFactory
     */
    protected $customerExtension;
    /**
     * @var \Magestore\Rewardpoints\Model\CustomerFactory
     */
    protected $rewardCustomerFactory;

    /**
     * CustomerRepository constructor.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param \Magento\Customer\Api\Data\CustomerExtensionInterfaceFactory $customerExtension
     * @param \Magestore\Rewardpoints\Model\CustomerFactory $rewardCustomerFactory
     */
    public function __construct(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria,
        \Magento\Customer\Api\Data\CustomerExtensionInterfaceFactory $customerExtension,
        \Magestore\Rewardpoints\Model\CustomerFactory $rewardCustomerFactory
    )
    {
        $this->searchCriteria = $searchCriteria;
        $this->customerExtension = $customerExtension;
        $this->rewardCustomerFactory = $rewardCustomerFactory;
    }

    /**
     * @param \Magestore\Webpos\Model\Integration\LoyaltyRepository $subject
     * @param \Magestore\Webpos\Api\Data\Customer\CustomerSearchResultsInterface $result
     * @return \Magestore\Webpos\Api\Data\Customer\CustomerSearchResultsInterface
     */
    public function afterGetList(\Magestore\Webpos\Model\Integration\LoyaltyRepository $subject, $result)
    {
        return $this->processSearchResultsData($result);
    }

    /**
     * @param \Magestore\Webpos\Api\Data\Customer\CustomerSearchResultsInterface $result
     * @return \Magestore\Webpos\Api\Data\Customer\CustomerSearchResultsInterface
     */
    public function processSearchResultsData($result) {
        $items = $result->getItems();

        foreach ($items as &$item) {
            $extensionAttributes = $item->getExtensionAttributes();
            if (!$extensionAttributes){
                $extensionAttributes = $this->customerExtension->create();
            }

            $extensionAttributes->setPointBalance($this->getCustomerPointBalance($item));
            $item->setExtensionAttributes($extensionAttributes);
        }

        return $result->setItems($items);
    }

    /**
     * @param \Magestore\Webpos\Api\Data\Customer\CustomerInterface $item
     * @return float
     */
    public function getCustomerPointBalance($item) {
        $customerRewardpoint = $this->rewardCustomerFactory->create();
        $rewardPoint = $customerRewardpoint->load($item->getId(), 'customer_id');
        if ($rewardPoint->getId()) {
            $pointBalance = $rewardPoint->getPointBalance();
            return $pointBalance;
        }

        return 0;
    }

}
