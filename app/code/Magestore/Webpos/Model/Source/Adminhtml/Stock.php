<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Source\Adminhtml;

class Stock implements \Magento\Framework\Option\ArrayInterface
{
    protected $options;

    /**
     * @var \Magestore\Webpos\Api\WebposManagementInterface
     */
    protected $webposManagement;

    /**
     * Stock constructor.
     * @param \Magestore\Webpos\Api\WebposManagementInterface $webposManagement
     */
    public function __construct(
        \Magestore\Webpos\Api\WebposManagementInterface $webposManagement
    )
    {
        $this->webposManagement = $webposManagement;
    }

    /**
     * Return options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $isMSIEnable = $this->webposManagement->isMSIEnable();
        if (!$isMSIEnable) {
            return [];
        }
        if (!$this->options) {
            $this->options = [['value' => '', 'label' => __('--Please Select--')]];
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            /** @var \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $stockCollection */
            $stockCollection = $objectManager->create('Magento\Inventory\Model\ResourceModel\Stock\Collection');
            $stockCollection->getSelect()->joinInner(
                ['inventory_source_stock_link' => $stockCollection->getTable('inventory_source_stock_link')],
                'main_table.stock_id = inventory_source_stock_link.stock_id',
                []
            )->columns([
                'stock_id' => 'main_table.stock_id',
                'name' => 'main_table.name'
            ])->group(
                'main_table.stock_id'
            )->having('COUNT(inventory_source_stock_link.source_code) = 1');
            if ($this->webposManagement->isWebposStandard()) {
                /** @var \Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface $defaultStockProvider */
                $defaultStockProvider = $objectManager
                    ->create('Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface');
                $defaultStockId = $defaultStockProvider->getId();
                $stockCollection->getSelect()->having('main_table.stock_id = ?', $defaultStockId);
            }
            foreach ($stockCollection as $item) {
                $this->options[] = ['value' => $item->getStockId(), 'label' => $item->getName()];
            }
        }

        return $this->options;
    }

}