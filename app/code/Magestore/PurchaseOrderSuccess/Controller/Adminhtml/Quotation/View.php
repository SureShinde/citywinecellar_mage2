<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Quotation;

/**
 * Class View
 * @package Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Quotation
 */
class View extends \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\AbstractAction
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magestore_PurchaseOrderSuccess::view_quotation';
    
    /**
     * View quotation form
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultForward = $this->_resultForwardFactory->create();
        $resultForward->setController('purchaseorder');
        $resultForward->setParams(['type' => '1']);
        $resultForward->forward('view');
    }
}