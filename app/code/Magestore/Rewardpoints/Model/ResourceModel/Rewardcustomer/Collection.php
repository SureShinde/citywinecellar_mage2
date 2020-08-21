<?php namespace Magestore\Rewardpoints\Model\ResourceModel\Rewardcustomer;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

/**
 * Flat customer online grid collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends SearchResult
{
    /**
     * Init collection select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this
            ->getSelect()
            ->joinLeft(
                ['customer_reward' => $this->getTable('rewardpoints_customer')],
                'main_table.entity_id = customer_reward.customer_id',
                ['point_balance']
            );
        $this
            ->getSelect()
            ->columns(['point_balance' => "IF(customer_reward.point_balance,customer_reward.point_balance,0)"]);
        return $this;
    }

    /**
     * Change the data return when exporting reports
     *
     * @return $data
     */
    public function getData()
    {
        $data = parent::getData();
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $groupRepository  = $om->get(\Magento\Customer\Api\GroupRepositoryInterface::class);
        $websiteRepository  = $om->get(\Magento\Store\Api\WebsiteRepositoryInterface::class);
        $requestInterface = $om->get(\Magento\Framework\App\RequestInterface::class);
        $metadataProvider = $om->get(\Magento\Ui\Model\Export\MetadataProvider::class);
        if (!method_exists($metadataProvider, 'getColumnOptions')) {
            if (($requestInterface->getActionName() == 'gridToCsv') ||
                ($requestInterface->getActionName() == 'gridToXml')
            ) {
                foreach ($data as &$item) {
                    $item['group_id'] = $groupRepository->getById((int)$item['group_id'])->getCode();
                    $item['website_id'] = $websiteRepository->getById((int)$item['website_id'])->getName();
                }
            }
        }
        return $data;
    }
}
