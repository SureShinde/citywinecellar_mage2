<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Api\Data;

interface PurchaseOrderItemReturnedSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get purchase order list.
     *
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemReturnedInterface[]
     */
    public function getItems();

    /**
     * Set purchase order list.
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemReturnednterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}