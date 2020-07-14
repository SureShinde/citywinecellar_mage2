<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magestore\Webpos\Plugin\Inventory\Model\Stock\Command;


/**
 * Prevent process of source links related to location.
 * Class PreventProcessForStockLocation
 * @package Magestore\Webpos\Plugin\InventoryAdminUi\Stock\StockSaveProcessor
 */
class PreventDeleteStockLocation
{
    public function __construct(
        \Magestore\Webpos\Api\Location\LocationRepositoryInterface $locationRepository
    )
    {
        $this->locationRepository = $locationRepository;
    }

    /**
     * Prevent delete stock related to location.
     *
     * @param \Magento\Inventory\Model\Stock\Command\DeleteByIdInterface $subject
     * @param int $stockId
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecute($subject, $stockId)
    {
        if ($stockId) {
            $countLocation = $this->locationRepository->getCountLocationByStockId($stockId);
            if ($countLocation > 0) {
                throw new \Magento\Framework\Exception\CouldNotDeleteException(
                    __('Stock is assigned to at least one POS Location could not be deleted.')
                );
            }
        }
    }
}
