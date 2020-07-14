<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\CatalogRule\Product;

/**
 * Interface PriceIdsSearchResultsInterface
 * @package Magestore\Webpos\Api\Data\CatalogRule\Product
 */
interface PriceIdsSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get product price list.
     *
     * @api
     * @return int[]|null
     */
    public function getItems();

    /**
     * Set product price list.
     *
     * @api
     * @param int[] $items
     * @return PriceSearchResultsInterface
     */
    public function setItems(array $items);
}
