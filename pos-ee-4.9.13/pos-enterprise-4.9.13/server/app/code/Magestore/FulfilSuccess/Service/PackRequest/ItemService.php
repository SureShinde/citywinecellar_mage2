<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Service\PackRequest;

use Magestore\FulfilSuccess\Api\Data\PackRequestItemInterface;
use Magento\Sales\Api\Data\OrderItemInterface;

class ItemService
{
    /**
     * Filter qty data before updating to Pack Request Item
     *
     * @param array $qtys
     * @return array
     */
    public function filterQtyData($qtys)
    {
        $filteredQtys = [];
        if(!empty($qtys)) {
            foreach($qtys as $qtyData) {
                $filteredQty = [];
                if(isset($qtyData[PackRequestItemInterface::ITEM_ID])) {
                    $filteredQty[PackRequestItemInterface::ITEM_ID] = $qtyData[PackRequestItemInterface::ITEM_ID];
                }
                if(isset($qtyData[PackRequestItemInterface::REQUEST_QTY])) {
                    $filteredQty[PackRequestItemInterface::REQUEST_QTY] = $qtyData[PackRequestItemInterface::REQUEST_QTY];
                }
                if(isset($qtyData[PackRequestItemInterface::PACKED_QTY])) {
                    $filteredQty[PackRequestItemInterface::PACKED_QTY] = $qtyData[PackRequestItemInterface::PACKED_QTY];
                }
                if(!empty($filteredQty)) {
                    $filteredQtys[] = $filteredQty;
                }
            }
        }
        return $filteredQtys;
    }

}
