<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Observer\Package;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;

class UpdateQtyToWarehouse implements ObserverInterface
{

    /**
     * @var Registry
     */
    protected $registry;

    public function __construct(
        Registry $registry
    ) {
        $this->registry = $registry;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer) 
    {
        if ($this->registry->registry('current_warehouse_id_packging')) {
            $warehouse = $observer->getEvent()->getWarehouse();
            $warehouseId = $this->registry->registry('current_warehouse_id_packging');
            $warehouse->setId($warehouseId);
        }
    }    
}