<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Api;

/**
 * @api
 */
interface GuestHighlightManagementInterface
{
    /**#@+
     * Values of $page argument.
     */
    const PAGE_PRODUCT = 0;
    const PAGE_CART = 1;
    const PAGE_CHECKOUT = 2;
    /**#@-*/

    /**
     * @param int $page
     *
     * @return \Amasty\Rewards\Api\Data\HighlightInterface|null
     */
    public function getHighlight($page);

    /**
     * @param int $page
     *
     * @return bool
     *
     * @throws \InvalidArgumentException
     */
    public function isVisible($page);
}
