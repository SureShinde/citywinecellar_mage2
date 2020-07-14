<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Block\Adminhtml\PackRequest\Packaging;

use Magestore\FulfilSuccess\Api\Data\PackRequestItemInterface;

class Scan extends \Magestore\FulfilSuccess\Block\Adminhtml\AbstractRequest\Detail\Scan
{

    /**
     * @var \Magestore\FulfilSuccess\Api\PackRequestOrderItemRepositoryInterface
     */
    protected $packRequestOrderItemRepository;

    /**
     * @var \Magestore\FulfilSuccess\Service\PickRequest\PickService
     */
    protected $pickService;

    /**
     * Scan constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magestore\FulfilSuccess\Api\PackRequestOrderItemRepositoryInterface $packRequestOrderItemRepository
     * @param \Magestore\FulfilSuccess\Service\PickRequest\PickService $pickService
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magestore\FulfilSuccess\Api\PackRequestOrderItemRepositoryInterface $packRequestOrderItemRepository,
        \Magestore\FulfilSuccess\Service\PickRequest\PickService $pickService,
        array $data = [])
    {
        $this->packRequestOrderItemRepository = $packRequestOrderItemRepository;
        $this->pickService = $pickService;
        parent::__construct($context, $coreRegistry, $data);
    }

    /**
     * Init data
     */
    public function initData()
    {
        $this->title = '';
        $this->placeholder = 'Scan item barcode here';
        $packRequest = $this->coreRegistry->registry('current_pack_request');
        if ($packRequest && $packRequest->getId()) {
            $source = [];
            $items = $this->packRequestOrderItemRepository->getNeedToPackItemsCollection($packRequest->getId());
            if (count($items) > 0) {
                foreach ($items as $item) {
                    $itemData = [
                        PackRequestItemInterface::PACK_REQUEST_ITEM_ID => $item->getData(PackRequestItemInterface::PACK_REQUEST_ITEM_ID),
                        PackRequestItemInterface::ITEM_BARCODE => $this->pickService->getItemBarcodesByProductId($item->getData(PackRequestItemInterface::PRODUCT_ID)),
                        PackRequestItemInterface::REQUEST_QTY => $item->getData(PackRequestItemInterface::REQUEST_QTY)
                    ];
                    $source[] = $itemData;
                }
            }

            $this->sourceData = $source;
        }
    }

    public function getJsService()
    {
        return "Magestore_FulfilSuccess/js/packrequest/packaging/scan-item";
    }
}