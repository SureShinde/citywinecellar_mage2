<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Catalog\Option;

/**
 * Interface ConfigOptionsInterface
 */
interface SwatchOptionSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get attributes list.
     *
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\SwatchOptionInterface[]
     */
    public function getItems();

    /**
     * Set attributes list.
     *
     * @param \Magestore\Webpos\Api\Data\Catalog\Option\SwatchOptionInterface[] $items
     * @return SwatchOptionSearchResultsInterface
     */
    public function setItems(array $items);
}