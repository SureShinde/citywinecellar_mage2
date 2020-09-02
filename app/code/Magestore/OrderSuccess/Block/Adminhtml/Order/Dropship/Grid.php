<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Block\Adminhtml\Order\Dropship;

/**
 * Class Grid
 * @package Magestore\OrderSuccess\Block\Adminhtml\Sales\Dropship
 */
class Grid extends \Magestore\OrderSuccess\Block\Adminhtml\Order\Grid
{
    /**
     * @var string
     */
    protected $_template = 'order/dropship/grid.phtml';

    /**
     * @var string
     */
    protected $_resourceName = 'Suplier';

    /**
     * check resource
     *
     * @return boolean
     */
    public function checkResource()
    {
        return true;
    }

    /**
     * get resource name
     *
     * @return string
     */
    public function getResourceName()
    {
        return __($this->_resourceName);
    }

    /**
     * get resource collection
     *
     * @return string
     */
    public function getResourceCollection($productId)
    {
        if($this->helperData->checkModuleEnable('Magestore_InventorySuccess')){
            $warehouseCollection = $this->_objectManager
                ->create('Magestore\InventorySuccess\Model\Warehouse\WarehouseStockRegistry')
                ->getStockWarehouses($productId);
            return $warehouseCollection;
        }
        return [];
    }
    
}
