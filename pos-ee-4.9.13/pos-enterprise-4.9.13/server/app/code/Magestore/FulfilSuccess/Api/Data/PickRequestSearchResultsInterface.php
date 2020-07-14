<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilSuccess\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;


interface PickRequestSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get batchs list.
     *
     * @return \Magestore\FulfilSuccess\Api\Data\PickRequestInterface[]
     */
    public function getItems();

    /**
     * Set batchs list.
     *
     * @param \Magestore\FulfilSuccess\Api\Data\PickRequestInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
