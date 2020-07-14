<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Service\PickRequest;

use Magento\Framework\Serialize\Serializer\Json;
use Magestore\FulfilSuccess\Api\Data\PickRequestInterface;
use Magestore\FulfilSuccess\Api\Data\PickRequestInterfaceFactory;
use Magestore\FulfilSuccess\Api\Data\PackRequestInterface;
use Magestore\FulfilSuccess\Api\PickRequestRepositoryInterface;
use Magestore\FulfilSuccess\Service\PickRequest\PickRequestService;
use Magestore\FulfilSuccess\Service\PickRequest\DataProcessService;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magestore\FulfilSuccess\Api\FulfilManagementInterface;

class BuilderService
{
    /**
     * @var PickRequestInterfaceFactory
     */
    protected $pickRequestFactory;

    /**
     * @var PickRequestRepositoryInterface
     */
    protected $pickRequestRepository;

    /**
     * @var PickRequestService
     */
    protected $pickRequestService;

    /**
     * @var DataProcessService
     */
    protected $dataProcessService;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * Serializer
     *
     * @var Json
     */
    private $serializer;


    public function __construct(
        PickRequestInterfaceFactory $pickRequestFactory,
        PickRequestRepositoryInterface $pickRequestRepository,
        PickRequestService $pickRequestService,
        DataProcessService $dataProcessService,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FulfilManagementInterface $fulfilManagement,
        Json $serializer = null
    )
    {
        $this->pickRequestFactory = $pickRequestFactory;
        $this->pickRequestRepository = $pickRequestRepository;
        $this->pickRequestService = $pickRequestService;
        $this->dataProcessService = $dataProcessService;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->fulfilManagement = $fulfilManagement;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()->get(Json::class);
    }

    /**
     *
     * @param array $data
     * @return type
     */
    public function createPickRequestsFromPostData($data)
    {
        $requestData = $this->dataProcessService->processPostedRequestData($data);
        if (!count($requestData[DataProcessService::REQUESTS])) {
            return;
        }

        $order = $this->orderRepository->get($requestData[DataProcessService::ORDER_ID]);

        foreach ($requestData[DataProcessService::REQUESTS] as $resource => $items) {
            $this->createPickRequestFromOrder($resource, $order, $items);
        }
    }

    /**
     *
     * @param string|int $resource
     * @param int $orderId
     * @return PickRequestInterface|null
     */
    public function loadExistedPickingRequest($resource, $orderId)
    {
        $this->searchCriteriaBuilder
            ->addFilter(PickRequestInterface::ORDER_ID, $orderId)
            ->addFilter(PickRequestInterface::STATUS, PickRequestInterface::STATUS_PICKING);
        if ($this->fulfilManagement->isMSIEnable()) {
            $this->searchCriteriaBuilder->addFilter(PickRequestInterface::SOURCE_CODE, $resource);
        } else if ($this->fulfilManagement->isInventorySuccessEnable()) {
            $this->searchCriteriaBuilder->addFilter(PickRequestInterface::WAREHOUSE_ID, $resource);
        }
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $pickRequests = $this->pickRequestRepository->getList($searchCriteria);
        if ($pickRequests->getTotalCount()) {
            foreach ($pickRequests->getItems() as $pickRequest) {
                return $pickRequest;
            }
        }
        return null;
    }

    /**
     * Create PickRequest from Sales
     * $items[$itemId => $requestQty]
     *
     * @param string|int $resource
     * @param \Magento\Sales\Model\Order $order
     * @param array $items
     * @return PickRequestInterface
     */
    public function createPickRequestFromOrder($resource, $order, $items = [])
    {
        /**
         * @var PickRequestInterface $pickRequest
         */
        $pickRequest = $this->loadExistedPickingRequest($resource, $order->getId());
        if (!$pickRequest) {
            $pickRequest = $this->pickRequestFactory->create();
            $pickRequest->setOrderId($order->getId());
            $pickRequest->setOrderIncrementId($order->getIncrementId());
            if ($this->fulfilManagement->isMSIEnable()) {
                $pickRequest->setSourceCode($resource);
            } else if ($this->fulfilManagement->isInventorySuccessEnable()) {
                $pickRequest->setWarehouseId($resource);
            }
            $this->pickRequestRepository->save($pickRequest);
        }
        //$totalItems = $pickRequest->getTotalItems();
        foreach ($order->getAllItems() as $item) {
            if (!empty($items) && !isset($items[$item->getItemId()])) {
                /* do not add this item to Pick Request */
                continue;
            }
            /* add bundle item to Pick Request */
            if ($item->getProductType() == \Magento\Bundle\Model\Product\Type::TYPE_CODE) {
                $pickRequestItems = $this->addBundleItemToPickRequest($pickRequest, $item, $items);
                /*
                if($pickRequestItems && count($pickRequestItems)) {
                    foreach($pickRequestItems as $pickRequestItem) {
                        $totalItems += $pickRequestItem->getRequestQty();
                    }
                }
                 */
            }
            /* add configurable item to Pick Request */
            if ($item->getProductType() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                $pickRequestItem = $this->addConfigurableItemToPickRequest($pickRequest, $item, $items);
                /*
                if($pickRequestItem) {
                    $totalItems += $pickRequestItem->getRequestQty();
                }
                 */
            }
            if ($item->getProduct()->isComposite()) {
                continue;
            }
            /* prepare to add simple item to Pick Request */
            $requestQty = isset($items[$item->getItemId()]) ? floatval($items[$item->getItemId()]) : 0;
            $pickRequestItem = $this->pickRequestService->addItemToPickRequest($pickRequest, $item, $requestQty);
            //$totalItems += $pickRequestItem->getRequestQty();
        }
        /* save changes after add items to Pick Request */
        //$pickRequest->setTotalItems($totalItems);
        $this->pickRequestRepository->save($pickRequest);

        return $pickRequest;
    }

