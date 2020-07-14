<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\ResourceModel\Traits;

trait CollectionTrait
{
    protected function renderFilters()
    {
        if ($this->getQueryText()) {
            $queryText = $this->getConnection()->quote('%' . $this->getQueryText() . '%');
            $allColumns = $this->getFulltextIndexColumns($this, $this->getStoreTable() ?: $this->getMainTable());
            $condition = '';
            foreach ($allColumns as $key => $column) {
                $column = $this->getStoreColumn($column);
                if ($key < 1) {
                    $condition .= sprintf('%s LIKE %s', $column, $queryText);
                    continue;
                }
                $condition .= sprintf(' OR %s LIKE %s', $column, $queryText);
            }

            if ($allColumns) {
                $this->getSelect()->where($condition);
            }
        }
    }

    /**
     * @param $collection
     * @param $indexTable
     *
     * @return array
     */
    private function getFulltextIndexColumns($collection, $indexTable)
    {
        $indexes = $collection->getConnection()->getIndexList($indexTable);
        $columns = [];
        foreach ($indexes as $index) {
            if (strtoupper($index['INDEX_TYPE']) == 'FULLTEXT') {
                $columns = $index['COLUMNS_LIST'];
            }
        }

        return $columns;
    }

    /**
     * @param string $query
     *
     * @return $this
     */
    public function addSearchFilter($query)
    {
        $this->setQueryText(trim($this->getQueryText() . ' ' . $query));

        return $this;
    }

    /**
     * @return array
     */
    public function getIndexFulltextValues()
    {
        $fulltextValues = [];
        foreach ($this->getItems() as $id => $item) {
            $fulltextString = '';
            $indexColumns = $this->getFulltextIndexColumns($this, $this->getMainTable());
            foreach ($indexColumns as $indexColumn) {
                if ($item->getData($indexColumn)) {
                    $fulltextString .= ' ' . trim($item->getData($indexColumn));
                }
            }

            $fulltextValues[$id] = trim($fulltextString);
        }

        return $fulltextValues;
    }
}
