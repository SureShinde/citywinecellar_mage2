<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Controller\Adminhtml\ReturnOrder;

/**
 * Class Index
 * @package Magestore\PurchaseOrderSuccess\Controller\Adminhtml\PurchaseOrder
 */
class Index extends \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\ReturnOrder\AbstractAction
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magestore_PurchaseOrderSuccess::list_return_order';

    /**
     * Quotation grid
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Return Request'));
        return $resultPage;
    }

}