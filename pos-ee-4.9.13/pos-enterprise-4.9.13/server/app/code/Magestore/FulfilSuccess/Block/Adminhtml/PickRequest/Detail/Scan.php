<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Block\Adminhtml\PickRequest\Detail;

use Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface;

class Scan extends \Magestore\FulfilSuccess\Block\Adminhtml\AbstractRequest\Detail\Scan
{

    /**
     * @var \Magestore\FulfilSuccess\Model\Repository\PickRequest\OrderItemRepository
     */
    protected $orderItemRepository;

    /**
     * @var \Magestore\FulfilSuccess\Service\PickRequest\PickService
     */
    protected $pickService;

    /**
     * Scan constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magestore\FulfilSuccess\Model\Repository\PickRequest\OrderItemRepository $orderItemRepository
     * @param \Magestore\FulfilSuccess\Service\PickRequest\PickService $pickService
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magestore\FulfilSuccess\Model\Repository\PickRequest\OrderItemRepository $orderItemRepository,
        \Magestore\FulfilSuccess\Service\PickRequest\PickService $pickService,
        array $data = [])
    {
        $this->orderItemRepository = $orderItemRepository;
        $this->pickService = $pickService;
        parent::__construct($context, $coreRegistry, $data);
    }

    /**
     * Init data
     */
    public function initData(){
        $this->title = __('Items to pick');
        $this->placeholder = __('Scan item barcode here');
        $pickRequest = $this->coreRegistry->registry('current_pick_request');
        if($pickRequest && $pickRequest->getId()){
            $source = [];
            $items = $this->orderItemRepository->getItemsCollection($pickRequest->getId());
            if(count($items) > 0){
                foreach ($items as $item) {
                    $itemData = [
                        PickRequestItemInterface::ITEM_BARCODE => $this->pickService->getItemBarcodesByProductId($item->getData(PickRequestItemInterface::PRODUCT_ID)),
                        PickRequestItemInterface::REQUEST_QTY => $item->getData(PickRequestItemInterface::REQUEST_QTY),
                        PickRequestItemInterface::PICK_REQUEST_ITEM_ID => $item->getData(PickRequestItemInterface::PICK_REQUEST_ITEM_ID)
                    ];
                    $source[] = $itemData;
                }
            }
            $this->sourceData = $source;
        }
    }
    
    /**
     * 
     * @return string
     */
    public function getJsService()
    {
        return "Magestore_FulfilSuccess/js/service/detail/os-scan-item";
    }
}