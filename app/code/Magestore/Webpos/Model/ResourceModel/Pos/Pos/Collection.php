<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\ResourceModel\Pos\Pos;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'pos_id';

    /**
     * construct
     */
    public function _construct()
    {
        $this->_init('Magestore\Webpos\Model\Pos\Pos', 'Magestore\Webpos\Model\ResourceModel\Pos\Pos');
    }

    /**
     * join to staff table
     *
     * @return $this
     */
    public function joinToStaffTable()
    {
        $this->getSelect()->joinLeft(array('staff' => $this->getTable('webpos_staff')),
                        'main_table.staff_id = staff.staff_id',
                        array(
                            'staff_name'=>'staff.name'
                        ));
        $this->getSelect()->order(array('staff_id ASC', 'pos_name ASC'));
        return $this;
    }
}