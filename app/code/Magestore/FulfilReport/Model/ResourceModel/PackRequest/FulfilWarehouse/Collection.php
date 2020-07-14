<?php

namespace Magestore\FulfilReport\Model\ResourceModel\PackRequest\FulfilWarehouse;

use Magestore\FulfilSuccess\Api\Data\PackRequestInterface;

class Collection extends \Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequest\Collection
{
    protected function _initSelect()
    {
        /** @var \Magestore\FulfilSuccess\Api\FulfilManagementInterface $fulfilManagement */
        $fulfilManagement = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magestore\FulfilSuccess\Api\FulfilManagementInterface');
        $isMSIEnable = $fulfilManagement->isMSIEnable();
        $selectionColumns = ['warehouse_id'];
        $resourceTableName = 'os_warehouse';
        $joinCondition = 'main_table.warehouse_id = resource.warehouse_id';
        $resourceFieldName = 'resource.warehouse_name';
        $resourceFieldId = 'resource.warehouse_id';
        $groupColumn = 'main_table.warehouse_id';
        if ($isMSIEnable) {
            $selectionColumns = ['source_code'];
            $resourceTableName = \Magento\Inventory\Model\ResourceModel\Source::TABLE_NAME_SOURCE;
            $joinCondition = 'main_table.source_code = resource.source_code';
            $resourceFieldName = 'resource.name';
            $resourceFieldId = 'resource.source_code';
            $groupColumn = 'main_table.source_code';
        }
        $this->getSelect()->from(['main_table' => $this->getMainTable()], $selectionColumns);
        $this->getSelect()->joinLeft(
            ['resource' => $this->getTable($resourceTableName)],
            $joinCondition,
            [
                'resource_field_name' => $resourceFieldName,
                'resource_field_id' => $resourceFieldId
            ]
        );
        $this->getSelect()->columns([
            'total_packed_requests' => new \Zend_Db_Expr("COUNT('pack_request_id')"),
        ])->group($groupColumn);

        $this->addCondition($isMSIEnable);

        return $this;
    }

    /**
     * Add condition
     *
     * @param bool $isMSIEnable
     */
    public function addCondition($isMSIEnable = false)
    {
        if ($isMSIEnable) {
            $this->addFieldToFilter('main_table.source_code', ['notnull' => true]);
        } else {
            $this->addFieldToFilter('main_table.warehouse_id', ['notnull' => true]);
        }

        $this->addFieldToFilter('main_table.status', PackRequestInterface::STATUS_PACKED);
    }
}