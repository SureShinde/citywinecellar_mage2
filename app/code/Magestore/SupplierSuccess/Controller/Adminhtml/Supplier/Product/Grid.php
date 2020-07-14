<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\SupplierSuccess\Controller\Adminhtml\Supplier\Product;

use \Magestore\SupplierSuccess\Controller\Adminhtml\AbstractSupplier;
class Grid extends AbstractSupplier
{
    const ADMIN_RESOURCE = 'Magestore_SupplierSuccess::view_supplier';

    /**
     * Product grid for AJAX request
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        return $this->_resultPageFactory->create();

        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(
                $this->_stockGrid,
                $this->_stockGridName
            )->toHtml()
        );
    }
}
