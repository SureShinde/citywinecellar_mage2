<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Api\Data;

interface InvoiceSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get purchase order list.
     *
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\InvoiceInterface[]
     */
    public function getItems();

    /**
     * Set purchase order list.
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\InvoiceInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}