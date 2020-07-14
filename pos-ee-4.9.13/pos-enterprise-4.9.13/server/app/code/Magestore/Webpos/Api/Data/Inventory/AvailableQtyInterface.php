<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Inventory;

/**
 * @api
 */
interface AvailableQtyInterface
{
    const AVAILABLE_QTY = 'available_qty';

    /**
     * @return float
     */
    public function getAvailableQty();

    /**
     * @param float $availableQty
     * @return AvailableQtyInterface
     */
    public function setAvailableQty($availableQty);
}