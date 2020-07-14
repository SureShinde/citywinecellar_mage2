<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Api\Data;

interface ReturnOrderItemSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get return order list.
     *
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemInterface[]
     */
    public function getItems();

    /**
     * Set return order list.
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}