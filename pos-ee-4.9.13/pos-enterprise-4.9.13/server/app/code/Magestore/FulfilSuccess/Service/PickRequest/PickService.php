<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Service\PickRequest;

use Magestore\FulfilSuccess\Api\Data\PickRequestInterface;
use Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface;
use Magestore\FulfilSuccess\Service\Locator\UserServiceInterface;


class PickService
{
    const PICKING_REQUEST_SESSION_ID = 'picking_request_session_id';

    /**
     * @var \Magestore\FulfilSuccess\Api\PickRequestRepositoryInterface
     */
    protected $pickRequestRepository;

    /**
     * @var \Magestore\FulfilSuccess\Api\PickRequestItemRepositoryInterface
     */
    protected $pickRequestItemRepository;

    /**
     * @var \Magestore\FulfilSuccess\Api\OrderItemRepositoryInterface
     */
    protected $orderItemRepository;

    /**
     * @var \Magestore\FulfilSuccess\Service\PickRequest\ItemService
     */
    protected $itemService;

    /**
     * @var \Magestore\FulfilSuccess\Service\PickRequest\PickRequestService
     */
    protected $pickRequestService;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var \Magestore\FulfilSuccess\Helper\Data
     */
    protected $helper;
    
    /**
     * @var UserServiceInterface 
     */
    protected $userService;    

    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $_objectManager;

