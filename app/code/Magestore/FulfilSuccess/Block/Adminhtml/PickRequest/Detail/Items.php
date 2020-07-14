<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Block\Adminhtml\PickRequest\Detail;

use Magestore\FulfilSuccess\Api\Data\PickRequestInterface;
use Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface;

class Items extends \Magento\Sales\Block\Adminhtml\Order\View\Items
{
    /**
     * @var \Magestore\FulfilSuccess\Api\OrderItemRepositoryInterface
     */
    protected $orderItemRepository;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magestore\FulfilSuccess\Helper\Data
     */
    protected $helper;

    /**
     * Items constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\Framework\Registry $registry
     * @param \Magestore\FulfilSuccess\Api\OrderItemRepositoryInterface $orderItemRepository
     * @param \Magestore\FulfilSuccess\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magestore\FulfilSuccess\Api\OrderItemRepositoryInterface $orderItemRepository,
        \Magestore\FulfilSuccess\Helper\Data $helper,
        array $data = [])
    {
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $data);
        $this->orderItemRepository = $orderItemRepository;
        $this->coreRegistry = $registry;
        $this->helper = $helper;
    }

    /**
     * Retrieve order items collection
     *
     * @return \Magento\Sales\Model\ResourceModel\Order\Item\Collection
     */
    public function getItemsCollection()
    {
        if($this->isPickedRequest()){
            $itemCollection = $this->orderItemRepository
                ->getPickedItemsCollection($this->getParentBlock()->getPickRequestId());
        }else{
            /** @var \Magento\Sales\Model\ResourceModel\Order\Item\Collection $itemCollection */
            $itemCollection = $this->orderItemRepository
                ->getItemsCollection($this->getParentBlock()->getPickRequestId());
        }
        foreach ($itemCollection as $item){
            $barcodes = $this->helper->getProductBarcodes($item->getData(PickRequestItemInterface::PRODUCT_ID));
            if(!empty($barcodes)){
                $item->setData(PickRequestItemInterface::ITEM_BARCODE, $barcodes);
            }
        }
        
        return $itemCollection;
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