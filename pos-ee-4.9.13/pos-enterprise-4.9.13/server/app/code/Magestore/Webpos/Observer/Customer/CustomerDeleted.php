<?php

namespace Magestore\Webpos\Observer\Customer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class CustomerDeleted implements ObserverInterface {

    protected $customerDeletedRepository;
    protected $customerDeletedInterfaceFactory;

    public function __construct(
        \Magestore\Webpos\Api\Log\CustomerDeletedRepositoryInterface $customerDeletedRepository,
        \Magestore\Webpos\Api\Data\Log\CustomerDeletedInterfaceFactory $customerDeletedInterfaceFactory
    ){
        $this->customerDeletedRepository = $customerDeletedRepository;
        $this->customerDeletedInterfaceFactory = $customerDeletedInterfaceFactory;
    }

    public function execute(EventObserver $observer)
    {
        $customerId = $observer->getCustomer()->getId();
        $customerDeleted = $this->customerDeletedInterfaceFactory->create()->setCustomerId($customerId);
        $this->customerDeletedRepository->save($customerDeleted);
    }
}