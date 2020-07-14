<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magestore\Giftvoucher\Ui\DataProvider\Product\Listing\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Quantity Per Source modifier on CatalogInventory Product Grid
 */
class Qty extends AbstractModifier
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    /**
     * Qty constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Module\Manager $moduleManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Module\Manager $moduleManager
    )
    {
        $this->objectManager = $objectManager;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @inheritdoc
     */
    public function modifyData(array $data)
    {
        if (0 === $data['totalRecords'] || !$this->isMSIEnable()) {
            return $data;
        }
        $isSourceItemManagementAllowedForProductType = $this->objectManager->get(
            'Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface'
        );
        foreach ($data['items'] as &$item) {
            $item['quantity_per_source'] = $isSourceItemManagementAllowedForProductType->execute(
                $item['type_id']
            ) === true ? $this->getSourceItemsData($item['sku']) : [];
        }
        unset($item);

        return $data;
    }

    /**
     * @param string $sku
     * @return array
     * @throws NoSuchEntityException
     */
    private function getSourceItemsData($sku)
    {
        /** @var \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $getSourceItemsBySku */
        $getSourceItemsBySku = $this->objectManager->get('Magento\InventoryApi\Api\GetSourceItemsBySkuInterface');
        $sourceRepository = $this->objectManager->get('Magento\InventoryApi\Api\SourceRepositoryInterface');
        $sourceItems = $getSourceItemsBySku->execute($sku);

        $sourceItemsData = [];
        foreach ($sourceItems as $sourceItem) {
            $source = $sourceRepository->get($sourceItem->getSourceCode());
            $qty = (float)$sourceItem->getQuantity();

            $sourceItemsData[] = [
                'source_name' => $source->getName(),
                'qty' => $qty,
            ];
        }
        return $sourceItemsData;
    }

    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta)
    {
        if (!$this->isMSIEnable()) {
            return $meta;
        }

        $meta = array_replace_recursive($meta, [
            'product_columns' => [
                'children' => [
                    'quantity_per_source' => $this->getQuantityPerSourceMeta(),
                    'qty' => [
                        'arguments' => null,
                    ],
                ],
            ],
        ]);
        return $meta;
    }

    /**
     * @return array
     */
    private function getQuantityPerSourceMeta()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'sortOrder' => 76,
                        'filter' => false,
                        'sortable' => false,
                        'label' => __('Quantity per Source'),
                        'dataType' => Text::NAME,
                        'componentType' => Column::NAME,
                        'component' => 'Magento_InventoryCatalogAdminUi/js/product/grid/cell/quantity-per-source',
                    ]
                ],
            ],
        ];
    }

    /**
     * @return boolean
     */
    public function isMSIEnable()
    {
        return $this->moduleManager->isEnabled('Magento_Inventory') &&
        $this->moduleManager->isEnabled('Magento_InventoryApi');
    }
}
