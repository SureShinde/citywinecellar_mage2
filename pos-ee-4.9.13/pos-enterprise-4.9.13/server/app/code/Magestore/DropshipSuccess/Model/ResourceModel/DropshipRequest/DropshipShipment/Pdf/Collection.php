<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\DropshipShipment\Pdf;

/**
 * Class Collection
 * @package Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\DropshipShipment\Pdf
 */
class Collection extends \Magento\Sales\Model\ResourceModel\Order\Shipment\Collection
{
    /**
     * @return $this
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(['main_table' => $this->getMainTable()]);
        $this->getSelect()->joinLeft(
            ['dropship_request' => $this->getTable('os_dropship_shipment')],
            'main_table.entity_id = dropship_request.shipment_id',
            [
                'id' => 'dropship_request.dropship_request_id'
            ]
        );
        return $this;
    }

    /**
     * rewrite add field to filters from collection
     *
     * @return array
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'id') {
            $field = 'dropship_request.dropship_request_id';
        }
        if ($field == 'created_at') {
            $field = 'main_table.created_at';
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * filter by requestId
     *
     * @return array
     */
    public function filterByRequestId($requestId)
    {
        return $this->addFieldToFilter('id', $requestId);
    }


}
