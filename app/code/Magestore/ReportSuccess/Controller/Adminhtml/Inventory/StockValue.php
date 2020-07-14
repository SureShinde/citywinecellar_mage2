<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Controller\Adminhtml\Inventory;

/**
 * Class StockValue
 * @package Magestore\ReportSuccess\Controller\Adminhtml\Inventory
 */
class StockValue extends \Magestore\ReportSuccess\Controller\Adminhtml\Inventory
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_ReportSuccess::stock_value');
        $resultPage->addBreadcrumb(__('Stock Value Report'), __('Stock Value Report'));
        $resultPage->getConfig()->getTitle()->prepend(__('Stock Value Report'));
        return $resultPage;
    }
}