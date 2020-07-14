<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Model\ResourceModel;

class Barcode  extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('os_barcode', 'id');
    }

    /**
     * Update updated time of product
     * @param $tableName
     * @param $updatedAt
     * @param $productId
     */
    public function updateUpdatedTimeOfProduct($tableName, $updatedAt, $productId)
    {
        if ($this->getConnection()->isTableExists($tableName)) {
            $this->getConnection()->update(
                $tableName,
                ['updated_at' => $updatedAt],
                "entity_id = $productId"
            );
        }
    }
}
