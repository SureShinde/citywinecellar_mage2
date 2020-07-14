<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposStripeTerminal\Api\Data;

/**
 * @api
 */
interface ConnectedReaderSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get items.
     *
     * @return \Magestore\WebposStripeTerminal\Api\Data\ConnectedReaderInterface[] Array of collection items
     */
    public function getItems();

    /**
     * Set items.
     *
     * @param \Magestore\WebposStripeTerminal\Api\Data\ConnectedReaderInterface[] $items
     * @return ConnectedReaderSearchResultsInterface
     */
    public function setItems(array $items = null);
}
