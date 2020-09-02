<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Model\ResourceModel\Order\Hold;

/**
 * Class Collection
 * @package Magestore\OrderSuccess\Model\ResourceModel\Sales\Hold
 */
class Collection extends \Magestore\OrderSuccess\Model\ResourceModel\Order\Collection
{
    /**
     * add condition.
     *
     * @param
     * @return $this
     */
    public function addCondition(){
        $this->addFieldToFilter('main_table.status','holded');
    }
}
