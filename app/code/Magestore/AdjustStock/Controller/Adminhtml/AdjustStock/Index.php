<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Controller\Adminhtml\AdjustStock;

/**
 * Class Index
 * @package Magestore\AdjustStock\Controller\Adminhtml\AdjustStock
 */
class Index extends \Magestore\AdjustStock\Controller\Adminhtml\AdjustStock\AdjustStock
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
        $resultPage->setActiveMenu('Magestore_AdjustStock::adjuststock_history');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Stock Adjustment'));
        $resultPage->addBreadcrumb(__('Inventory Success'), __('Inventory Success'));
        $resultPage->addBreadcrumb(__('Manage Adjust Stock'), __('Manage Stock Adjustment'));

        return $resultPage;
    }

    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_AdjustStock::adjuststock_history');
    }
}
