<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Api\Data;

/**
 * Pack Request search result interface.
 *
 * @api
 */
interface PackRequestSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get items.
     *
     * @return \Magestore\FulfilSuccess\Api\Data\PackRequestInterface[] Array of collection items.
     */
    public function getItems();

    /**
     * Set items.
     *
     * @param \Magestore\FulfilSuccess\Api\Data\PackRequestInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
