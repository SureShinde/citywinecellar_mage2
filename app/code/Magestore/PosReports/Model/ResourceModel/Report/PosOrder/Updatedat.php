<?php
/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\PosReports\Model\ResourceModel\Report\PosOrder;

/**
 * POS Order entity resource model with aggregation by updated at
 *
 * Class Updatedat
 */
class Updatedat extends Createdat
{
    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('pos_order_aggregated_updated', 'id');
    }

    /**
     * Aggregate POS Orders data by order updated at
     *
     * @param string|int|\DateTime|array|null $from
     * @param string|int|\DateTime|array|null $to
     * @return Createdat|Updatedat
     * @throws \Exception
     */
    public function aggregate($from = null, $to = null)
    {
        return $this->_aggregateByField('updated_at', $from, $to);
    }
}
