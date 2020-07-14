<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposShipping\Api\Order;

interface ShippingServiceInterface
{

    /**
     * Create shipment
     *
     * @param string $requestIncrementId
     * @param string $order_increment_id
     * @param \Magestore\WebposShipping\Api\Data\Order\Shipment\ItemToShipInterface[]|null $items_to_ship
     * @param string|null $note
     * @param string[]|null $tracks
     * @return \Magestore\Webpos\Api\Data\Checkout\OrderInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createShipment(
        $requestIncrementId,
        $order_increment_id,
        $items_to_ship = [],
        $note = null,
        $tracks = null
    );

    /**
     * Process create shipment action log
     *
     * @param string $requestIncrementId
     * @return \Magestore\Webpos\Api\Data\Checkout\OrderInterface|bool
     */
    public function processCreateShipmentActionLog($requestIncrementId);

    /**
     * Cancel fulfill request
     *
     * @param string $order_id
     * @return \Magestore\Webpos\Api\Data\Checkout\OrderInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function tryCancelPickPackRequest($order_id);
}
