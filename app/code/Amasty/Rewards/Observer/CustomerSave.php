<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Observer;

use Magento\Framework\Event\ObserverInterface;
use Amasty\Rewards\Model\ConstantRegistryInterface as Constant;

class CustomerSave implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer
     */
    protected $customerResource;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Customer\Model\ResourceModel\Customer $customerResource,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        $this->request = $request;
        $this->customerFactory = $customerFactory;
        $this->customerResource = $customerResource;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->request->getFullActionName() == 'customer_index_save') {
            $data = $this->request->getPost('customer');
            $customer = $this->customerFactory->create();

            if (isset($data['entity_id'])) {
                $this->customerResource->load($customer, (int)$data['entity_id']);
            }

            if (isset($data[Constant::NOTIFICATION_EARNING]) && !$data[Constant::NOTIFICATION_EARNING]) {
                $customer->setAmrewardsEarningNotification((int)$data[Constant::NOTIFICATION_EARNING]);
                $this->customerResource->saveAttribute($customer, Constant::NOTIFICATION_EARNING);
            }

            if (isset($data[Constant::NOTIFICATION_EXPIRE]) && !$data[Constant::NOTIFICATION_EXPIRE]) {
                $customer->setAmrewardsExpireNotification((int)$data[Constant::NOTIFICATION_EXPIRE]);
                $this->customerResource->saveAttribute($customer, Constant::NOTIFICATION_EXPIRE);
            }
        }
    }
}
