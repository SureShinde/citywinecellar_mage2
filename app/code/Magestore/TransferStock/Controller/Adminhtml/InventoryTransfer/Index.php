<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer;

/**
 * Class Index
 * @package Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer
 */
class Index extends \Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer\InventoryTransfer
{
    /**
     * History action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_TransferStock::inventorytransfer');
        $resultPage->getConfig()->getTitle()->prepend(__('Inventory Transfer'));
        $resultPage->addBreadcrumb(__('Inventory Transfer'), __('Inventory Transfer'));

        return $resultPage;
    }
}
