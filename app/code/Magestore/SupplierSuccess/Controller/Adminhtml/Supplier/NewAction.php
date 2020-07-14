<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Controller\Adminhtml\Supplier;

use \Magestore\SupplierSuccess\Controller\Adminhtml\AbstractSupplier;

class NewAction extends AbstractSupplier
{
    const ADMIN_RESOURCE = 'Magestore_SupplierSuccess::view_supplier';
    /**
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
