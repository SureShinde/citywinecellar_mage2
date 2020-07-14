<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Observer\Sales;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magestore\DropshipSuccess\Api\Data\DropshipRequestInterface;


/**
 * Class CreditmemoSaveAfter
 * @package Magestore\DropshipSuccess\Observer\Sales
 */
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