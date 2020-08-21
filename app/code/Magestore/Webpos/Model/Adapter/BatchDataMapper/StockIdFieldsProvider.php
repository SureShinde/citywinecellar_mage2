<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Adapter\BatchDataMapper;

use Magestore\Webpos\Model\ResourceModel\Inventory\Stock\Item;
use Magento\AdvancedSearch\Model\Adapter\DataMapper\AdditionalFieldsProviderInterface;

/**
 * Provide data mapping for stock id
 */
class StockIdFieldsProvider implements AdditionalFieldsProviderInterface
{
    /**
     * @var Item
     */
    private $resourceStockItem;

    /**
     * StockIdFieldsProvider constructor.
     *
     * @param Item $resourceStockItem
     */
    public function __construct(
        Item $resourceStockItem
    ) {
        $this->resourceStockItem = $resourceStockItem;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getFields(array $productIds, $storeId)
    {
        $fields = $this->resourceStockItem->getAssignedStockIdByIds($productIds);

        return $fields;
    }
}
