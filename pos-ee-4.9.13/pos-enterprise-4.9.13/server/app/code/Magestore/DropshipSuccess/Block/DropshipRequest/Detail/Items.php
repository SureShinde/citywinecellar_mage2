<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Block\DropshipRequest\Detail;

use Magestore\DropshipSuccess\Service\DropshipRequestService;

/**
 * Class Items
 * @package Magestore\DropshipSuccess\Block\DropshipRequest\Detail
 */
class Items extends \Magento\Sales\Block\Order\Items
{

    /**
     * @var DropshipRequestService
     */
    protected $dropshipRequestService;

    /**
     * Items constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param DropshipRequestService $dropshipRequestService
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        DropshipRequestService $dropshipRequestService,
        array $data = []
    ) {
        $this->dropshipRequestService = $dropshipRequestService;
        parent::__construct($context, $registry, $data);
    }

    /**
     * Retrieve order items collection
     *
     * @return \Magento\Sales\Model\ResourceModel\Order\Item\Collection
     */
    public function getItemsCollection()
    {
        if ($this->dropshipRequestService->isShippedRequest()) {
            /** @var \Magento\Sales\Model\ResourceModel\Order\Item\Collection $itemCollection */
            $itemCollection = $this->dropshipRequestService->getDropshipItemsViewCollection($this->getRequest()->getParam('dropship_id'));
        } else {
            /** @var \Magento\Sales\Model\ResourceModel\Order\Item\Collection $itemCollection */
            $itemCollection = $this->dropshipRequestService->getDropshipItemsCollection($this->getRequest()->getParam('dropship_id'));
        }
        return $itemCollection;
    }

    /**
     * @return bool
     */
    public function isShippedRequest()
    {
        return $this->dropshipRequestService->isShippedRequest();
    }

    /**
     * Return product type for quote/order item
     *
     * @param \Magento\Framework\DataObject $item
     * @return string
     */
    public function getItemType(\Magento\Framework\DataObject $item)
    {
        if ($item->getOrderItem()) {
            $type = $item->getOrderItem()->getProductType();
        } elseif ($item instanceof \Magento\Quote\Model\Quote\Address\Item) {
            $type = $item->getQuoteItem()->getProductType();
        } else {
            $type = $item->getProductType();
        }
        return $type;
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
    public function getShipmentUrl()
    {
        return $this->getUrl('dropship/dropshipRequest/viewShipment', ['dropship_id' => $this->getRequest()->getParam('dropship_id')]);
    }
}