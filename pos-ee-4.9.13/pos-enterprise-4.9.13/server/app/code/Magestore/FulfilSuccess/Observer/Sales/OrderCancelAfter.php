<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Observer\Sales;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magestore\FulfilSuccess\Api\Data\PickRequestInterface;
use Magestore\FulfilSuccess\Api\Data\PackRequestInterface;
use Magestore\FulfilSuccess\Api\PickRequestRepositoryInterface;
use Magestore\FulfilSuccess\Api\PackRequestRepositoryInterface;
use Magestore\FulfilSuccess\Service\PickRequest\PickRequestService;
use Magestore\FulfilSuccess\Service\PackRequest\PackRequestService;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\OrderItemRepositoryInterface;


class OrderCancelAfter implements ObserverInterface 
{
    /**
     * @var PickRequestService
     */
    protected $pickRequestService;
    
    /**
     * @var PackRequestService
     */
    protected $packRequestService;    
    
    /**
     * @var SearchCriteriaBuilder 
     */
    protected $searchCriteriaBuilder;
    
    /**
     * @var PickRequestRepositoryInterface 
     */
    protected $pickRequestRepository;
    
    /**
     * @var PackRequestRepositoryInterface 
     */
    protected $packRequestRepository;
    
    /**
     * @var OrderItemRepositoryInterface
     */
    protected $orderItemRepository;    
    
    
    public function __construct(
        PickRequestService $pickRequestService, 
        PackRequestService $packRequestService,
        PickRequestRepositoryInterface $pickRequestRepository,
        PackRequestRepositoryInterface $packRequestRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OrderItemRepositoryInterface $orderItemRepository
    )
    {
        $this->pickRequestService = $pickRequestService;
        $this->packRequestService = $packRequestService;
        $this->pickRequestRepository = $pickRequestRepository;
        $this->packRequestRepository = $packRequestRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->orderItemRepository = $orderItemRepository;
    }
    
    /**
     * 
     * @param EventObserver $observer
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(EventObserver $observer) 
    {
        $order = $observer->getEvent()->getOrder();
        
        $searchCriteria = $this->searchCriteriaBuilder
                            ->addFilter(PickRequestInterface::ORDER_ID, $order->getId())
                            ->create();
        $pickRequests = $this->pickRequestRepository->getList($searchCriteria);
        $packRequests = $this->packRequestRepository->getList($searchCriteria);
        
        if($pickRequests->getTotalCount()) {
            foreach($pickRequests->getItems() as $pickRequest) {
                $this->pickRequestService->cancel($pickRequest);
            }
        }
        
        if($packRequests->getTotalCount()) {
            foreach($packRequests->getItems() as $packRequest) {
                $this->packRequestService->cancel($packRequest);
            }            
        }
        /* remove qty_prepareship in order items */
        foreach($order->getAllItems() as $item) {
            $item->setQtyPrepareship(0);
        }

        return $this;
    }    
}