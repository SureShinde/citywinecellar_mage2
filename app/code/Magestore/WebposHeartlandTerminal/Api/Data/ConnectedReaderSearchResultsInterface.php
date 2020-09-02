<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposHeartlandTerminal\Api\Data;

/**
 * @api
 */
interface ConnectedReaderSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get items.
     *
     * @return \Magestore\WebposHeartlandTerminal\Api\Data\ConnectedReaderInterface[] Array of collection items
     */
    public function getItems();

    /**
     * Set items.
     *
     * @param \Magestore\WebposHeartlandTerminal\Api\Data\ConnectedReaderInterface[] $items
     * @return ConnectedReaderSearchResultsInterface
     */
    public function setItems(array $items = null);
}
