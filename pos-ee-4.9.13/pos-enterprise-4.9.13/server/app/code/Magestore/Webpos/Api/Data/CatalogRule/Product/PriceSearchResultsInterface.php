<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\CatalogRule\Product;

/**
 * Interface PriceSearchResultsInterface
 * @package Magestore\Webpos\Api\Data\CatalogRule\Product
 */
interface PriceSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get product price list.
     *
     * @api
     * @return \Magestore\Webpos\Api\Data\CatalogRule\Product\PriceInterface[]
     */
    public function getItems();

    /**
     * Set product price list.
     *
     * @api
     * @param \Magestore\Webpos\Api\Data\CatalogRule\Product\PriceInterface[] $items
     * @return PriceSearchResultsInterface
     */
    public function setItems(array $items);
}
