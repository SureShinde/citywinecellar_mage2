<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Plugin\InventoryCatalog\Model;

use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product as ProductResourceModel;
use Magento\InventoryCatalogApi\Model\GetSkusByProductIdsInterface;
use Magestore\WebposIntegration\Controller\Rest\RequestProcessor;
use Magestore\Webpos\Model\Checkout\PosOrder;

/**
 * Class GetSkusByProductIds
 *
 * @package Magestore\Webpos\Plugin\InventoryCatalog\Model
 */
class GetSkusByProductIds
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ProductResourceModel
     */
    private $productResource;

    /**
     * GetSkusByProductIds constructor.
     *
     * @param RequestInterface $request
     * @param ProductResourceModel $productResource
     */
    public function __construct(
        RequestInterface $request,
        ProductResourceModel $productResource
    ) {
        $this->request = $request;
        $this->productResource = $productResource;
    }

    /**
     * Before get sku by product id
     *
     * @param GetSkusByProductIdsInterface $subject
     * @param array $productIds
     * @return array|null
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecute(GetSkusByProductIdsInterface $subject, array $productIds)
    {
        if (!$this->request->getParam(RequestProcessor::SESSION_PARAM_KEY)
            || !$this->request->getParam(PosOrder::PARAM_ORDER_LOCATION_ID)) {
            return null;
        }
        // Only update stock for existing products (remove deleted products)
        $skuByIds = array_column(
            $this->productResource->getProductsSku($productIds),
            ProductInterface::SKU,
            'entity_id'
        );
        return [array_keys($skuByIds)];
    }
}
