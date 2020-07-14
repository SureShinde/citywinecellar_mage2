<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\Grid;

use Magestore\FulfilSuccess\Api\Data\PackRequestInterface;
use Magento\Customer\Ui\Component\DataProvider\Document;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;
use Magestore\FulfilSuccess\Service\Location\LocationServiceInterface;

class RecentCollection extends \Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\Grid\Collection
{

    /**
     * prepare collection
     *
     * @return array
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(['main_table' => $this->getMainTable()]);
        $this->addFilterFieldMap();
        $this->addFieldToFilter(
            PackRequestInterface::STATUS,
            ['in' => [PackRequestInterface::STATUS_PACKED, PackRequestInterface::STATUS_CANCELED]]
        );
        $warehouseId = $this->locationService->getCurrentWarehouseId();
        if ($warehouseId) {
            if ($this->fulfilManagement->isMSIEnable()) {
                $this->addFieldToFilter(PackRequestInterface::SOURCE_CODE, $warehouseId);
            } else if ($this->fulfilManagement->isInventorySuccessEnable()) {
                $this->addFieldToFilter(PackRequestInterface::WAREHOUSE_ID, $warehouseId);
            }
        }

        $userId = $this->userService->getCurrentUserId();
        if ($userId) {
            $this->addFieldToFilter(PackRequestInterface::USER_ID, $userId);
        }

        $this->getSelect()->join(['order' => $this->getTable('sales_order_grid')],
            'main_table.order_id = order.entity_id',
            [
                'increment_id',
                'shipping_name',
                'customer_email',
                'purchased_at' => 'order.created_at',
                'base_grand_total',
            ]);

        return $this;
    }
}