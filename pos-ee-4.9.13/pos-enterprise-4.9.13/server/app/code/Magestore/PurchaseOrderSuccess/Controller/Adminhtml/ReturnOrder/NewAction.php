<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Controller\Adminhtml\ReturnOrder;

/**
 * Class NewAction
 * @package Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Quotation
 */
class NewAction extends \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\ReturnOrder\AbstractAction
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magestore_PurchaseOrderSuccess::new_return_order';

    /**
     * New purchase order form
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultForward = $this->_resultForwardFactory->create();
        $resultForward->forward('view');
        return $resultForward;
    }
}