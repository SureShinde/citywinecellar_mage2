<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Model\ResourceModel\Page\Product;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;

class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    /**
     * @param $positionData
     * @return array
     */
    public function getProductInfoByIds($positionData)
    {
        $this->_beforeLoad();
        $this->_renderFilters();
        $this->_renderOrders();
        $select = clone $this->getSelect();
        $select->reset(\Magento\Framework\DB\Select::COLUMNS);
        $select->columns('e.entity_id');
        $select->group('e.entity_id');

        return $this->sortIds($this->getConnection()->fetchCol($select), $positionData);
    }

    /**
     * @return $this
     */
    protected function _beforeLoad()
    {
        $this->addAttributeToFilter('status', Status::STATUS_ENABLED);
        $this->addAttributeToFilter('visibility', [
            'in' => [
                Visibility::VISIBILITY_IN_CATALOG,
                Visibility::VISIBILITY_IN_SEARCH,
                Visibility::VISIBILITY_BOTH
            ]
        ]);

        return parent::_beforeLoad();
    }

    /**
     * @param array $ids
     * @return array
     */
    private function sortIds($ids, $positionData)
    {
        $positionData = $this->preparePositionData($positionData, $ids);
        $ids = array_diff($ids, $positionData);
        $itemsCount = count($ids) + count($positionData);
        $idx = 0;
        while ($idx < $itemsCount) {
            if (!isset($positionData[$idx])) {
                $positionData[$idx] = current($ids);
                next($ids);
            }
            $idx++;
        }

        ksort($positionData, SORT_NUMERIC);

        return $positionData;
    }

    /**
     * @param array $ids
     * @return array
     */
    protected function preparePositionData($positionData, $ids)
    {
        $positionData = array_intersect($positionData, $ids);
        $maxPosition = count($ids) - 1;
        foreach ($positionData as $position => $productId) {
            if ($position > $maxPosition) {
                $positionData[$maxPosition] = $productId;
                $maxPosition--;
            }
        }

        return $positionData;
    }
}
