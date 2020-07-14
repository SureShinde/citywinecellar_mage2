<?php

namespace Magestore\FulfilReport\Model\ResourceModel\PackRequest\FulfilWarehouseDaily;

class Collection extends \Magestore\FulfilReport\Model\ResourceModel\PackRequest\FulfilWarehouse\Collection
{
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->columns(
            ['date' => new \Zend_Db_Expr("DATE_FORMAT(main_table.updated_at, \"%Y-%m-%d\")")]
        )->group(
            new \Zend_Db_Expr("DATE_FORMAT(main_table.updated_at, \"%Y-%m-%d\")")
        )->order(
            new \Zend_Db_Expr("DATE_FORMAT(main_table.updated_at, \"%Y-%m-%d\") ASC")
        );
        /** @var \Magestore\FulfilSuccess\Api\FulfilManagementInterface $fulfilManagement */
        $fulfilManagement = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magestore\FulfilSuccess\Api\FulfilManagementInterface');
        if ($fulfilManagement->isMSIEnable()) {
            $this->getSelect()->order(new \Zend_Db_Expr("resource.source_code ASC"));
        } else {
            $this->getSelect()->order(new \Zend_Db_Expr("resource.warehouse_id ASC"));
        }
    }
}