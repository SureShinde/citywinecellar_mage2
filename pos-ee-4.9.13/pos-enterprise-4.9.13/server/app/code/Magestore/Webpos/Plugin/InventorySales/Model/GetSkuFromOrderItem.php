<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Plugin\InventorySales\Model;

use Magento\Framework\App\RequestInterface;
use Magento\InventorySalesApi\Model\GetSkuFromOrderItemInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\InventoryCatalogApi\Model\GetSkusByProductIdsInterface;
use Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface;
use Magestore\Webpos\Model\Checkout\PosOrder;
use Magestore\WebposIntegration\Controller\Rest\RequestProcessor;

/**
 * Class GetSkuFromOrderItem
 *
 * @package Magestore\Webpos\Plugin\InventorySales\Model
 */
class GetSkuFromOrderItem
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var GetSkusByProductIdsInterface
     */
    private $getSkusByProductIds;

    /**
     * @var IsSourceItemManagementAllowedForProductTypeInterface
     */
    private $isSourceItemManagementAllowedForProductType;

    /**
     * GetSkuFromOrderItem constructor.
     *
     * @param RequestInterface $request
     * @param GetSkusByProductIdsInterface $getSkusByProductIds
     * @param IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType
     */
    public function __construct(
        RequestInterface $request,
        GetSkusByProductIdsInterface $getSkusByProductIds,
        IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType
    ) {
        $this->request = $request;
        $this->getSkusByProductIds = $getSkusByProductIds;
        $this->isSourceItemManagementAllowedForProductType = $isSourceItemManagementAllowedForProductType;
    }

    /**
     * Change process get sku by order item
     *
     * @param GetSkuFromOrderItemInterface $subject
     * @param callable $proceed
     * @param OrderItemInterface $orderItem
     * @return mixed|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundExecute(
        GetSkuFromOrderItemInterface $subject,
        callable $proceed,
        OrderItemInterface $orderItem
    ) {
        if (!$this->request->getParam(RequestProcessor::SESSION_PARAM_KEY)
            || !$this->request->getParam(PosOrder::PARAM_ORDER_LOCATION_ID)) {
            return $proceed($orderItem);
        }

        // Allow create shipment for deleted products
        if ($this->isSourceItemManagementAllowedForProductType->execute($orderItem->getProductType())) {
            $itemSkus = $this->getSkusByProductIds->execute([$orderItem->getProductId()]);
            if (isset($itemSkus[$orderItem->getProductId()])) {
                return $itemSkus[$orderItem->getProductId()];
            }
        }
        return $orderItem->getSku();
    }
}
