<?php

namespace Magestore\FulfilReport\Model\ResourceModel\PickRequest\FulfilStaffDaily;

class Collection extends \Magestore\FulfilReport\Model\ResourceModel\PickRequest\FulfilStaff\Collection
{
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->columns([
            'date' => new \Zend_Db_Expr("DATE_FORMAT(main_table.updated_at, \"%Y-%m-%d\")")
        ]);

        $this->getSelect()->group(new \Zend_Db_Expr("DATE_FORMAT(main_table.updated_at, \"%Y-%m-%d\")"));
    }
}