<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Observer\InventorySuccess;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class GetCurrentWarehouseByStore implements ObserverInterface
{
    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $webposHelper;

    /**
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * GetDefaultScopeId constructor.
     * @param \Magestore\Webpos\Helper\Data $webposHelper
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magestore\Webpos\Helper\Data $webposHelper,
        \Magento\Framework\Registry $coreRegistry
    )
    {
        $this->webposHelper = $webposHelper;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Load linked Warehouse from Location of WebPOS Order
     *
     * @param EventObserver $observer
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(EventObserver $observer)
    {
        if (!$this->coreRegistry->registry('webpos_get_product_list') &&
            !$this->coreRegistry->registry('create_order_webpos')) {
            return $this;
        }

        $warehouse = $observer->getEvent()->getWarehouse();

        $locationId = $this->webposHelper->getCurrentLocationId();

        if (!$locationId) {
            return $this;
        }
        $warehouse->setId($locationId);
    }

}