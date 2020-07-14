<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\ResourceModel\Shift;

/**
 * Class Shift
 * @package Magestore\Webpos\Model\ResourceModel\Shift
 */
class Shift extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('webpos_shift', 'shift_id');
    }

}