<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Sales\OrderManagement;

use Magento\Sales\Api\Data\OrderInterface;

interface AppendReservationsAfterOrderPlacementInterface
{
    /**
     * Add reservation for order
     *
     * @param OrderInterface $order
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute($order);
}
