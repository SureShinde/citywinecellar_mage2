<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\ResourceModel\Pos\Pos;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Resource POS Collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'pos_id';

    /**
     * Construct
     */
    public function _construct()
    {
        $this->_init(
            \Magestore\Webpos\Model\Pos\Pos::class,
            \Magestore\Webpos\Model\ResourceModel\Pos\Pos::class
        );
    }

    /**
     * Join to staff table
     *
     * @return $this
     */
    public function joinToStaffTable()
    {
        $this->getSelect()->joinLeft(
            ['staff' => $this->getTable('webpos_staff')],
            'main_table.staff_id = staff.staff_id',
            [
                'staff_name' => 'staff.name'
            ]
        );
        $this->getSelect()->order(['staff_id ASC', 'pos_name ASC']);
        return $this;
    }
}
