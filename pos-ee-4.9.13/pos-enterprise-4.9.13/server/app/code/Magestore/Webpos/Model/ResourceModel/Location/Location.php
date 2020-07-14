<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\ResourceModel\Location;

class Location extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {
    protected function _construct()
    {
        $moduleManager = \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Module\Manager::class);
        if(!$moduleManager->isEnabled('Magestore_InventorySuccess')){
            $this->_init('webpos_location', 'location_id');
        } else {
            $this->_init('os_warehouse', 'warehouse_id');
        }
    }
}