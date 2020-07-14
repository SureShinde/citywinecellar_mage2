<?php

namespace Magestore\Webpos\Api\Data\Integration\Giftcard;

interface GiftcardRefundRequestInterface {
    const ORDER_ITEM_ID = 'order_item_id';
    const QTY = 'qty';

    /**
     * @return int
     */
    public function getOrderItemId();

    /**
     * @param int $orderItemId
     * @return $this
     */
    public function setOrderItemId($orderItemId);

    /**
     * @return float
     */
    public function getQty();

    /**
     * @param float $qty
     * @return $this
     */
    public function setQty($qty);
}