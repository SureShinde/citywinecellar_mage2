<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilSuccess\Block\Adminhtml\PackRequest\Packaging;

class Grid extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'packRequest/packaging/grid.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Sales\Model\Order\ShipmentFactory
     */
    protected $shipmentFactory;

    /**
     * @var \Magestore\FulfilSuccess\Service\PackRequest\PackRequestService
     */
    protected $packRequestService;

    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Model\Order\ShipmentFactory $shipmentFactory
     * @param \Magestore\FulfilSuccess\Service\PackRequest\PackRequestService $packRequestService
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\Order\ShipmentFactory $shipmentFactory,
        \Magestore\FulfilSuccess\Service\PackRequest\PackRequestService $packRequestService,
        array $data = []
    )
    {
        $this->_coreRegistry = $registry;
        $this->orderRepository = $orderRepository;
        $this->shipmentFactory = $shipmentFactory;
        $this->packRequestService = $packRequestService;
        parent::__construct($context, $data);
    }

    /**
     * Return collection of shipment items
     *
     * @return array
     */
    public function getCollection()
    {
        $items = [];
        /** @var \Magento\Sales\Model\ResourceModel\Order\Item\Collection $collection */
        $collection = $this->packRequestService->getPackedItemsCollection($this->getPackRequestId());

        foreach ($collection as $item) {
            if (!$item->isDeleted()) {
                $items[] = $item;
            }
        }

        return $items;
    }

    public function getPackRequestId()
    {
        return $this->getRequest()->getParam('pack_request_id');
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
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        $orderId = $this->getOrderId();
        $order = $this->orderRepository->get($orderId);
        return $order;
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
     * Can display customs value
     *
     * @return bool
     */
    public function displayCustomsValue()
    {
        $storeId = $this->getShipment()->getStoreId();
        $order = $this->getShipment()->getOrder();
        $address = $order->getShippingAddress();
        $shipperAddressCountryCode = $this->_scopeConfig->getValue(
            \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_COUNTRY_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $recipientAddressCountryCode = $address->getCountryId();
        if ($shipperAddressCountryCode != $recipientAddressCountryCode) {
            return true;
        }
        return false;
    }

    /**
     * Format price
     *
     * @param   float $value
     * @return  string
     */
    public function formatPrice($value)
    {
        return sprintf('%.2F', $value);
    }
}
