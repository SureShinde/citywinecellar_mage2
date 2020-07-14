<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Service\PickRequest;

use Magestore\FulfilSuccess\Api\PickRequestRepositoryInterface;
use Magestore\FulfilSuccess\Api\PackRequestRepositoryInterface;
use Magestore\FulfilSuccess\Service\PrintService;


class PickRequestPrintService extends PrintService
{
    const GROUP_TYPE_PICK_REQUEST = 1;
    const GROUP_TYPE_PRODUCT = 2;

    /**
     * @var PickRequestRepositoryInterface
     */
    protected $pickRequestRepository;

    /**
     * @var PackRequestRepositoryInterface
     */
    protected $packRequestRepository;

    /**
     * @var \Magestore\FulfilSuccess\Helper\Data
     */
    protected $helper;


    public function __construct(
        PickRequestRepositoryInterface $pickRequestRepository,
        PackRequestRepositoryInterface $packRequestRepository,
        \Magestore\FulfilSuccess\Helper\Data $helper
    )
    {
        $this->pickRequestRepository = $pickRequestRepository;
        $this->packRequestRepository = $packRequestRepository;
        $this->helper = $helper;
    }

    /**
     * Print Picking lists of Pick Requests
     *
     * @param \Magestore\FulfilSuccess\Api\Data\PickRequestInterface[] $pickRequests
     * @param int $groupType
     * @return array
     */
    public function printPickingList($pickRequests, $groupType)
    {
        if(!count($pickRequests)) {
            return;
        }

        switch($groupType) {
            case self::GROUP_TYPE_PICK_REQUEST:
                return $this->printPickingListByPickId($pickRequests);

            case self::GROUP_TYPE_PRODUCT:
            default:
                return $this->printPickingListByProduct($pickRequests);
        }
    }

    /**
     * Print picking list which group by Pick ID
     *
     * @param \Magestore\FulfilSuccess\Api\Data\PickRequestInterface[] $pickRequests
     * @return array
     */
    public function printPickingListByPickId($pickRequests)
    {
        $printData = [];
        foreach($pickRequests as $pickRequest){
            $pickItems = $this->pickRequestRepository->getItemList($pickRequest);
            if(empty($pickItems)) {
                continue;
            }
            $packRequest = $this->packRequestRepository->getByPickRequestId($pickRequest->getId());
            $printData[$pickRequest->getId()]['pack_request_id'] = ($packRequest)?$packRequest->getId():false;
            $printData[$pickRequest->getId()]['pick_request_id'] = $pickRequest->getId();
            $printData[$pickRequest->getId()]['order_id'] = $pickRequest->getOrderIncrementId();
            $printData[$pickRequest->getId()]['items'] = [];
            foreach($pickItems as $pickItem) {
                $printData[$pickRequest->getId()]['items'][] = [
                    'item_id' => $pickItem->getItemId(),
                    'sku' => $pickItem->getItemSku(),
                    'name' => $pickItem->getItemName(),
                    'shelf_location' => $pickItem->getShelfLocation(),
                    'qty' => floatval($pickItem->getNeedToPickQty()),
                    'picked_qty' => floatval($pickItem->getPickedQty()),
                    'barcodes' => $this->helper->getProductBarcodes($pickItem->getProductId()),
                ];
            }
        }
        return $printData;
    }

    /**
     * Print picking list which group by product
     *
     * @param \Magestore\FulfilSuccess\Api\Data\PickRequestInterface[] $pickRequests
     * @return array
     */
    public function printPickingListByProduct($pickRequests)
    {
        $printData = [];
        $items = [];
        foreach($pickRequests as $pickRequest){
            $pickItems = $this->pickRequestRepository->getItemList($pickRequest);
            if(empty($pickItems)) {
                continue;
            }
            foreach($pickItems as $pickItem) {
                if(!isset($items[$pickItem->getProductId()])) {
                    $items[$pickItem->getProductId()] = [
                        'item_id' => $pickItem->getItemId(),
                        'sku' => $pickItem->getItemSku(),
                        'name' => $pickItem->getItemName(),
                        'shelf_location' => $pickItem->getShelfLocation(),
                        'qty' => floatval($pickItem->getNeedToPickQty()),
                        'picked_qty' => floatval($pickItem->getPickedQty()),
                        'barcodes' => $this->helper->getProductBarcodes($pickItem->getProductId()),
                    ];
                } else {
                    $items[$pickItem->getProductId()]['qty'] += $pickItem->getNeedToPickQty();
                }
            }
        }
        $printData[0]['order_id'] = null;
        $printData[0]['items'] = $items;
        return $printData;
    }

    /**
     * Print picked Sales Items of Pick Requests
     *
     * @param \Magestore\FulfilSuccess\Api\Data\PickRequestInterface[] $pickRequests
     * @return array
     */
    public function printPickedOrderItems($pickRequests)
    {
        $printData = $this->printPickingListByPickId($pickRequests);
        if(!empty($printData)) {
            foreach($printData as $key => &$data) {
                if(!empty($data['items'])) {
                    foreach($data['items'] as $itemKey => $item) {
                        if(!$item['picked_qty']) {
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
