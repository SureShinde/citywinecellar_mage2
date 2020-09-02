<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Model\ResourceModel\Order\Canceled;

/**
 * Order Canceled Collection
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
        $this->addFieldToFilter(
            'main_table.status',
            [
                'in' => [
                    'canceled',
                    'closed'
                ]
            ]
        );
    }
}
