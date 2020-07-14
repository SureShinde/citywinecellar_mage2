<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\ResourceModel\Sales\Order;

use \Magestore\Webpos\Api\Data\Checkout\OrderInterface;

/**
 * Class Collection
 * @package Magestore\Webpos\Model\ResourceModel\Sales\Order
 */
class Collection extends \Magento\Sales\Model\ResourceModel\Order\Collection
{
    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Magestore\Webpos\Model\Checkout\Order::class,
            \Magento\Sales\Model\ResourceModel\Order::class
        );
    }

    /**
     * joint to sales_item and sales_address table
     */
    public function joinToGetSearchString($queryString)
    {
        $searchString = new \Zend_Db_Expr(
            "CONCAT(
                            GROUP_CONCAT(DISTINCT main_table.increment_id SEPARATOR ', '),
                            GROUP_CONCAT(DISTINCT coalesce(order_address.telephone,'') SEPARATOR ', '),
                            GROUP_CONCAT(DISTINCT main_table.customer_email SEPARATOR ', '),
                            GROUP_CONCAT(DISTINCT coalesce(order_address.middlename,'') SEPARATOR ', '),
                            GROUP_CONCAT(IFNULL(main_table.payment_reference_number, '')  SEPARATOR ', '),
                            GROUP_CONCAT(
                                IFNULL( CONCAT(order_address.firstname, ' ', order_address.lastname) , '')  
                                SEPARATOR ', '
                            ),
                            GROUP_CONCAT(
                                IFNULL( CONCAT(order_address.lastname, ' ', order_address.firstname) , '')  
                                SEPARATOR ', '
                            )
            )");
        $this->getSelect()
            ->join(array('order_address' => $this->getTable('sales_order_address')),
                'main_table.entity_id = order_address.parent_id',
                array());
        $this->getSelect()->group('main_table.entity_id');
        $this->getSelect()->having($searchString . ' like "' . $queryString . '"');
    }

    /**
     * get All Store Ids  which is same Current Stock linked current location
     * @return array
     */
    public function getAllStoreIdsSameCurrentStock()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface $stockManagement */
        $stockManagement = $objectManager->get('Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface');
        $stockId = $stockManagement->getStockId();
        /** @var \Magento\InventorySales\Model\ResourceModel\GetAssignedSalesChannelsDataForStock $getAssignedSalesChannelsDataForStock */
        $getAssignedSalesChannelsDataForStock =
            $objectManager->get('Magento\InventorySales\Model\ResourceModel\GetAssignedSalesChannelsDataForStock');
        $salesChannelsData = $getAssignedSalesChannelsDataForStock->execute($stockId);

        $websiteCodes = [];
        foreach ($salesChannelsData as $salesChannelData) {
            if (
                $salesChannelData[\Magento\InventorySalesApi\Api\Data\SalesChannelInterface::TYPE]
                !== \Magento\InventorySalesApi\Api\Data\SalesChannelInterface::TYPE_WEBSITE
            ) {
                continue;
            }

            $websiteCodes[] = $salesChannelData[\Magento\InventorySalesApi\Api\Data\SalesChannelInterface::CODE];
        }

        if (empty($websiteCodes)) {
            return [];
        }

        /** @var \Magento\Store\Api\WebsiteRepositoryInterface $websiteRepository */
        $websiteRepository = $objectManager->get('Magento\Store\Api\WebsiteRepositoryInterface');
        /** @var \Magento\Store\Api\StoreRepositoryInterface $storeRepository */
        $storeRepository = $objectManager->get('Magento\Store\Api\StoreRepositoryInterface');

        $websiteIds = [];
        foreach ($websiteRepository->getList() as $website) {
            if (!in_array($website->getCode(), $websiteCodes)) {
                continue;
            }
            $websiteIds[] = $website->getId();
        }

        if (empty($websiteCodes)) {
            return [];
        }

        $storeIds = [];
        foreach ($storeRepository->getList() as $store) {
            if (!in_array($store->getWebsiteId(), $websiteIds)) {
                continue;
            }
            $storeIds[] = $store->getId();
        }

        return $storeIds;
    }

    /**
     *  return order out of current stock
     *  integrate MSI
     */
    public function ignoreByCurrentStockAndSource()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magestore\Webpos\Api\WebposManagementInterface $webposManagement */
        $webposManagement = $objectManager->get('Magestore\Webpos\Api\WebposManagementInterface');
        /** @var \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface $stockManagement */
        $stockManagement = $objectManager->get('Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface');

        if ($stockId = $stockManagement->getStockId()) {
            if ($webposManagement->isWebposStandard()) {
                $storeIds = $this->getAllStoreIdsSameCurrentStock();
                $locationIds = $objectManager->get('Magestore\Webpos\Api\Location\LocationRepositoryInterface')
                    ->getLocationIdsByStockId($stockId);
                $this->getSelect()->where('main_table.store_id NOT IN (?) ', $storeIds)
                    ->where('main_table.pos_location_id NOT IN (?)', $locationIds);
            }
        }
    }


    /**
     *
     * override cause need left join
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        $countSelect->reset(\Magento\Framework\DB\Select::ORDER);
        $countSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $countSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $countSelect->reset(\Magento\Framework\DB\Select::COLUMNS);

        $part = $this->getSelect()->getPart(\Magento\Framework\DB\Select::GROUP);
        if (!is_array($part) || !count($part)) {
            $countSelect->columns(new \Zend_Db_Expr('COUNT(*)'));
            return $countSelect;
        }

        $countSelect->reset(\Magento\Framework\DB\Select::GROUP);
        $group = $this->getSelect()->getPart(\Magento\Framework\DB\Select::GROUP);
        $countSelect->columns(new \Zend_Db_Expr(("COUNT(DISTINCT " . implode(", ", $group) . ")")));
        return $countSelect;
    }

}