    /**
     * Add configurable item to Pick Request
     *
     * @param int $warehouseId
     * @param \Magento\Sales\Model\Order\Item $item
     * @param array $items
     * @return PickRequestInterface|null
     */
    public function addConfigurableItemToPickRequest($pickRequest, $item, $items)
    {
        if ($item->getProductType() != \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            return null;
        }
        $requestQty = isset($items[$item->getItemId()]) ? floatval($items[$item->getItemId()]) : 0;
        foreach ($item->getChildrenItems() as $child) {
            return $this->pickRequestService->addItemToPickRequest($pickRequest, $child, $requestQty);
        }
    }

    /**
     * Add bundle item to Pick Request
     *
     * @param int $warehouseId
     * @param \Magento\Sales\Model\Order\Item $item
     * @param array $items
     * @return array|null
     */
    public function addBundleItemToPickRequest($pickRequest, $item, $items)
    {
        if ($item->getProductType() != \Magento\Bundle\Model\Product\Type::TYPE_CODE) {
            return null;
        }

        if ($item->isShipSeparately() || !$item->getChildrenItems() || !count($item->getChildrenItems())) {
            return null;
        }
        $requestQty = isset($items[$item->getItemId()]) ? floatval($items[$item->getItemId()]) : 0;
        $parentPickRequestItem = $this->pickRequestService->addItemToPickRequest($pickRequest, $item, $requestQty);

        $pickRequestItems = [];
        foreach ($item->getChildrenItems() as $child) {
            if (isset($items[$child->getItemId()])) {
                continue;
            }
            $childRequestQty = $this->getChildBundleQty($child) * $requestQty;
            $pickRequestItems[] = $this->pickRequestService->addItemToPickRequest($pickRequest, $child, $childRequestQty, $parentPickRequestItem->getId());
        }
        return $pickRequestItems;
    }

    /**
     * Get bundle qty of child item
     *
     * @param \Magento\Sales\Model\Order\Item $item
     * @return float
     */
    public function getChildBundleQty($item)
    {
        $options = $item->getProductOptions();
        if (isset($options['bundle_selection_attributes'])) {
            $attributes = $this->serializer->unserialize($options['bundle_selection_attributes']);
            if (isset($attributes['qty'])) {
                return $attributes['qty'];
            }
        }
        return 0;
    }

    /**
     * Create pick request from pack
     *
     * @param PackRequestInterface $packRequest
     * @return PickRequestInterface
     */
    public function createPickRequestFromPack(PackRequestInterface $packRequest)
    {
        $pickRequest = $this->pickRequestFactory->create();
        $pickRequest->setOrderId($packRequest->getOrderId());
        $pickRequest->setOrderIncrementId($packRequest->getOrderIncrementId());
        $pickRequest->setWarehouseId($packRequest->getWarehouseId());
        $pickRequest->setSourceCode($packRequest->getSourceCode());
        $this->pickRequestRepository->save($pickRequest);

        $pickItems = [];
        $packItems = $packRequest->getItems();
        if (count($packItems)) {
            foreach ($packItems as $item) {
                $parentId = null;
                if ($item->getParentItemId()) {
                    if (isset($pickItems[$item->getParentItemId()])) {
                        $parentId = $pickItems[$item->getParentItemId()];
                    }
                }
                $pickItem = $this->pickRequestService->addPackItemToPickRequest($pickRequest, $item, $parentId);
                $pickItems[$pickItem->getItemId()] = $pickItem->getId();
            }
        }
        /* save changes after add items to Pick Request */
        $this->pickRequestRepository->save($pickRequest);

        return $pickRequest;
    }

}
