<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Integration;

interface GiftcardManagementInterface{
    /**
     * @param \Magestore\Webpos\Api\Data\Checkout\QuoteInterface $quote
     * @param string $giftcode
     * @param string[] $existed_codes
     * @return \Magestore\Webpos\Api\Data\Integration\Giftcard\GiftcardResponseInterface
     */
    public function apply(\Magestore\Webpos\Api\Data\Checkout\QuoteInterface $quote, $giftcode = null, $existed_codes = []);
    
    /**
     * @param int $customer_id
     * @return \Magestore\Webpos\Api\Data\Integration\Giftcard\GiftcardResponseInterface
     */
    public function getGiftcardByCustomer($customer_id = null);
}
