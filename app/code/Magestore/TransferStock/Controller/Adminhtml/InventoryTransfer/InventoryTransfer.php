<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer;

/**
 * Class InventoryTransfer
 * @package Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer
 */
abstract class InventoryTransfer extends \Magestore\TransferStock\Controller\Adminhtml\AbstractAction
{
    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_TransferStock::inventorytransfer');
    }
}
