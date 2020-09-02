<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Block\Adminhtml\DropshipRequest\Edit\Tab\Info\Items\Column;

/**
 * Class Qty
 * @package Magestore\DropshipSuccess\Block\Adminhtml\DropshipRequest\Edit\Tab\Info\Items\Column
 */
class Qty extends \Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn
{
    /**
     * @return mixed
     */
    public function getItem()
    {
        $item = $this->_getData('item');
        return $item;
    }
}
