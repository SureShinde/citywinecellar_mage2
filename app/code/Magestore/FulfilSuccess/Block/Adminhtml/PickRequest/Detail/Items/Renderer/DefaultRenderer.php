<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Block\Adminhtml\PickRequest\Detail\Items\Renderer;

use Magestore\FulfilSuccess\Api\Data\PickRequestInterface;

class DefaultRenderer extends \Magestore\FulfilSuccess\Block\Adminhtml\AbstractRequest\Detail\Items\Renderer\DefaultRenderer
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * DefaultRenderer constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\GiftMessage\Helper\Message $messageHelper
     * @param \Magento\Checkout\Helper\Data $checkoutHelper
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Shipping\Model\CarrierFactory $carrierFactory
     * @param array $data
     */
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
        if($this->isPickedRequest()){
            return '<span>' . floatval($item->getPickedQty()) . '</span>';
        }else{
            return '<input class="text-center admin__control-text os_fulfilsuccess_input_picking_on_detail os_fulfilsuccess_input width-small os_fulfilsuccess_input_number_control not-negative" type="text" data-itemid="' . $item->getPickRequestItemId() . '" id="os_picked_items_' . $item->getPickRequestItemId() . '" name="picked[items][' . $item->getPickRequestItemId() . ']" value="' . floatval($item->getPickedQty()) . '" data-increment="1" data-max="' . floatval($item->getRequestQty()) . '"/>';
        }
    }

    /**
     * @return bool
     */
    public function isPickedRequest(){
        $pickRequest = $this->coreRegistry->registry('current_pick_request');
        if($pickRequest && $pickRequest->getId()){
            return ($pickRequest->getData(PickRequestInterface::STATUS) == PickRequestInterface::STATUS_PICKED)?true:false;
        }
        return false;
    }
}