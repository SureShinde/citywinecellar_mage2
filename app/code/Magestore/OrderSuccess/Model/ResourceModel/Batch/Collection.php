<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Model\ResourceModel\Batch;

/**
 * Class Collection
 * @package Magestore\OrderSuccess\Model\ResourceModel\Batch
 */
/**
 * Class Collection
 * @package Magestore\OrderSuccess\Model\ResourceModel\Batch
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct() 
    {
        $this->_init('Magestore\OrderSuccess\Model\Batch', 'Magestore\OrderSuccess\Model\ResourceModel\Batch');
    }
    
    /**
     * 
     * @return \Magestore\OrderSuccess\Model\ResourceModel\Batch\Collection
     */
    public function addUser()
    {
        $this->getSelect()->join(
                    ['user' => $this->getTable('admin_user')],
                    'main_table.user_id = user.user_id',
                    ['username']
                );
        return $this;
    }    
}
