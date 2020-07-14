<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Api\Data\Staff;

/**
 * Interface RoleSearchResultsInterface
 * @package Magestore\Appadmin\Api\Data\Staff
 */
interface RoleSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get items.
     *
     * @return \Magestore\Appadmin\Api\Data\Staff\RoleInterface[] Array of collection items
     */
    public function getItems();

    /**
     * Set items.
     *
     * @param \Magestore\Appadmin\Api\Data\Staff\RoleInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
