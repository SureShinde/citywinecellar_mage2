<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Block\Adminhtml\PackRequest;

class Packaging extends \Magento\Shipping\Block\Adminhtml\Order\Packaging
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @var \Magento\Shipping\Model\CarrierFactory
     */
    protected $_carrierFactory;

    /**
     * @var \Magento\Sales\Model\Order\ShipmentFactory
     */
    protected $shipmentFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magestore\FulfilSuccess\Service\PackRequest\PackRequestService
     */
    protected $packRequestService;

    /**
     * @var \Magestore\FulfilSuccess\Api\PackRequestOrderItemRepositoryInterface
     */
    protected $packRequestOrderItemRepository;

    /**
     * Packaging constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Shipping\Model\Carrier\Source\GenericInterface $sourceSizeModel
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Shipping\Model\CarrierFactory $carrierFactory
     * @param \Magento\Sales\Model\Order\ShipmentFactory $shipmentFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magestore\FulfilSuccess\Service\PackRequest\PackRequestService $packRequestService
     * @param \Magestore\FulfilSuccess\Api\PackRequestOrderItemRepositoryInterface $packRequestOrderItemRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Shipping\Model\Carrier\Source\GenericInterface $sourceSizeModel,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Shipping\Model\CarrierFactory $carrierFactory,
        \Magento\Sales\Model\Order\ShipmentFactory $shipmentFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magestore\FulfilSuccess\Service\PackRequest\PackRequestService $packRequestService,
        \Magestore\FulfilSuccess\Api\PackRequestOrderItemRepositoryInterface $packRequestOrderItemRepository,
        array $data = []
    )
    {
        parent::__construct($context, $jsonEncoder, $sourceSizeModel, $coreRegistry,
            $carrierFactory, $data);
        $this->shipmentFactory = $shipmentFactory;
        $this->orderRepository = $orderRepository;
        $this->packRequestService = $packRequestService;
        $this->packRequestOrderItemRepository = $packRequestOrderItemRepository;
    }

    /**
     * Configuration for popup window for packaging
     *
     * @return string
     */
    public function getConfigDataJson()
    {
        $packRequestId = $this->getPackRequestId();
        $orderId = $this->getOrderId();
        $urlParams = [];

        $itemsQty = [];
        $itemsPrice = [];
        $itemsName = [];
        $itemsWeight = [];
        $itemsProductId = [];
        $itemsOrderItemId = [];

        if ($packRequestId) {
            $urlParams['pack_request_id'] = $packRequestId;
            $urlParams['order_id'] = $orderId;
            $createLabelUrl = $this->getUrl('fulfilsuccess/packRequest_packaging/save', $urlParams);
            $itemsGridUrl = $this->getUrl('fulfilsuccess/packRequest_packaging/getItemsGrid', $urlParams);

//            foreach ($this->getShipment()->getAllItems() as $item) {
            foreach ($this->packRequestService->getPackedItemsCollection($packRequestId) as $item) {
                $itemsQty[$item->getOrderItemId()] = $item->getQty() * 1;
                $itemsPrice[$item->getOrderItemId()] = $item->getPrice();
                $itemsName[$item->getOrderItemId()] = $item->getName();
                $itemsWeight[$item->getOrderItemId()] = $item->getWeight();
                $itemsProductId[$item->getOrderItemId()] = $item->getProductId();
                $itemsOrderItemId[$item->getOrderItemId()] = $item->getOrderItemId();
            }
        }

        $data = [
            'createLabelUrl' => $createLabelUrl,
            'itemsGridUrl' => $itemsGridUrl,
            'errorQtyOverLimit' => __(
                'You are trying to add a quantity for some products that doesn\'t match the quantity that was shipped.'
            ),
            'titleDisabledSaveBtn' => __('Products should be added to package(s)'),
            'validationErrorMsg' => __('The value that you entered is not valid.'),
            'shipmentItemsQty' => $itemsQty,
            'shipmentItemsPrice' => $itemsPrice,
            'shipmentItemsName' => $itemsName,
            'shipmentItemsWeight' => $itemsWeight,
            'shipmentItemsProductId' => $itemsProductId,
            'shipmentItemsOrderItemId' => $itemsOrderItemId,
            'customizable' => $this->_getCustomizableContainers(),
            'packRequestId' => $packRequestId,
            'reloadViewDetailUrl' => $this->_urlBuilder->getUrl('*/*/getInfo'),
            'modalId' => 'pack_request_detail_holder',
            'recent_packed_listing' => 'os_fulfilsuccess_recent_packed_listing.recent_packed_listing_data_source',
            'pack_request_listing' => 'os_fulfilsuccess_packrequest_listing.fulfilsuccess_packrequest_listing_data_source'
        ];
        return $this->_jsonEncoder->encode($data);
    }

    public function getWarehouseId()
    {
        return $this->getRequest()->getParam('warehouse_id');
    }

    public function getPackRequestId()
    {
        return $this->getRequest()->getParam('pack_request_id');
    }

    /**
     * is auto load
     *
     * @return boolean
     */
    public function isAutoLoad()
    {
        return $this->getRequest()->getParam('autoload') ?: 0;
    }

    /**
     * Retrieve available order
     *
     * @return \Magento\Sales\Model\Order
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getOrder()
    {
        if ($this->hasOrder()) {
            return $this->getData('order');
        }
        if ($this->_coreRegistry->registry('current_order')) {
            return $this->_coreRegistry->registry('current_order');
        }
        if ($this->_coreRegistry->registry('order')) {
            return $this->_coreRegistry->registry('order');
        }
        throw new \Magento\Framework\Exception\LocalizedException(__('We can\'t get the order instance right now.'));
    }

    /**
     * Get order id
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->getOrder()->getId();
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

    public function isCreateShippingLabel()
    {
        return true;
    }
}