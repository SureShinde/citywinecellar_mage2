<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Api;

interface CheckoutHighlightManagementInterface
{
    /**#@+
     * Defined $page values
     */
    const CART = 'cart';
    const CHECKOUT = 'checkout';
    /**#@-*/

    /**
     * Return data array should be display as part of Highlight Caption
     *
     * @return array
     */
    public function getHighlightData();

    /**
     * Fill data into object.
     * Used for extension attributes only.
     *
     * @return \Amasty\Rewards\Api\Data\HighlightInterface
     */
    public function fillData();

    /**
     * @param int $customerId
     *
     * @return \Amasty\Rewards\Api\Data\HighlightInterface
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getHighlightByCustomerId($customerId);

    /**
     * Return setting value about highlight visibility
     * on $page from Stores -> Configuration.
     *
     * @param string $page
     *
     * @return bool
     */
    public function isVisible($page);
}
