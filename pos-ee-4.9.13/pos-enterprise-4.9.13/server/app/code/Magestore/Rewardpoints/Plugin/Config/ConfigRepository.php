<?php
namespace Magestore\Rewardpoints\Plugin\Config;
/**
 * Class Config
 * @package Magestore\Rewardpoints\Plugin\Config
 */
class ConfigRepository {

    /**
     * @var \Magento\Framework\Api\SearchCriteriaInterface
     */
    protected $searchCriteria;

    /**
     * @var \Magestore\Rewardpoints\Model\ResourceModel\Rate\CollectionFactory
     */
    protected $collectionFactory;

    protected $configExtension;

    /**
     * Config constructor.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param \Magestore\Rewardpoints\Model\ResourceModel\Rate\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria,
        \Magestore\Rewardpoints\Model\ResourceModel\Rate\CollectionFactory $collectionFactory,
        \Magestore\Webpos\Api\Data\Config\ConfigExtensionInterfaceFactory $configExtension
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->searchCriteria = $searchCriteria;
        $this->configExtension = $configExtension;
    }

    /**
     * @param \Magestore\Webpos\Api\Config\ConfigRepositoryInterface $subject
     * @param $result
     */
    public function afterGetAllConfig(\Magestore\Webpos\Api\Config\ConfigRepositoryInterface $subject, $result)
    {
        $extensionAttributes = $result->getExtensionAttributes();
        if (!$extensionAttributes){
            $extensionAttributes = $this->configExtension->create();
        }

        $extensionAttributes->setRewardpointsRate($this->getRewardpointsRate());
        $result->setExtensionAttributes($extensionAttributes);
        return $result;
    }

    /**
     * @param \Magestore\Webpos\Api\Config\ConfigRepositoryInterface $subject
     * @param $result
     */
    public function afterGetConfigPath(\Magestore\Webpos\Api\Config\ConfigRepositoryInterface $subject, $result)
    {
        $rewardPath = [
            'rewardpoints/general/enable',
            'rewardpoints/general/point_name',
            'rewardpoints/general/point_names',
            'rewardpoints/earning/rounding_method',
            'rewardpoints/earning/max_balance',
            'rewardpoints/earning/by_tax',
            'rewardpoints/earning/by_shipping',
            'rewardpoints/earning/earn_when_spend',
            'rewardpoints/earning/order_invoice',
            'rewardpoints/earning/holding_days',
            'rewardpoints/spending/redeemable_points',
            'rewardpoints/spending/max_points_per_order',
            'rewardpoints/spending/max_point_default',
            'rewardpoints/spending/spend_for_shipping',
        ];
        $newResult = array_merge($result, $rewardPath);
        return $newResult;
    }

    /**
     * @return array
     */
    public function getRewardpointsRate()
    {
        $rates = [];

        $searchCriteria = $this->searchCriteria;
        $searchCriteria->setFilterGroups([]);
        $searchCriteria->setSortOrders([]);
        $searchCriteria->setCurrentPage('');
        $searchCriteria->setPageSize('');

        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('status', 1);
        $collection->setOrder('sort_order', 'ASC');

        foreach ($collection as $rate) {
            $rates[] = $rate->getData();
        }
        return $rates;
    }
}