<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Model\ResourceModel\Order\NeedVerify;

use Magento\Sales\Model\Order as OrderInterface;

/**
 * Order need verify Collection
 */
class Collection extends \Magestore\OrderSuccess\Model\ResourceModel\Order\Collection
{
    /**
     * Add condition
     *
     * @return Collection|void
     */
    public function addCondition()
    {
        $this->addFieldToFilter('is_verified', 0);
        $this->addFieldToFilter(
            'main_table.status',
            [
                'nin' => [
                    OrderInterface::STATE_HOLDED,
                    OrderInterface::STATE_CANCELED,
                    OrderInterface::STATE_CLOSED,
                    OrderInterface::STATE_COMPLETE
                ]
            ]
        );
    }
}
