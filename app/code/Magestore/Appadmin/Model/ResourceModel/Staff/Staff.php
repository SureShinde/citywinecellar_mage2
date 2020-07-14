<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Model\ResourceModel\Staff;

/**
 * Class Staff
 * @package Magestore\Appadmin\Model\ResourceModel\Staff
 */
class Staff extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     *
     * @var string
     */
    protected $_idFieldName = 'staff_id';
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('webpos_staff', 'staff_id');
    }

}
