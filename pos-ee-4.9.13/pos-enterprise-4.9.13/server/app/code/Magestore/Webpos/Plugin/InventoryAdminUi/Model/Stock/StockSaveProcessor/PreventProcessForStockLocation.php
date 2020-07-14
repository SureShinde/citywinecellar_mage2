<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magestore\Webpos\Plugin\InventoryAdminUi\Model\Stock\StockSaveProcessor;


/**
 * Prevent process of source links related to location.
 * Class PreventProcessForStockLocation
 * @package Magestore\Webpos\Plugin\InventoryAdminUi\Stock\StockSaveProcessor
 */
class PreventProcessForStockLocation
{
    public function __construct(
        \Magestore\Webpos\Api\Location\LocationRepositoryInterface $locationRepository
    )
    {
        $this->locationRepository = $locationRepository;
    }

    /**
     * Prevent process of source links related to location.
     *
     * @param \Magento\InventoryAdminUi\Model\Stock\StockSourceLinkProcessor $subject
     * @param int $stockId
     * @param array $linksData
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeProcess($subject, $stockId, $linksData)
    {
        if ($stockId) {
            $countLocation = $this->locationRepository->getCountLocationByStockId($stockId);
            if ($countLocation > 0) {
                if (empty($linksData)) {
                    throw new \Magento\Framework\Exception\CouldNotSaveException(
                        __('You cannot let a Stock linked to a POS Location with no assigned Source. Please choose a Source.')
                    );
                } else if (count($linksData) > 1) {
                    throw new \Magento\Framework\Exception\CouldNotSaveException(
                        __('The Stock is already linked to a POS Location. You can assign only one Source to the Stock.')
                    );
                }
            }
        }
    }
}
