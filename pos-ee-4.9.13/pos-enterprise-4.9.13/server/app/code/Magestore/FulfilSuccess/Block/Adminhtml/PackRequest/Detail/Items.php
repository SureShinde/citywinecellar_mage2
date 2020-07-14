<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Block\Adminhtml\PackRequest\Detail;

use Magestore\FulfilSuccess\Api\Data\PackRequestInterface;
use Magestore\FulfilSuccess\Api\Data\PackRequestItemInterface;
use Magestore\FulfilSuccess\Service\PackRequest\PackRequestService;

class Items extends \Magento\Sales\Block\Adminhtml\Order\View\Items
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory
     */
    protected $itemsCollection;

    /**
     * @var \Magestore\FulfilSuccess\Api\PackRequestOrderItemRepositoryInterface
     */
    protected $packRequestOrderItemRepository;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magestore\FulfilSuccess\Helper\Data
     */
    protected $helper;

    /**
     * @var PackRequestService
     */
    protected $packRequestService;

    /**
     * Items constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $itemsCollection
     * @param \Magestore\FulfilSuccess\Api\PackRequestOrderItemRepositoryInterface $packRequestOrderItemRepository
     * @param \Magestore\FulfilSuccess\Helper\Data $helper
     * @param PackRequestService $packRequestService
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $itemsCollection,
        \Magestore\FulfilSuccess\Api\PackRequestOrderItemRepositoryInterface $packRequestOrderItemRepository,
        \Magestore\FulfilSuccess\Helper\Data $helper,
        PackRequestService $packRequestService,
        array $data = [])
    {
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $data);
        $this->itemsCollection = $itemsCollection;
        $this->packRequestOrderItemRepository = $packRequestOrderItemRepository;
        $this->coreRegistry = $registry;
        $this->helper = $helper;
        $this->packRequestService = $packRequestService;
    }

    /**
     * Retrieve order items collection
     *
     * @return \Magento\Sales\Model\ResourceModel\Order\Item\Collection
     */
    public function getItemsCollection()
    {
        if ($this->isPackedRequest()) {
            /** @var \Magento\Sales\Model\ResourceModel\Order\Item\Collection $itemCollection */
            $itemCollection = $this->packRequestService->getPackedViewItemsCollection($this->getParentBlock()->getPackRequestId());
//            $itemCollection = $this->packRequestOrderItemRepository->getPackedItemsCollection($this->getParentBlock()->getPackRequestId());
        } else {
            /** @var \Magento\Sales\Model\ResourceModel\Order\Item\Collection $itemCollection */
//            $itemCollection = $this->packRequestOrderItemRepository->getNeedToPackItemsCollection($this->getParentBlock()->getPackRequestId());
            $itemCollection = $this->packRequestService->getPackedItemsCollection($this->getParentBlock()->getPackRequestId());
        }
        foreach ($itemCollection as $item) {
            $barcodes = $this->helper->getProductBarcodes($item->getData(PackRequestItemInterface::PRODUCT_ID));
            if (!empty($barcodes)) {
                $item->setData(PackRequestItemInterface::ITEM_BARCODE, $barcodes);
            }
        }

        return $itemCollection;
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