<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\ResourceModel\Customer\Indexer;

/**
 * Customers collection for customer_grid indexer
 */
class Collection extends \Magento\Customer\Model\ResourceModel\Customer\Collection
{
    /**
     * @inheritdoc
     */
    protected function beforeAddLoadedItem(\Magento\Framework\DataObject $item)
    {
        if (!$item->getBillingTelephone()){
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $customerRepository = $objectManager
                ->get('Magento\Customer\Api\CustomerRepositoryInterface');
            $customer = $customerRepository->getById($item->getId());
            if ($customer->getCustomAttribute('customer_telephone')){
                $telephoneValue = $customer->getCustomAttribute('customer_telephone')->getValue();
                $item->setBillingTelephone($telephoneValue);
            }
        }
        return $item;
    }
}
