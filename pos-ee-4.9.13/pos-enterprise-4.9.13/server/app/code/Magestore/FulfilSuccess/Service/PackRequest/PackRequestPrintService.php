<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Service\PackRequest;

use Magestore\FulfilSuccess\Api\PackRequestRepositoryInterface;
use Magestore\FulfilSuccess\Service\PrintService;


class PackRequestPrintService extends PrintService
{
    const GROUP_TYPE_ORDER = 1;
    const GROUP_TYPE_PRODUCT = 2;
    
    /**
     * @var PackRequestRepositoryInterface
     */
    protected $packRequestRepository;
    
    
    public function __construct(PackRequestRepositoryInterface $packRequestRepository)
    {
        $this->packRequestRepository = $packRequestRepository;
    }

    /**
     * Print packing list which group by order
     *
     * @param \Magestore\FulfilSuccess\Api\Data\PackRequestInterface[] $packRequests
     * @return array
     */
    public function printPackingListByOrder($packRequests)
    {
        $printData = [];
        foreach($packRequests as $packRequest){
            $packItems = $this->packRequestRepository->getItemList($packRequest);
            if(empty($packItems)) {
                continue;
            }
            $printData[$packRequest->getOrderId()]['order_id'] = $packRequest->getOrderIncrementId();
            $printData[$packRequest->getOrderId()]['items'] = [];
            $orderItemIds = [];
            foreach($packItems as $packItem) {
                $printData[$packRequest->getOrderId()]['items'][] = [
                    'item_id' => $packItem->getItemId(),
                    'sku' => $packItem->getItemSku(),
                    'name' => $packItem->getItemName(),
                    'shelf_location' => $packItem->getShelfLocation(),
                    'qty' => floatval($packItem->getNeedToPackQty()),
                    'packed_qty' => floatval($packItem->getPackedQty()),
                ];
            }
        }
        return $printData;
    }

    /**
     * Print packed Sales Items of Pack Requests
     * 
     * @param \Magestore\FulfilSuccess\Api\Data\PackRequestInterface[] $packRequests
     * @return array
     */
    public function printPackedOrderItems($packRequests)
    {
        $printData = $this->printPackingListByOrder($packRequests);
        if(!empty($printData)) {
            foreach($printData as $key => &$data) {
                if(!empty($data['items'])) {
                    foreach($data['items'] as $itemKey => $item) {
                        if(!$item['packed_qty']) {
                            unset($printData[$key]['items'][$itemKey]);
                        }
                    }
                }
                if(empty($printData[$key]['items'])) {
                    unset($printData[$key]);
                }                
            }
        }

        return $printData;
    }
}