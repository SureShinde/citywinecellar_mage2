<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Model\Page\Product;

use Amasty\Xlanding\Model\Page;

class AdminhtmlDataProvider extends AbstractDataProvider
{
    /**
     * @param $conditions
     * @return $this;
     */
    public function setSerializedRuleConditions($conditions)
    {
        $this->session->setSerializedRuleConditions($conditions);
        return $this;
    }

    /**
     * @return string
     */
    public function getSerializedRuleConditions()
    {
        return $this->session->getSerializedRuleConditions();
    }

    /**
     * @return \Amasty\Xlanding\Model\ResourceModel\Page\Product\Collection
     */
    public function getProductCollection()
    {
        if (!$this->hasData('product_collection')) {
            $this->emulation->startEnvironmentEmulation(
                $this->getStoreId(),
                \Magento\Framework\App\Area::AREA_FRONTEND,
                true
            );
            $collection = $this->productCollectionFactory->create()
                ->addAttributeToSelect([
                    'sku',
                    'name',
                    'price',
                    'small_image'
                ]);

            if ($this->moduleManager->isEnabled('Magento_CatalogInventory')) {
                $collection->joinField(
                    'stock',
                    'cataloginventory_stock_status',
                    'stock_status',
                    'product_id=' . $this->productIdLink,
                    ['stock_id' => \Magento\CatalogInventory\Model\Stock::DEFAULT_STOCK_ID],
                    'left'
                );
            }
            $this->emulation->stopEnvironmentEmulation();

            $this->orderCollection($collection);
            $this->setData('product_collection', $collection);
        }

        return $this->getData('product_collection');
    }

    /**
     * @param \Amasty\Xlanding\Model\ResourceModel\Page\Product\Collection $collection
     * @return $this
     */
    private function orderCollection($collection)
    {
        $ruleCollection = $this->getRuleCollection();
        $sortedIds = $ruleCollection->getProductInfoByIds(
            array_flip($this->getProductPositionData())
        );

        if ($sortedIds) {
            $ids = implode(',', $sortedIds);
            $field = $ruleCollection->getSelect()->getAdapter()->quoteIdentifier('e.entity_id');
            $collection->getSelect()->where($field . ' IN(?)', $sortedIds);
            $collection->getSelect()->order(new \Zend_Db_Expr("FIELD({$field}, {$ids})"));
        } else {
            $collection->addAttributeToFilter('entity_id', self::DEFAULT_PRODUCT);
        }

        return $this;
    }

    /**
     * @return \Amasty\Xlanding\Model\ResourceModel\Page\Product\Collection
     */
    private function getRuleCollection()
    {
        if (!$this->hasData('rule_collection')) {
            $this->emulation->startEnvironmentEmulation(
                $this->getStoreId(),
                \Magento\Framework\App\Area::AREA_FRONTEND,
                true
            );
            $collection = $this->productCollectionFactory->create();
            $page = $this->initPage();
            $page->applyAttributesFilter($collection->getSelect());
            if ($this->isEmptyRule($page->getRule())) {
                $collection->addAttributeToFilter('entity_id', self::DEFAULT_PRODUCT);
            }
            if ($this->getRuleCollectionLimit()) {
                $collection->getSelect()->limit($this->getRuleCollectionLimit());
            }
            $this->setCollectionOrder($collection);
            $this->emulation->stopEnvironmentEmulation();

            $this->setData('rule_collection', $collection);
        }

        return $this->getData('rule_collection');
    }

    /**
     * @param \Amasty\Xlanding\Model\ResourceModel\Page\Product\Collection $collection
     * @return $this
     */
    private function setCollectionOrder($collection)
    {
        $this->sorting->applySorting($collection, $this->getSortOrder());
        return $this;
    }

    /**
     * @return Page
     */
    private function initPage()
    {
        $page = $this->getCurrentLandingPage();

        $rule = $page->getRule();
        if ($this->getSerializedRuleConditions()) {
            $rule->setConditions([]);
            $rule->setData('conditions_serialized', $this->getSerializedRuleConditions());
        }
        return $page;
    }

