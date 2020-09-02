<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Block\DropshipRequest\Detail\Items;


use Magestore\DropshipSuccess\Service\DropshipRequestService;

/**
 * Class DefaultRenderer
 * @package Magestore\DropshipSuccess\Block\DropshipRequest\Detail\Items
 */
class DefaultRenderer extends \Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer
{

    /**
     * @var DropshipRequestService
     */
    protected $dropshipRequestService;

    /**
     * DefaultRenderer constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory
     * @param DropshipRequestService $dropshipRequestService
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        DropshipRequestService $dropshipRequestService,
        array $data = []
    ) {
        parent::__construct($context, $string, $productOptionFactory, $data);
        $this->dropshipRequestService = $dropshipRequestService;
    }

    /**
     * @param \Magento\Framework\DataObject $item
     * @return string
     */
    public function getShippedInput(\Magento\Framework\DataObject $item)
    {
        if (!$this->isShippedRequest()) {
            /** @var \Magento\Sales\Api\Data\OrderItemInterface $item */
            if ($item->getParentItemId()) {
                $itemId = $item->getParentItemId();
            } else {
                $itemId = $item->getItemId();
            }
            return '<input onchange="validateNumber(this, ' . floatval($item->getQtyRequested() - $item->getQtyShipped()) . ');" class="text-center qty-item os_dropshipsuccess_input_dropship_on_detail width-small not-negative" type="number" id="os_dropship_request_items_' . $item->getDropshipRequestItemId() . '" name="shipment[items][' . $itemId . ']" value="0" data-increment="1" data-max="' . floatval($item->getQtyRequested() - $item->getQtyShipped()) . '"/>';
        }
    }

    /**
     * @return bool
     */
    public function isShippedRequest()
    {
        return $this->dropshipRequestService->isShippedRequest();
    }
}