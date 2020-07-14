<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Api\AdjustStock;

use Magestore\AdjustStock\Api\Data\AdjustStock\AdjustStockInterface;

interface AdjustStockManagementInterface
{
    /**
     * Create new stock adjustment
     *
     * @param \Magestore\AdjustStock\Api\Data\AdjustStock\AdjustStockInterface $adjustStock
     * @param array $data
     * @return AdjustStockInterface
     */
    public function createAdjustment(AdjustStockInterface $adjustStock, $data);

    /**
     * Generate new adjust stock code
     *
     * @return string
     */
    public function generateCode();

    /**
     * Check adjustment code
     *
     * @param int|null $adjustmentId
     * @param string $adjustmentCode
     * @return bool
     */
    public function checkAdjustmentCode($adjustmentId, $adjustmentCode);

    /**
     * Complete an adjustment
     *
     * @param AdjustStockInterface $adjustStock
     */
    public function complete(AdjustStockInterface $adjustStock);

    /**
     * Check is show thumbnail in grid
     *
     * @return bool
     */
    public function isShowThumbnail();

    /**
     * @param null $adjustStockId
     * @return mixed
     */
    public function getSelectBarcodeProductListJson($adjustStockId = null);

    /**
     * @return mixed
     */
    public function getProductCollection();
}

