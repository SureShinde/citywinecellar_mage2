<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_OneStepCheckout
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Rewardpoints\Observer\Webpos;

use Magento\Framework\Event\ObserverInterface;

/**
 * Observer - Webpos - Get Loyalty Collection
 */
class GetLoyaltyCollection implements ObserverInterface
{
    /**
     * @inheritDoc
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();
        $collection->getSelect()->joinLeft(
            ['rewardpoints_customer' => $collection->getTable('rewardpoints_customer')],
            'e.entity_id = rewardpoints_customer.customer_id',
            [
                'rewardpoints_updated_at' => 'rewardpoints_customer.updated_at',
                'point_balance' => 'rewardpoints_customer.point_balance'
            ]
        );
        return $this;
    }
}
