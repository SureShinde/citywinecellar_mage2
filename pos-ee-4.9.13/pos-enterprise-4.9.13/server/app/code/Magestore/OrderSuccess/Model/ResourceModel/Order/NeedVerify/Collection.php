<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Model\ResourceModel\Order\NeedVerify;

use Magento\Sales\Model\Order as OrderInterface;

/**
 * Class Collection
 * @package Magestore\OrderSuccess\Model\ResourceModel\Order\NeedVerify
 */
class Collection extends \Magestore\OrderSuccess\Model\ResourceModel\Order\Collection
{
    /**
     * add condition.
     *
     * @param
     * @return $this
     */
    public function addCondition()
    {
        $this->addFieldToFilter('is_verified', 0);
        $this->addFieldToFilter('main_table.status', array(
            'nin' => array(
                OrderInterface::STATE_HOLDED,
                OrderInterface::STATE_CANCELED,
                OrderInterface::STATE_CLOSED,
                OrderInterface::STATE_COMPLETE
            )
        ));
    }

}