    /**
     * @return Page
     */
    public function getCurrentLandingPage()
    {
        return $this->_registry->registry('amasty_xlanding_page');
    }

    /**
     * @return array
     */
    public function getProductPositionData($storeId = null)
    {
        if ($storeId === null) {
            $storeId = $this->getStoreId();
        }

        $positionData = $this->session->getPositionData();

        return isset($positionData[$storeId]) ? $positionData[$storeId] : [];
    }

    /**
     * @return array
     */
    public function getProductPositionDataByStore()
    {
        return $this->session->getPositionData() ?: [];
    }

    /**
     * @param array $positionData
     * @return $this
     */
    public function setProductPositionData($positionData = [], $storeId = null)
    {
        if ($storeId === null) {
            $storeId = $this->getStoreId();
        }

        if (!empty($positionData)) {
            $currentPositionData = $this->session->getPositionData();

            foreach ($positionData as $productId => $position) {
                if (is_numeric($productId)) {
                    $currentPositionData[$storeId][$productId] = $position;
                }
            }
            $positionData = $currentPositionData;
            $this->session->setPositionData($positionData);
        }
        return $this;
    }

    /**
     * @param $key
     * @return $this
     */
    public function unsetProductPositionData($key, $storeId = null)
    {
        if ($storeId === null) {
            $storeId = $this->getStoreId();
        }

        $data = $this->getProductPositionDataByStore();
        unset($data[$storeId][$key]);
        $this->session->setPositionData($data);
        return $this;
    }

    /**
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder)
    {
        $this->session->setSortOrder($sortOrder);
        return $this;
    }

    /**
     * @return int
     */
    public function getSortOrder()
    {
        return (int)$this->session->getSortOrder();
    }

    /**
     * @param Page $page
     * @return $this
     */
    public function init($page)
    {
        $this->setSerializedRuleConditions($page->getConditionsSerialized());
        $this->setSortOrder($page->getSortOrder());
        $this->session->setPositionData($page->getProductPositionData());
        return $this;
    }

    /**
     * @param int $sourcePosition
     * @param int $destanationPosition
     * @return $this
     */
    public function resortPositionData($sourcePosition, $destanationPosition, $storeId = null)
    {
        if ($storeId === null) {
            $storeId = $this->getStoreId();
        }

        $positionData = $this->getProductPositionDataByStore();

        $storePositionData = isset($positionData[$storeId]) ? $positionData[$storeId] : [];

        if ($sourcePosition < $destanationPosition) {
            foreach ($storePositionData as $productId => $position) {
                if ($position > $sourcePosition && $position < $destanationPosition) {
                    $storePositionData[$productId]--;
                }
            }
        } elseif ($sourcePosition > $destanationPosition) {
            foreach ($storePositionData as $productId => $position) {
                if ($position >= $destanationPosition && $position < $sourcePosition) {
                    $storePositionData[$productId]++;
                }
            }
        } else {
            return $this;
        }

        $positionData[$storeId] = $storePositionData;
        $this->session->setPositionData($positionData);
        return $this;
    }

    /**
     * @param int $productId
     * @return int
     */
    public function getCurrentProductPosition($productId)
    {
        $productIds = $this->getRuleCollection()->getProductInfoByIds(
            array_flip($this->getProductPositionData())
        );
        $position = array_search($productId, $productIds);

        return $position !== false ? $position : count($productIds);
    }

    /**
     * @param $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->session->setStoreId($storeId);
        return $this;
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->session->getStoreId() ? $this->session->getStoreId() : $this->storeManager->getStore()->getId();
    }

    /**
     * Clear storage data after save page
     *
     * @return $this
     */
    public function clear()
    {
        $this->session->setPositionData = null;
        $this->setSerializedRuleConditions(null);
        $this->setSortOrder(null);
        $this->setStoreId(null);

        return $this;
    }
}
