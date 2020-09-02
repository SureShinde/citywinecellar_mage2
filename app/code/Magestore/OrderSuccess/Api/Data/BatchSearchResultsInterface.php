<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface BatchSearchResultsInterface
 * @package Magestore\OrderSuccess\Api\Data
 */
interface BatchSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get batchs list.
     *
     * @return \Magestore\OrderSuccess\Api\Data\BatchInterface[]
     */
    public function getItems();

    /**
     * Set batchs list.
     *
     * @param \Magestore\OrderSuccess\Api\Data\BatchInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
