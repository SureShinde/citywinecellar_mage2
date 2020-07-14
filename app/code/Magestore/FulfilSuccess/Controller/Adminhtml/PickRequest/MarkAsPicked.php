<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Controller\Adminhtml\PickRequest;

use \Magestore\FulfilSuccess\Api\Data\PickRequestInterface;
use \Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface;

/**
 * Class MarkAsPicked
 * @package Magestore\FulfilSuccess\Controller\Adminhtml\PickRequest
 */
class MarkAsPicked extends \Magestore\FulfilSuccess\Controller\Adminhtml\PickRequest\PickItems
{

    public function execute()
    {
        $response = [
            'action' => 'mark_as_picked'
        ];
        $pickRequestId = $this->getRequest()->getParam('id');
        $pickRequestItems = $this->getRequest()->getParam('items');
        if(!empty($pickRequestId) && !empty($pickRequestItems) && is_array($pickRequestItems)){
            $request = $this->pickRequestRepository->getById($pickRequestId);
            if($request->getId()){
                $status = $request->getData(PickRequestInterface::STATUS);
                if($status == PickRequestInterface::STATUS_PICKING){
                    $items = $this->pickRequestRepository->getItemList($request);
                    $pickedData = [];
                    if(count($pickRequestItems) > 0) {
                        foreach ($items as $item){
                            foreach ($pickRequestItems as $key => $itemData){
                                if(
                                    isset($itemData[PickRequestItemInterface::PICK_REQUEST_ITEM_ID]) &&
                                    $item->getId() == $itemData[PickRequestItemInterface::PICK_REQUEST_ITEM_ID]
                                ){
                                    $pickedData[$item->getId()] = $item->getData();
                                    $pickedData[$item->getId()][PickRequestItemInterface::PICKED_QTY] = $itemData[PickRequestItemInterface::PICKED_QTY];
                                    break;
                                }
                            }
                        }
                    }
                    $this->pickService->finishPickRequest($request, $pickedData);
                    $response['success'] = true;
                }else{
                    $response['error'] = true;
                    $response['message'] = __('The picking request for this order has been picked previously, can not pick it again');
                }
            }else{
                $response['error'] = true;
                $response['message'] = __('The picking request does not exist');
            }
        }else{
            $response['error'] = true;
            $response['message'] = __('Invalid Data');
        }
        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($response);
        return $resultJson;
    }
}
