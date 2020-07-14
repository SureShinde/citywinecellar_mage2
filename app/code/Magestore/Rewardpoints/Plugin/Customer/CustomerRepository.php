<?php
namespace Magestore\Rewardpoints\Plugin\Customer;
/**
 * Class CustomerRepository
 * @package Magestore\Rewardpoints\Plugin\Customer
 */
class CustomerRepository {

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
     * @param \Magestore\Webpos\Api\Customer\CustomerRepositoryInterface $subject
     * @param \Magestore\Webpos\Api\Data\Customer\CustomerInterface $result
     * @return \Magestore\Webpos\Api\Data\Customer\CustomerInterface
     */
    public function afterSave(\Magestore\Webpos\Api\Customer\CustomerRepositoryInterface $subject, $result) {
        return $this->processDataResult($result);
    }

    /**
     * @param \Magestore\Webpos\Api\Customer\CustomerRepositoryInterface $subject
     * @param \Magestore\Webpos\Api\Data\Customer\CustomerSearchResultsInterface $result
     * @return \Magestore\Webpos\Api\Data\Customer\CustomerSearchResultsInterface|[]
     */
    public function afterGetList(\Magestore\Webpos\Api\Customer\CustomerRepositoryInterface $subject, $result)
    {
        return $this->processSearchResultsData($result);
    }

    /**
     * @param \Magestore\Webpos\Api\Customer\CustomerRepositoryInterface $subject
     * @param \Magestore\Webpos\Api\Data\Customer\CustomerSearchResultsInterface $result
     * @return \Magestore\Webpos\Api\Data\Customer\CustomerSearchResultsInterface|[]
     */
    public function afterSearch(\Magestore\Webpos\Api\Customer\CustomerRepositoryInterface $subject, $result) {
        return $this->processSearchResultsData($result);
    }

    /**
     * @param \Magestore\Webpos\Api\Customer\CustomerRepositoryInterface $subject
     * @param \Magestore\Webpos\Api\Data\Customer\CustomerInterface $result
     * @return \Magestore\Webpos\Api\Data\Customer\CustomerInterface
     */
    public function afterGetById(\Magestore\Webpos\Api\Customer\CustomerRepositoryInterface $subject, $result) {
        return $this->processDataResult($result);
    }

    /**
     * @param \Magestore\Webpos\Api\Data\Customer\CustomerSearchResultsInterface|[] $result
     * @return \Magestore\Webpos\Api\Data\Customer\CustomerSearchResultsInterface|[]
     */
    public function processSearchResultsData($result) {
        if(is_array($result) && isset($result['cached_at'])) {
            $items = $result['items'];

            foreach ($items as &$item) {
                $extensionAttributes = isset($item['extension_attributes']) ? $item['extension_attributes'] : [];

                $extensionAttributes['point_balance'] = $this->getCustomerPointBalance($item);
                $item['extension_attributes'] = $extensionAttributes;
            }

            $result['items'] = $items;
            return $result;
        } else {
            $items = $result->getItems();

            foreach ($items as &$item) {
                $extensionAttributes = $item->getExtensionAttributes();
                if (!$extensionAttributes) {
                    $extensionAttributes = $this->customerExtension->create();
                }

                $extensionAttributes->setPointBalance($this->getCustomerPointBalance($item));
                $item->setExtensionAttributes($extensionAttributes);
            }

            return $result->setItems($items);
        }
    }

    /**
     * @param \Magestore\Webpos\Api\Data\Customer\CustomerInterface $result
     * @return \Magestore\Webpos\Api\Data\Customer\CustomerInterface
     */
    public function processDataResult($result) {
        $extensionAttributes = $result->getExtensionAttributes();
        if (!$extensionAttributes){
            $extensionAttributes = $this->customerExtension->create();
        }

        $extensionAttributes->setPointBalance($this->getCustomerPointBalance($result));
        $result->setExtensionAttributes($extensionAttributes);

        return $result;
    }

    /**
     * @param \Magestore\Webpos\Api\Data\Customer\CustomerInterface $item
     * @return float
     */
    public function getCustomerPointBalance($item) {
        if(is_array($item)) {
            $id = $item['id'];
        } else {
            $id = $item->getId();
        }
        $customerRewardpoint = $this->rewardCustomerFactory->create();
        $rewardPoint = $customerRewardpoint->load($id, 'customer_id');
        if ($rewardPoint->getId()) {
            $pointBalance = $rewardPoint->getPointBalance();
            return $pointBalance;
        }

        return 0;
    }
}
