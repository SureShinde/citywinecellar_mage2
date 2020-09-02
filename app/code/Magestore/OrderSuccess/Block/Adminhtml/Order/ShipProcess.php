<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Block\Adminhtml\Order;

use Magestore\OrderSuccess\Api\Data\ShippingChanelInterface;

/**
 * Class ShipProcess
 * @package Magestore\OrderSuccess\Block\Adminhtml\Sales
 */
class ShipProcess extends \Magento\Shipping\Block\Adminhtml\Order\Packaging
{
    /**
     * Source size model
     *
     * @var \Magento\Shipping\Model\Carrier\Source\GenericInterface
     */
    protected $_sourceSizeModel;

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
     * @var ShippingChanelInterface
     */
    protected $shippingChanelInterface;

    /**
     * ShipProcess constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Shipping\Model\Carrier\Source\GenericInterface $sourceSizeModel
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Shipping\Model\CarrierFactory $carrierFactory
     * @param \Magento\Sales\Model\Order\ShipmentFactory $shipmentFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param ShippingChanelInterface $shippingChanelInterface
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
        ShippingChanelInterface $shippingChanelInterface,
        array $data = []
    ) {
        parent::__construct($context, $jsonEncoder, $sourceSizeModel, $coreRegistry,
                            $carrierFactory, $data);
        $this->shipmentFactory = $shipmentFactory;
        $this->orderRepository = $orderRepository;
        $this->shippingChanelInterface = $shippingChanelInterface;
    }

    /**
     * Retrieve shipment model instance
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getShipment()
    {
        $shipment = false;
        if($this->getOrderId()) {
            $order = $this->getOrder();
            $shipment = $this->shipmentFactory->create($order, $this->getOrderItemData($order), []);
        }
        return $shipment;
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
     * Get order position
     *
     * @return int
     */
    public function getOrderPosition()
    {
        $orderPosition = $this->getRequest()->getParam('order_position');
        return $orderPosition;
    }

    /**
     * Retrieve shipment model instance
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getOrder()
    {
        $order = false;
        $orderId = $this->getOrderId();
        if($orderId) {
            $order = $this->orderRepository->get($orderId);
        }
        return $order;
    }

    /**
     * Check whether girth is allowed for current carrier
     *
     * @return bool
     */
    public function isGirthAllowed()
    {
        return false;
    }

    /**
     * get Shipping Channels
     *
     * @return array
     */
    public function getContainers()
    {
        return $this->shippingChanelInterface->getOptionArray();
    }

    /**
     * Return content types
     *
     * @return array
     */
    public function getContentTypes()
    {
        return [];
    }

    /**
     * Configuration for popup window for packaging
     *
     * @return string
     */
    public function getConfigDataJson()
    {
//        $orderId = $this->getRequest()->getParam('order_id');
//        $itemsQty = [];
//        $itemsPrice = [];
//        $itemsName = [];
//        $itemsWeight = [];
//        $itemsProductId = [];
//        $itemsOrderItemId = [];
        $createLabelUrl = $this->getUrl('ordersuccess/shipprocess/save');
        $itemsGridUrl = $this->getUrl('ordersuccess/shipprocess/getItemsGrid');

//        if ($orderId) {
//            foreach ($this->getShipment()->getAllItems() as $item) {
//                $itemsQty[$item->getOrderItemId()] = $item->getQty() * 1;
//                $itemsPrice[$item->getOrderItemId()] = $item->getPrice();
//                $itemsName[$item->getOrderItemId()] = $item->getName();
//                $itemsWeight[$item->getOrderItemId()] = $item->getWeight();
//                $itemsProductId[$item->getOrderItemId()] = $item->getProductId();
//                $itemsOrderItemId[$item->getOrderItemId()] = $item->getOrderItemId();
//            }
//        }
        $data = [
            'parentGrid' => 'os_needship_listing.os_needship_listing_data_source',
            'createLabelUrl' => $createLabelUrl,
            'itemsGridUrl' => $itemsGridUrl,
            'errorQtyOverLimit' => __(
                'You are trying to add a quantity for some products that doesn\'t match the quantity that was shipped.'
            ),
            'titleDisabledSaveBtn' => __('Products should be added to package(s)'),
            'validationErrorMsg' => __('The value you entered is not valid.'),
//            'shipmentItemsQty' => $itemsQty,
//            'shipmentItemsPrice' => $itemsPrice,
//            'shipmentItemsName' => $itemsName,
//            'shipmentItemsWeight' => $itemsWeight,
//            'shipmentItemsProductId' => $itemsProductId,
//            'shipmentItemsOrderItemId' => $itemsOrderItemId,
            'customizable' => [],
        ];
        return $this->_jsonEncoder->encode($data);
    }

}
