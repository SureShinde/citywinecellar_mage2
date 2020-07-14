<?php

namespace Magestore\FulfilReport\Model\ResourceModel\PackRequest\FulfilStaffDaily;

class Collection extends \Magestore\FulfilReport\Model\ResourceModel\PackRequest\FulfilStaff\Collection
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