    /**
     * PickService constructor.
     * @param \Magestore\FulfilSuccess\Api\PickRequestRepositoryInterface $pickRequestRepository
     * @param \Magestore\FulfilSuccess\Api\PickRequestItemRepositoryInterface $pickRequestItemRepository
     * @param \Magestore\FulfilSuccess\Api\OrderItemRepositoryInterface $orderItemRepository
     * @param \Magestore\FulfilSuccess\Service\PickRequest\ItemService $itemService
     * @param \Magestore\FulfilSuccess\Service\PickRequest\PickRequestService $pickRequestService
     * @param \Magestore\FulfilSuccess\Helper\Data $helper
     * @param \Magento\Backend\Model\Session $session
     */
    public function __construct(
        \Magestore\FulfilSuccess\Api\PickRequestRepositoryInterface $pickRequestRepository,
        \Magestore\FulfilSuccess\Api\PickRequestItemRepositoryInterface $pickRequestItemRepository,
        \Magestore\FulfilSuccess\Api\OrderItemRepositoryInterface $orderItemRepository,
        \Magestore\FulfilSuccess\Service\PickRequest\ItemService $itemService,
        \Magestore\FulfilSuccess\Service\PickRequest\PickRequestService $pickRequestService,
        UserServiceInterface $userService,
        \Magestore\FulfilSuccess\Helper\Data $helper,
        \Magento\Backend\Model\Session $session
    ) {
        $this->pickRequestRepository = $pickRequestRepository;
        $this->pickRequestItemRepository = $pickRequestItemRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->itemService = $itemService;
        $this->pickRequestService = $pickRequestService;
        $this->userService = $userService;
        $this->helper = $helper;
        $this->session = $session;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * Finish pick request
     * @param \Magestore\FulfilSuccess\Api\Data\PickRequestInterface $request
     * @param array $pickedData
     */
    public function finishPickRequest($request, $pickedData){
        if($request && $request->getId()){
            $status = $request->getData(PickRequestInterface::STATUS);
            $qtyPicked = 0;
            if($status ==  PickRequestInterface::STATUS_PICKING){
                if(!empty($pickedData)){
                    $items = $this->pickRequestItemRepository->getListByRequestId($request->getId());
                    if (count($items) > 0) {
                        foreach ($items as $item) {
                            $itemId = $item->getData(PickRequestItemInterface::PICK_REQUEST_ITEM_ID);
                            $parentId = $item->getData(PickRequestItemInterface::PARENT_PICK_REQUEST_ITEM_ID);
                            if(isset($pickedData[$itemId])){
                                $item->setData(PickRequestItemInterface::PICKED_QTY, $pickedData[$itemId][PickRequestItemInterface::PICKED_QTY]);
                                $this->pickRequestItemRepository->save($item);
                            }elseif(!empty($parentId) && isset($pickedData[$parentId])){
                                $parentPickedQty = $pickedData[$parentId][PickRequestItemInterface::PICKED_QTY];
                                $parentRequestQty = $pickedData[$parentId][PickRequestItemInterface::REQUEST_QTY];
                                $pickedQty = $item->getData(PickRequestItemInterface::PICKED_QTY);
                                $requestQty = $item->getData(PickRequestItemInterface::REQUEST_QTY);
                                $newPickedQty = ($parentPickedQty == $parentRequestQty)?$requestQty:($parentPickedQty/$parentRequestQty*$requestQty);
                                if($pickedQty != $newPickedQty){
                                    $item->setData(PickRequestItemInterface::PICKED_QTY, $newPickedQty);
                                    $this->pickRequestItemRepository->save($item);
                                }
                            }
                            $itemModel = $this->_objectManager->create(\Magento\Sales\Model\Order\Item::class)
                                ->load($item->getData(PickRequestItemInterface::ITEM_ID));

                            /* ignore ship together children of bundle*/
                            if (!(!$itemModel->isShipSeparately() && $parentId)) {
                                $qtyPicked = $qtyPicked + $item->getData(PickRequestItemInterface::PICKED_QTY);
                            }
                        }
                    }
                }
                $request->setTotalItems($qtyPicked);
                $request->setAge($this->pickRequestService->getAge($request));          
                $request->setData(PickRequestInterface::STATUS, PickRequestInterface::STATUS_PICKED);
                $request->setUserId($this->userService->getCurrentUserId());
                $this->pickRequestRepository->save($request);
                
                $this->pickRequestService->moveItemsToNeedToShip($request);
                $this->pickRequestService->createPackRequest($request);
            }
        }
    }

    /**
     * Finish picking request
     * @param array $pickedData
     * @param bool $pickRequestId
     */
    public function finishPickingRequest($pickedData, $pickRequestId = false){
        $resetSession = true;
        $request = $this->session->getData(self::PICKING_REQUEST_SESSION_ID);
        if($pickRequestId){
            $resetSession = ($request && ($request->getId() == $pickRequestId))?true:false;
            $request = $this->pickRequestRepository->getById($pickRequestId);
        }
        $this->finishPickRequest($request, $pickedData);
        if($resetSession){
            $this->session->setData(self::PICKING_REQUEST_SESSION_ID, null);
        }
    }

    /**
     * @param \Magestore\FulfilSuccess\Api\Data\PickRequestInterface $request
     * @return array
     */
    public function startPickRequest($request){
        $result = [];
        if($request->getId()){
            $this->session->setData(self::PICKING_REQUEST_SESSION_ID, $request);
            $result = $this->getPickRequestItems($request);
        }
        return $result;
    }

    /**
     * @param \Magestore\FulfilSuccess\Api\Data\PickRequestInterface $request
     * @return array
     */
    public function getPickRequestItems($request){
        $result = [];
        if($request->getId()) {
            $items = $this->pickRequestItemRepository->getListByRequestId($request->getId());
            if (count($items) > 0) {
                foreach ($items as $item) {
                    $data = $item->getData();
                    $data[PickRequestItemInterface::REQUEST_QTY] = floatval($data[PickRequestItemInterface::REQUEST_QTY]);
                    $data[PickRequestItemInterface::PICKED_QTY] = floatval($data[PickRequestItemInterface::PICKED_QTY]);
                    $data[PickRequestItemInterface::ITEM_BARCODE] = $this->getPickRequestItemBarcodes($item);
                    $result[] = $data;
                }
            }
        }
        return $result;
    }

    /**
     * @param \Magestore\FulfilSuccess\Api\Data\PickRequestInterface $request
     * @return boolean
     */
    public function canPick($request){
        $canPick = false;
        if($request->getId()){
            $pickingRequest = $this->session->getData(self::PICKING_REQUEST_SESSION_ID);
            $canPick = (!$pickingRequest || ($pickingRequest && ($pickingRequest->getId() == $request->getId())))?true:false;
        }
        return $canPick;
    }

    /**
     * @return array
     */
    public function getPickingItems(){
        $items = [];
        $pickingRequest = $this->session->getData(self::PICKING_REQUEST_SESSION_ID);
        if($pickingRequest && $pickingRequest->getId()){
            $items = $this->getPickRequestItems($pickingRequest);
        }
        return $items;
    }

    /**
     * @return string
     */
    public function getPickingOrderIncrementId(){
        $orderIncrementId = '';
        $pickingRequest = $this->session->getData(self::PICKING_REQUEST_SESSION_ID);
        if($pickingRequest && $pickingRequest->getId()){
            $orderIncrementId = $pickingRequest->getData(PickRequestInterface::ORDER_INCREMENT_ID);
        }
        return $orderIncrementId;
    }

    /**
     * @return string
     */
    public function getPickingRequestId(){
        $requestId = '';
        $pickingRequest = $this->session->getData(self::PICKING_REQUEST_SESSION_ID);
        if($pickingRequest && $pickingRequest->getId()){
            $requestId = $pickingRequest->getData(PickRequestInterface::PICK_REQUEST_ID);
        }
        return $requestId;
    }

    /**
     * @param \Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface $item
     * @return string
     */
    public function getPickRequestItemBarcodes($item){
        $barcodes = "";
        if($item && $item->getId()){
            $barcodes = $this->helper->getProductBarcodes($item->getData(PickRequestItemInterface::PRODUCT_ID));
        }
        return $barcodes;
    }

    /**
     * @param $productId
     * @return string
     */
    public function getItemBarcodesByProductId($productId){
        return $this->helper->getProductBarcodes($productId);
    }

    /**
     * Remove picking session data
     */
    public function removePickingSession(){
        $this->session->setData(self::PICKING_REQUEST_SESSION_ID, null);
    }

    /**
     * @param $barcodes
     * @return array|string
     */
    public function prepareBarcodesForView($barcodes){
        if($barcodes){
            $barcodes = explode('||',$barcodes);
            $barcodes = implode(', ',$barcodes);
        }
        return $barcodes;
    }
}