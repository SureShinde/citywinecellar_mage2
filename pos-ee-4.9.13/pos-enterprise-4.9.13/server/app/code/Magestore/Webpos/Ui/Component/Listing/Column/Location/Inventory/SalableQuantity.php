<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magestore\Webpos\Ui\Component\Listing\Column\Location\Inventory;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku;
use Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Add grid column with salable quantity data
 */
class SalableQuantity extends Column
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $requestInterface;

    /**
     * SalableQuantity constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\RequestInterface $requestInterface
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\RequestInterface $requestInterface,
        array $components = [],
        array $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->objectManager = $objectManager;
        $this->requestInterface = $requestInterface;
    }

    /**
     * @inheritdoc
     */
    public function prepareDataSource(array $dataSource)
    {
        $stockId = $this->requestInterface->getParam('stock_id');
        if ($stockId && $dataSource['data']['totalRecords'] > 0) {
            /** @var \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType */
            $isSourceItemManagementAllowedForProductType = $this->objectManager
                ->get('Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface');
            foreach ($dataSource['data']['items'] as &$row) {
                $row['salable_quantity'] =
                    $isSourceItemManagementAllowedForProductType->execute($row['type_id']) === true
                        ? $this->getSalableQuantityDataBySku($row['sku'], (int) $stockId)
                        : null;
            }
        }
        unset($row);

        return $dataSource;
    }

    /**
     * @param string $sku
     * @param int $stockId
     * @return array
     */
    private function getSalableQuantityDataBySku($sku, $stockId)
    {
        /** @var \Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface $getStockItemConfiguration */
        $getStockItemConfiguration = $this->objectManager
            ->get('Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface');
        $stockItemConfiguration = $getStockItemConfiguration->execute($sku, $stockId);
        $isManageStock = $stockItemConfiguration->isManageStock();
        return [
            'qty' => $isManageStock ?
                $this->objectManager
                    ->get('Magento\InventorySalesApi\Api\GetProductSalableQtyInterface')
                    ->execute($sku, $stockId) :
                null,
            'manage_stock' => $isManageStock,
        ];
    }
}
