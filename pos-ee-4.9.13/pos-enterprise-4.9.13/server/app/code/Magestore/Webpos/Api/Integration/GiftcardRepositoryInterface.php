<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Integration;


interface GiftcardRepositoryInterface
{
    /**
     * check if gift card product can refund
     *
     * @api
     * @param \Magestore\Webpos\Api\Data\Integration\Giftcard\GiftcardRefundRequestInterface[] $listGiftCardRefund
     * @return int
     */
    public function checkGiftCardRefund($listGiftCardRefund);
}
