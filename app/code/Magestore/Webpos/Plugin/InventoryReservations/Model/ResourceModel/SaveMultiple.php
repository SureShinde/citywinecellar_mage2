<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Plugin\InventoryReservations\Model\ResourceModel;


class SaveMultiple
{
    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Inventory\Stock\Item
     */
    protected $stockItemResource;

    /**
     * SaveMultiple constructor.
     * @param \Magestore\Webpos\Model\ResourceModel\Inventory\Stock\Item $stockItemResource
     */
    public function __construct(
        \Magestore\Webpos\Model\ResourceModel\Inventory\Stock\Item $stockItemResource
    )
    {
        $this->stockItemResource = $stockItemResource;
    }

    /**
     * @param \Magento\InventoryReservations\Model\ResourceModel\SaveMultiple $subject
     * @param void $result
     * @param \Magento\InventoryReservationsApi\Model\ReservationInterface[] $reservations
     * @return void
     */
    public function afterExecute(
        $subject,
        $result = null,
        array $reservations
    )
    {
        $skus = [];
        foreach ($reservations as $reservation) {
            $skus[] = $reservation->getSku();
        }
        if (!empty($skus)) {
            $this->stockItemResource->updateUpdatedTimeBySku($skus);
        }
    }
}