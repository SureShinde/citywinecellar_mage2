<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\ResourceModel\Traits;

trait StoreCollectionTrait
{
    /**
     * @param bool $isAddColumns
     * @return $this
     */
    public function addDefaultStore($isAddColumns = true)
    {
        $table = $this->getMainTable() . '_store';
        $idFieldName = $this->getResource()->getIdFieldName();
        $this->getSelect()->joinLeft(
            ['store' => $table],
            sprintf(
                'store.%s = main_table.%s AND store.store_id = %s',
                $idFieldName,
                $idFieldName,
                \Magento\Store\Model\Store::DEFAULT_STORE_ID
            ),
            []
        )->group('main_table.' . $idFieldName);
        if ($isAddColumns) {
            $this->getSelect()->columns('store.*');
        }
        if (method_exists($this, 'setIsStoreDataAdded')) {
            $this->setIsStoreDataAdded(true);
        }
        return $this;
    }

    /**
     * @param $storeId
     * @param bool $isAddColumns
     * @return $this
     */
    public function addStore($storeId, $isAddColumns = true)
    {
        $table = $this->getMainTable() . '_store';
        $idFieldName = $this->getResource()->getIdFieldName();

        $this->getSelect()->joinLeft(
            ['noDefaultStore' => $table],
            sprintf(
                'noDefaultStore.%s = main_table.%s AND noDefaultStore.store_id = %s',
                $idFieldName,
                $idFieldName,
                $storeId
            ),
            []
        );
        if ($isAddColumns) {
            $this->getSelect()->columns('noDefaultStore.*');
        }

        return $this;
    }

    /**
     * @param int $storeId
     * @return $this|\Amasty\Blog\Model\ResourceModel\Abstracts\Collection
     */
    public function addStoreWithDefault($storeId)
    {
        $this->addDefaultStore(false)->addStore($storeId, false);
        $idFieldName = $this->getResource()->getIdFieldName();
        foreach (self::MULTI_STORE_FIELDS_MAP as $key => $field) {
            $this->getSelect()->columns([$key => $field]);
        }
        $this->getSelect()->group('main_table.' . $idFieldName);

        return $this;
    }

    /**
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->getSelect()->limit($limit);
    }

    /**
     * @param string $column
     * @return string
     */
    public function getStoreColumn($column)
    {
        return self::MULTI_STORE_FIELDS_MAP[$column] ?? $column;
    }
}
