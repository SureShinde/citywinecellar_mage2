<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\Grid;

use Magestore\FulfilSuccess\Api\Data\PickRequestInterface;
use Magento\Customer\Ui\Component\DataProvider\Document;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;
use Magestore\FulfilSuccess\Service\Location\LocationServiceInterface;
use Magestore\FulfilSuccess\Service\Locator\BatchServiceInterface;


class RecentCollection extends \Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\Grid\Collection
{

    /**
     * prepare collection
     *
     * @return array
     */
    protected function _initSelect()
    {
        $this->addFilterFieldMap();
        $this->getSelect()->from(['main_table' => $this->getMainTable()]);
        $this->addFieldToFilter(PickRequestInterface::STATUS, PickRequestInterface::STATUS_PICKED);

        $warehouseId = $this->locationService->getCurrentWarehouseId();
        if ($warehouseId) {
            if ($this->fulfilManagement->isMSIEnable()) {
                $this->addFieldToFilter(PickRequestInterface::SOURCE_CODE, $warehouseId);
            } else if ($this->fulfilManagement->isInventorySuccessEnable()) {
                $this->addFieldToFilter(PickRequestInterface::WAREHOUSE_ID, $warehouseId);
            }
        }

        $userId = $this->userService->getCurrentUserId();
        if ($userId) {
            $this->addFieldToFilter(PickRequestInterface::USER_ID, $userId);
        }

        $this->getSelect()->join(
            ['order' => $this->getTable('sales_order_grid')],
            'main_table.order_id = order.entity_id',
            [
                'increment_id',
                'shipping_name',
                'customer_email',
                'purchased_at' => 'order.created_at',
                'base_grand_total',
            ]
        );

        return $this;
    }
}