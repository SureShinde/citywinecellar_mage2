<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\PickRequestItem;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\FulfilSuccess\Model\PickRequest\PickRequestItem', 'Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\PickRequestItem');
    }
    
    /**
     * Add shelf location data to items
     * 
     * @param int $warehouseId
     */
    public function addShelfLocation($warehouseId)
    {
        $this->_eventManager->dispatch(
                'fulfillsuccess_add_item_shelf_location', 
                ['pick_item_collection' => $this, 'warehouse_id' => $warehouseId]
        );
    }
    
    /**
     * Add warehouse Id to collection
     * 
     * @return \Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\PickRequestItem\Collection
     */
    public function addWarehouseId()
    {
        $this->getSelect()->join(
                ['pickRequest' => $this->getTable('os_fulfilsuccess_pickrequest')],
                ' main_table.pick_request_id = pickRequest.pick_request_id',
                'warehouse_id'
        );
        return $this;
    }
    
    /**
     * 
     * @param array $fields
     * @return \Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\PickRequestItem\Collection
     */
    public function joinPickRequest($fields=[])
    {
        $fields = count($fields) ? $fields : '*';
        $this->getSelect()->join(
                ['pickRequest' => $this->getTable('os_fulfilsuccess_pickrequest')],
                ' main_table.pick_request_id = pickRequest.pick_request_id',
                $fields
        );
        return $this;        
    }

}
