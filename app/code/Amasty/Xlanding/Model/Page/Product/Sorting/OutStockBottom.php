<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Model\Page\Product\Sorting;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\DB\Select;
use Magento\Store\Model\StoreManagerInterface;

class OutStockBottom extends SortAbstract implements SortInterface
{
    /**
     * @var \Magento\CatalogInventory\Model\ResourceModel\Stock\Status
     */
    private $stockStatus;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        \Magento\CatalogInventory\Model\ResourceModel\Stock\Status $stockStatus,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
    ) {
        parent::__construct($moduleManager, $scopeConfig, $productMetadata);
        $this->stockStatus = $stockStatus;
        $this->storeManager = $storeManager;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return __("Move out of stock to bottom");
    }

    /**
     * @param Collection $collection
     * @return Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sort(Collection $collection)
    {
        $select = $collection->getSelect();
        parent::sort($collection);
        if (!$this->isStockStatusJoined($select)) {
            $this->stockStatus->addStockStatusToSelect($select, $this->storeManager->getWebsite());
        }

        $collection->getSelect()
            ->order(sprintf('stock_status.%s %s', $this->getStockColumn($select), $collection::SORT_ORDER_DESC));

        return $collection;
    }

    /**
     * @param Select $select
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    protected function getStockColumn($select)
    {
        $fromTables = $select->getPart(Select::FROM);
        $catalogInventoryTable = $this->stockStatus->getMainTable();
        if ($this->isMsiEnabled() && $fromTables['stock_status']['tableName'] != $catalogInventoryTable) {
            $qtyColumn = 'is_salable';
        } else {
            $qtyColumn = 'stock_status';
            $fromTables['stock_status']['joinCondition'] = preg_replace(
                '@(stock_status.website_id=)\d+@',
                '$1 0',
                $fromTables['stock_status']['joinCondition']
            );
            $select->setPart(Select::FROM, $fromTables);
        }

        return $qtyColumn;
    }

    /**
     * @param Select $select
     * @return bool
     * @throws \Zend_Db_Select_Exception
     */
    protected function isStockStatusJoined($select)
    {
        $fromTables = $select->getPart(Select::FROM);

        return isset($fromTables['stock_status']);
    }

    /**
     * @return bool
     */
    private function isMsiEnabled()
    {
        return $this->moduleManager->isEnabled('Magento_Inventory');
    }
}
