<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Observer\Sales;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;

class CreditmemoItemSaveAfter implements ObserverInterface
{
    /**
     * @var OrderItemRepositoryInterface
     */
    protected $orderItemRepository;
    

    public function __construct(  
        OrderItemRepositoryInterface $orderItemRepository
    ) {
        $this->orderItemRepository = $orderItemRepository;
    }    
    
    
    /**
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        $item = $observer->getEvent()->getCreditmemoItem();
        $orderItem = $item->getOrderItem();
        $orderItem->setQtyPrepareship(0);
        $this->orderItemRepository->save($orderItem);
    }    
}