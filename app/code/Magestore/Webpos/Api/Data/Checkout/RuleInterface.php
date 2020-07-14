<?php

namespace Magestore\Webpos\Api\Data\Checkout;

interface RuleInterface extends \Magento\SalesRule\Api\Data\RuleInterface {
    const VALID_ITEM_IDS = 'valid_item_ids';

    /**
     * Get ValidItemIds
     *
     * @return float[]|null
     */
    public function getValidItemIds();
    /**
     * Set ValidItemIds
     *
     * @param float[]|null $validItemIds
     * @return RuleInterface
     */
    public function setValidItemIds($validItemIds);

}