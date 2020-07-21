<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Api;

interface CatalogHighlightManagementInterface
{
    /**
     * For product page only.
     *
     * @param int $productId
     * @param int $customerId
     * @param string|null $attributes
     *
     * @return \Amasty\Rewards\Api\Data\HighlightInterface
     */
    public function getHighlightForProduct($productId, $customerId, $attributes = null);

    /**
     * For category page only.
     *
     * @param int $productId
     * @param int $customerId
     * @param string|null $attributes
     *
     * @return \Amasty\Rewards\Api\Data\HighlightInterface
     */
    public function getHighlightForCategory($productId, $customerId, $attributes = null);
}
