<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;


interface DropshipRequestSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get dropship requests list.
     *
     * @return \Magestore\DropshipSuccess\Api\Data\DropshipRequestInterface[]
     */
    public function getItems();

    /**
     * Set dropship requests list.
     *
     * @param \Magestore\DropshipSuccess\Api\Data\DropshipRequestInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
