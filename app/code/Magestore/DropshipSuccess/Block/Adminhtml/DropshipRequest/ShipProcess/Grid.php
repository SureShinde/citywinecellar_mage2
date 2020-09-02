<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Block\Adminhtml\DropshipRequest\ShipProcess;

/**
 * Class Grid
 * @package Magestore\DropshipSuccess\Block\Adminhtml\DropshipRequest\ShipProcess
 */
class Grid extends \Magestore\OrderSuccess\Block\Adminhtml\Order\Grid
{

    /**
     * @var string
     */
    protected $_template = 'dropshiprequest/shipprocess/grid.phtml';

    /**
     * @var string
     */
    protected $_resourceName = 'Supplier';

    /**
     * @var array
     */
    protected $suppliers = [];

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
        return __('Supplier To Dropship');
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
        $this->loadAvailableSuppliers();

        return parent::_prepareLayout();
    }

    /**
     * Return collection of shipment items
     *
     * @return array
     */
    public function getCollection()
    {
        if ($this->getShipment()->getId()) {
            $collection = $this->_shipmentItemFactory->create()->getCollection()->setShipmentFilter(
                $this->getShipment()->getId()
            );
        } else {
            $itemCollection = $this->getShipment()->getAllItems();
            $collection = [];
            /** @var \Magestore\SupplierSuccess\Service\SupplierService $supplierService */
            $supplierService = $this->_objectManager->create(
                'Magestore\SupplierSuccess\Service\SupplierService'
            );
            foreach ($itemCollection as $col) {
                $productId = $col->getProductId();
                /** @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Collection $supplierProducts */
                $supplierProducts = $supplierService->getSupplierByProductId($productId);
                if ($supplierProducts->getSize()) {
                    $collection[] = $col;
                }
            }
        }
        return $collection;
    }

    /**
     * Get list of availlabel Suppliers
     *
     * @return array
     */
    public function loadAvailableSuppliers()
    {
        if (!count($this->suppliers)) {
            $productIds = [];
            $items = $this->getCollection();
            if (count($items)) {
                foreach ($items as $item) {
                    $productIds[$item->getOrderItemId()] = $item->getProductId();
                    if (!$childItems = $item->getOrderItem()->getChildrenItems()) {
                        continue;
                    }
                    if (!count($childItems)) {
                        continue;
                    }
                    foreach ($childItems as $childItem) {
                        $productIds[$childItem->getItemId()] = $childItem->getProductId();
                    }
                }
            }
            /** @var \Magestore\SupplierSuccess\Service\SupplierService $supplierService */
            $supplierService = $this->_objectManager->get(
                'Magestore\SupplierSuccess\Service\SupplierService'
            );
            $this->suppliers = $supplierService->getSuppliersToDropship($productIds);
            $this->prepareBundleItemWarehouse($items);
        }
        return $this->suppliers;
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
                $availableSuppliers = [];
                $suppliers = [];
                foreach ($orderItem->getChildrenItems() as $child) {
                    if (!isset($this->suppliers[$child->getItemId()])) {
                        continue;
                    }
                    foreach ($this->suppliers[$child->getItemId()] as $supplierId => $data) {
                        $suppliers[$supplierId] = $data['supplier'];
                    }
                    if (!count($availableSuppliers)) {
                        $availableSuppliers = array_keys($this->suppliers[$child->getItemId()]);
                    } else {
                        $availableSuppliers = array_intersect($availableSuppliers, array_keys($this->suppliers[$child->getItemId()]));
                    }
                }
                if (count($availableSuppliers)) {
                    foreach ($availableSuppliers as $supplierId) {
                        $this->suppliers[$orderItem->getItemId()][$supplierId] = [
                            'supplier' => $suppliers[$supplierId]
                        ];
                    }
                }

            }
            continue;
        }
    }

    /**
     * Get json of availlabel Suppliers
     *
     * @return string
     */
    public function getAvailableSuppliersJson()
    {
        return \Zend\Json\Json::encode($this->suppliers);
    }

    /**
     * Get available supplier of $item
     *
     * @param \Magento\Sales\Model\Order\Item $item
     * @return array
     */
    public function getAvailableSuppliers($item)
    {
        $productId = $this->getSimpleItemId($item);

        if (!isset($this->suppliers[$productId])) {
            return [];
        }
        return $this->suppliers[$productId];
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
        if ($this->helperData->checkModuleEnable('Magestore_InventorySuccess')) {
            $warehouseCollection = $this->_objectManager
                ->create('Magestore\InventorySuccess\Model\Warehouse\WarehouseStockRegistry')
                ->getStockWarehouses($productId);
            return $warehouseCollection;
        }
        return [];
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
     * Render child items of Sales Item
     *
     * @param \Magento\Sales\Model\Order\Item $orderItem
     * @return string
     */
    public function renderChildItems($orderItem)
    {
        $childItems = $this->getLayout()->createBlock('\Magestore\DropshipSuccess\Block\Adminhtml\DropshipRequest\ShipProcess\Renderer\ChildItems');
        $childItems->setItem($orderItem);
        $childItems->setParentBlock($this);
        return $childItems->toHtml();
    }
}
