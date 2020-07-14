<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Block\Adminhtml\PackRequest\Detail\Items;

use Magestore\FulfilSuccess\Api\Data\PackRequestInterface;

class DefaultRenderer extends \Magestore\FulfilSuccess\Block\Adminhtml\AbstractRequest\Detail\Items\Renderer\DefaultRenderer
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magento\GiftMessage\Helper\Message $messageHelper,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Shipping\Model\CarrierFactory $carrierFactory,
        array $data = [])
    {
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $messageHelper, $checkoutHelper, $imageHelper, $carrierFactory, $data);
        $this->coreRegistry = $registry;
    }

    /**
     * @param \Magento\Framework\DataObject $item
     * @return string
     */
    public function displayPickedQty(\Magento\Framework\DataObject $item)
    {
        /** @var \Magento\Sales\Api\Data\OrderItemInterface $item */
        $itemId = $item->getItemId();
        if ($this->isPackedRequest()) {
            return '<span>' . floatval($item->getPackedQty()) . '</span>';
        } else {
            return '<input class="text-center admin__control-text qty-item os_fulfilsuccess_input_packing_on_detail os_fulfilsuccess_input width-small os_fulfilsuccess_input_number_control not-negative" type="text" data-itemid="' . $item->getPackRequestItemId() . '" id="os_packed_items_' . $item->getPackRequestItemId() . '" name="shipment[items][' . $itemId . ']" value="0" data-increment="1" data-max="' . floatval($item->getRequestQty() - $item->getPackedQty()) . '"/>';
        }
    }

    /**
     * @return bool
     */
    public function isPackedRequest()
    {
        $packRequest = $this->coreRegistry->registry('current_pack_request');
        if ($packRequest && $packRequest->getId()) {
            if (in_array($packRequest->getData(PackRequestInterface::STATUS),
                [PackRequestInterface::STATUS_PACKED, PackRequestInterface::STATUS_CANCELED])) {
                return true;
            }
//            return ($packRequest->getData(PackRequestInterface::STATUS) == PackRequestInterface::STATUS_PACKED) ? true : false;
        }
        return false;
    }
}