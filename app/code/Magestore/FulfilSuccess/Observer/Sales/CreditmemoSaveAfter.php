<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Observer\Sales;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magestore\FulfilSuccess\Api\Data\PickRequestInterface;


class CreditmemoSaveAfter extends OrderCancelAfter implements ObserverInterface
{
    /**
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        $creditMemo = $observer->getEvent()->getCreditmemo();
        $order = $creditMemo->getOrder();
        
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
            $this->orderItemRepository->save($item);
        }        
        
        return $this;
    }    
}