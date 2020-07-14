<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Model\ResourceModel\Staff\Role;

/**
 * Class Collection
 * @package Magestore\Appadmin\Model\ResourceModel\Staff\Role
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     *
     * @var string
     */
    protected $_idFieldName = 'role_id';

    /**
     * Initialize collection resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\Appadmin\Model\Staff\Role', 'Magestore\Appadmin\Model\ResourceModel\Staff\Role');
    }
}