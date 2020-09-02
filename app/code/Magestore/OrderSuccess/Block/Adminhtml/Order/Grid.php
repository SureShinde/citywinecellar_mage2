<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Block\Adminhtml\Order;

/**
 * Class Grid
 * @package Magestore\OrderSuccess\Block\Adminhtml\Sales
 */
class Grid extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'order/grid.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Sales\Model\Order\Shipment\ItemFactory
     */
    protected $_shipmentItemFactory;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Sales\Model\Order\ShipmentFactory
     */
    protected $shipmentFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var string
     */
    protected $resourceName;

    /**
     * @var \Magestore\OrderSuccess\Helper\Data
     */
    protected $helperData;

    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Sales\Model\Order\Shipment\ItemFactory $shipmentItemFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Model\Order\ShipmentFactory $shipmentFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magestore\OrderSuccess\Helper\Data $helperData
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Sales\Model\Order\Shipment\ItemFactory $shipmentItemFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\Order\ShipmentFactory $shipmentFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magestore\OrderSuccess\Helper\Data $helperData,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_shipmentItemFactory = $shipmentItemFactory;
        $this->_coreRegistry = $registry;
        $this->shipmentFactory = $shipmentFactory;
        $this->orderRepository = $orderRepository;
        $this->helperData = $helperData;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $data);
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
            $collection = $this->getShipment()->getAllItems();
        }
        return $collection;
    }

    /**
     * check resource
     *
     * @return boolean
     */
    public function checkResource()
    {
        return false;
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
        return [];
    }

    /**
     * Retrieve shipment model instance
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getShipment()
    {
        $order = $this->getOrder();
        return $this->shipmentFactory->create($order, $this->getOrderItemData($order), []);
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    public function getOrderItemData(\Magento\Sales\Model\Order $order) {
        $data = [];
        foreach ($order->getAllItems() as $item) {
            $data[$item->getId()] = $item->getQtyToShip();
        }
        return $data;
    }

    /**
     * Get order id
     *
     * @return int
     */
    public function getOrderId()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        return $orderId;
    }

    /**
     * Retrieve shipment model instance
     *
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function getOrder()
    {
        $orderId = $this->getOrderId();
        $order = $this->orderRepository->get($orderId);
        return $order;
    }

    /**
     * get param
     *
     * @param   string
     * @return  mixed
     */
    public function getParam($param)
    {
        return $this->getRequest()->getParam($param);
    }

    /**
     * check packed item
     *
     * @param   string
     * @return  mixed
     */
    public function checkSelectedItem($item)
    {
        $itemIds = $this->getParam('packed_items');
        $itemIds = explode(',', $itemIds);
        if(count($itemIds)){
            if(in_array($item->getData('order_item_id'), $itemIds)){
                return true;
            }
        }
        return false;

    }
}
