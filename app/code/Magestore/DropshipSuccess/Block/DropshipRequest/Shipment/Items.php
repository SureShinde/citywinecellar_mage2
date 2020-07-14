<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */


namespace Magestore\DropshipSuccess\Block\DropshipRequest\Shipment;

/**
 * Class Items
 * @package Magestore\DropshipSuccess\Block\DropshipRequest\Shipment
 */
/**
 * Class Items
 * @package Magestore\DropshipSuccess\Block\DropshipRequest\Shipment
 */
class Items extends \Magento\Shipping\Block\Items
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory
     */
    protected $shipmentCollectionFactory;

    /**
     * @var \Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\DropshipShipment\CollectionFactory
     */
    protected $dropshipShipmentCollectionFactory;

    /**
     * Items constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory
     * @param \Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\DropshipShipment\CollectionFactory $dropshipShipmentCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory,
        \Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\DropshipShipment\CollectionFactory $dropshipShipmentCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $data);
        $this->shipmentCollectionFactory = $shipmentCollectionFactory;
        $this->dropshipShipmentCollectionFactory = $dropshipShipmentCollectionFactory;
    }

    /**
     * @return \Magento\Sales\Model\ResourceModel\Order\Shipment\Collection
     */
    public function getShipmentsCollection()
    {
        $dropshipRequestId = $this->getRequest()->getParam('dropship_id');
        /** @var \Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\DropshipShipment\Collection $dropshipShipmentCollection */
        $dropshipShipmentCollection = $this->dropshipShipmentCollectionFactory->create();
        $dropshipShipmentCollection->addFieldToFilter('dropship_request_id', $dropshipRequestId);
        $shipmentIds = $dropshipShipmentCollection->getColumnValues('shipment_id');
        /** @var \Magento\Sales\Model\ResourceModel\Order\Shipment\Collection $shipmentCollection */
        $shipmentCollection = $this->shipmentCollectionFactory->create()
            ->addFieldToFilter('entity_id', ['in' => $shipmentIds]);
        return $shipmentCollection;
    }

    /**
     * Retrieve rendered item html content
     *
     * @param \Magento\Framework\DataObject $item
     * @return string
     */
    public function getItemHtml(\Magento\Framework\DataObject $item)
    {
        if ($item->getOrderItem()) {
            $type = $item->getOrderItem()->getProductType();
        } else {
            $type = $item->getProductType();
        }

        return $this->getItemRenderer($type)->setItem($item)->toHtml();
    }

    /**
     * Retrieve item renderer block
     *
     * @param string $type
     * @return \Magento\Framework\View\Element\AbstractBlock
     * @throws \RuntimeException
     */
    public function getItemRenderer($type)
    {
        /** @var $renderer \Magento\Sales\Block\Adminhtml\Items\AbstractItems */
        $renderer = $this->getChildBlock($type) ?: $this->getChildBlock(self::DEFAULT_TYPE);
        if (!$renderer instanceof \Magento\Framework\View\Element\BlockInterface) {
            throw new \RuntimeException('Renderer for type "' . $type . '" does not exist.');
        }
        $renderer->setColumnRenders($this->getLayout()->getGroupChildNames($this->getNameInLayout(), 'column'));
        return $renderer;
    }

    /**
     * @param object $shipment
     * @return string
     */
    public function getPrintShipmentUrl($shipment)
    {
//        return $this->getUrl('sales/order/printShipment', ['shipment_id' => $shipment->getId()]);
        return $this->getUrl('dropship/dropshipRequest/printShipment', ['shipment_id' => $shipment->getId()]);
    }

    /**
     * get dropship request status
     * @return null|string
     */
    public function getStatus()
    {
        return $this->_coreRegistry->registry('current_dropship_request')->getStatus();
    }

    /**
     * view shipment url
     * @return string
     */
    public function getOrderItemUrl()
    {
        return $this->getUrl('dropship/dropshipRequest/viewDropship', ['dropship_id' => $this->getRequest()->getParam('dropship_id')]);
    }
}
