<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilSuccess\Block\Adminhtml\PickRequest\ShipProcess;

class Grid extends \Magestore\OrderSuccess\Block\Adminhtml\Order\Grid
{

    /**
     * @var string
     */
    protected $_template = 'pickRequest/shipprocess/grid.phtml';

    /**
     * @var string
     */
    protected $_resourceName = 'Warehouse';

    /**
     * @var array
     */
    protected $warehouses = [];

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
        return $this->_resourceName;
    }

    /**
     * Get resource title
     *
     * @return string
     */
    public function getResourceTitle()
    {
        /** @var \Magestore\FulfilSuccess\Api\FulfilManagementInterface $fulfilManagement */
        $fulfilManagement = $this->_objectManager->get('Magestore\FulfilSuccess\Api\FulfilManagementInterface');
        if ($fulfilManagement->isMSIEnable()) {
            return __('Source to Pick');
        }
        return __('Warehouse To Pick');
    }


    /**
     * Preparing global layout
     *
     * You can redefine this method in child classes for changing layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->loadAvailableWarehouses();

        return parent::_prepareLayout();
    }

    /**
     * Get list of availlabel Warehouses
     *
     * @return array
     */
    public function loadAvailableWarehouses()
    {
        if (!count($this->warehouses)) {
            $order = $this->getOrder();
            $productIds = [];
            $items = $this->getCollection();
            if (count($items)) {
                foreach ($items as $item) {
                    $productIds[$item->getOrderItemId()] = $item->getProductId();
                    if (!$childItems = $item->getOrderItem()->getChildrenItems()) {
                        continue;
                    }
                    if (empty($childItems)) {
                        continue;
                    }
                    foreach ($childItems as $childItem) {
                        $productIds[$childItem->getItemId()] = $childItem->getProductId();
                    }
                }
            }
            /** @var \Magestore\FulfilSuccess\Service\Warehouse\WarehouseServiceInterface $warehouseService */
            $warehouseService = $this->_objectManager
                ->get('Magestore\FulfilSuccess\Service\Warehouse\WarehouseServiceInterface');
            $this->warehouses = $warehouseService->getWarehousesToPick($productIds, $order);
            $this->prepareBundleItemWarehouse($items);
        }
        /* [$itemId => [$warehouseId => ['available_qty' => $qty, 'warehouse' => $warehouse]]] */
        return $this->warehouses;
    }

    /**
     * prepare warehouse list for bundle item
     *
     * @param array $items
     */
    public function prepareBundleItemWarehouse($items)
    {
        foreach ($items as $item) {
            $orderItem = $this->getOrder()->getItemById($item->getOrderItemId());
            if ($orderItem->getProductType() == \Magento\Bundle\Model\Product\Type::TYPE_CODE
                && !$orderItem->isShipSeparately()
            ) {
                $availableWarehouses = [];
                $warehouses = [];
                foreach ($orderItem->getChildrenItems() as $child) {
                    if (!isset($this->warehouses[$child->getItemId()])) {
                        continue;
                    }
                    foreach ($this->warehouses[$child->getItemId()] as $warehouseId => $data) {
                        $warehouses[$warehouseId] = $data['warehouse'];
                    }
                    if (empty($availableWarehouses)) {
                        $availableWarehouses = array_keys($this->warehouses[$child->getItemId()]);
                    } else {
                        $availableWarehouses = array_intersect($availableWarehouses, array_keys($this->warehouses[$child->getItemId()]));
                    }
                }
                if (!empty($availableWarehouses)) {
                    foreach ($availableWarehouses as $warehouseId) {
                        $this->warehouses[$orderItem->getItemId()][$warehouseId] = [
                            'available_qty' => 999999,
                            'warehouse' => $warehouses[$warehouseId]
                        ];
                    }
                }

            }
            continue;
        }
    }

    /**
     * Get json of availlabel Warehouses
     *
     * @return string
     */
    public function getAvailableWarehousesJson()
    {
        return \Zend\Json\Json::encode($this->warehouses);
    }

    /**
     * Get available warehouses of $item
     *
     * @param \Magento\Sales\Model\Order\Item $item
     * @return array
     */
    public function getAvailableWarehouses($item)
    {
        $productId = $this->getSimpleItemId($item);

        if (!isset($this->warehouses[$productId])) {
            return [];
        }
        return $this->warehouses[$productId];
    }

    /**
     * Get simple product_id from item
     *
     * @param \Magento\Sales\Model\Order\Item $item
     * @return int
     */
    public function getSimpleProductId($item)
    {
        $productId = $item->getProductId();
        if ($item->getProductType() == \Magento\Bundle\Model\Product\Type::TYPE_CODE) {
            return $productId;
        }
        if ($childItems = $item->getChildrenItems()) {
            foreach ($childItems as $childItem) {
                $productId = $childItem->getProductId();
            }
        }
        return $productId;
    }

    /**
     * Get id of simple item from order item
     *
     * @param \Magento\Sales\Model\Order\Item $item
     * @return int
     */
    public function getSimpleItemId($item)
    {
        $itemId = $item->getItemId();
        if ($item->getProductType() == \Magento\Bundle\Model\Product\Type::TYPE_CODE) {
            return $itemId;
        }
        if ($childItems = $item->getChildrenItems()) {
            foreach ($childItems as $childItem) {
                $itemId = $childItem->getItemId();
            }
        }
        return $itemId;
    }

    /**
     * get resource collection
     *
     * @return string
     */
    public function getResourceCollection($productId)
    {
        $warehouseCollection = $this->_objectManager
            ->create('Magestore\InventorySuccess\Model\Warehouse\WarehouseStockRegistry')
            ->getStockWarehouses($productId);
        return $warehouseCollection;
    }

    /**
     * check packed item
     *
     * @param   string
     * @return  mixed
     */
    public function checkSelectedItem($item)
    {
        return false;

    }

    /**
     * Render child items of Order Item
     *
     * @param \Magento\Sales\Model\Order\Item $orderItem
     * @return string
     */
    public function renderChildItems($orderItem)
    {
        $childItems = $this->getLayout()->createBlock('\Magestore\FulfilSuccess\Block\Adminhtml\PickRequest\ShipProcess\Renderer\ChildItems');
        $childItems->setItem($orderItem);
        $childItems->setParentBlock($this);
        return $childItems->toHtml();
    }
}
