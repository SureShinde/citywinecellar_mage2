<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Controller\Adminhtml\Inventory;

/**
 * Class StockDetails
 * @package Magestore\ReportSuccess\Controller\Adminhtml\Inventory
 */
class StockDetails extends \Magestore\ReportSuccess\Controller\Adminhtml\Inventory
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_ReportSuccess::stock_details');
        $resultPage->addBreadcrumb(__('Stock Details Report'), __('Stock Details Report'));
        $resultPage->getConfig()->getTitle()->prepend(__('Stock Details Report'));
        return $resultPage;
    }
}