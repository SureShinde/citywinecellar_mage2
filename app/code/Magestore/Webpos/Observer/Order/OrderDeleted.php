<?php

namespace Magestore\Webpos\Observer\Order;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class OrderDeleted implements ObserverInterface {

    protected $orderDeletedRepository;
    protected $orderDeletedInterfaceFactory;

    public function __construct(
        \Magestore\Webpos\Api\Log\OrderDeletedRepositoryInterface $orderDeletedRepository,
        \Magestore\Webpos\Api\Data\Log\OrderDeletedInterfaceFactory $orderDeletedInterfaceFactory
    ){
        $this->orderDeletedRepository = $orderDeletedRepository;
        $this->orderDeletedInterfaceFactory = $orderDeletedInterfaceFactory;
    }

    public function execute(EventObserver $observer)
    {
        $orderIncrementId = $observer->getOrder()->getIncrementId();
//        var_dump($orderIncrementId);die;
        $orderDeleted = $this->orderDeletedInterfaceFactory->create()
                        ->setOrderIncrementId($orderIncrementId);
        $this->orderDeletedRepository->save($orderDeleted);
    }
}