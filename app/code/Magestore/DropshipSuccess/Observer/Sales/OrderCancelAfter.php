<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Observer\Sales;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magestore\DropshipSuccess\Api\Data\DropshipRequestInterface;
use Magestore\DropshipSuccess\Api\DropshipRequestRepositoryInterface;
use Magestore\DropshipSuccess\Service\DropshipRequestService;
use Magento\Framework\Api\SearchCriteriaBuilder;


/**
 * Class OrderCancelAfter
 * @package Magestore\DropshipSuccess\Observer\Sales
 */
class OrderCancelAfter implements ObserverInterface
{
    /**
     * @var DropshipRequestService
     */
    protected $dropshipRequestService;
    
    /**
     * @var SearchCriteriaBuilder 
     */
    protected $searchCriteriaBuilder;
    
    /**
     * @var DropshipRequestRepositoryInterface
     */
    protected $dropshipRequestRepository;

    /**
     * OrderCancelAfter constructor.
     * @param DropshipRequestService $dropshipRequestService
     * @param DropshipRequestRepositoryInterface $dropshipRequestRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        DropshipRequestService $dropshipRequestService,
        DropshipRequestRepositoryInterface $dropshipRequestRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        $this->dropshipRequestService = $dropshipRequestService;
        $this->dropshipRequestRepository = $dropshipRequestRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
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
                            ->addFilter(DropshipRequestInterface::ORDER_ID, $order->getId())
                            ->create();
        $dropshipRequests = $this->dropshipRequestRepository->getList($searchCriteria);
        if($dropshipRequests->getTotalCount()) {
            foreach($dropshipRequests->getItems() as $dropshipRequest) {
                $this->dropshipRequestService->cancel($dropshipRequest);
            }
        }
        return $this;
    }    
}