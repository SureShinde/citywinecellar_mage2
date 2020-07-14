<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\ResourceModel\Log;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magestore\Webpos\Api\Data\Log\ProductDeletedInterface;

class ProductDeleted extends AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('webpos_product_deleted', 'id');
    }

    /**
     * @param int $productId
     * @param int[]|null $stockIds
     */
    public function insertMultiple($productId = null, $stockIds = [])
    {
        $connection = $this->getConnection();
        $insertData = [];
        if (empty($stockIds)) {
            $connection->insertMultiple($this->getMainTable(), [ProductDeletedInterface::PRODUCT_ID => $productId]);
        } else {
            foreach ($stockIds as $stockId) {
                $insertData[] = [
                    ProductDeletedInterface::PRODUCT_ID => $productId,
                    ProductDeletedInterface::STOCK_ID => $stockId
                ];
            }
            $connection->insertMultiple($this->getMainTable(), $insertData);
        }
    }

    /**
     * @param int|null $productId
     * @param int[]|null $stockIds
     */
    public function deleteByProductId($productId = null, $stockIds = [])
    {
        if ($productId) {
            $whereCondition = [ProductDeletedInterface::PRODUCT_ID . ' = ?' => $productId];
            if (!empty($stockIds)) {
                $whereCondition[ProductDeletedInterface::STOCK_ID . ' IN (?)'] = $stockIds;
            }
            $this->getConnection()->delete($this->getMainTable(), $whereCondition);
        }
    }
}
