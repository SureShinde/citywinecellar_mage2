<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Controller\Adminhtml\Inventory;

/**
 * Class HistoricalStock
 * @package Magestore\ReportSuccess\Controller\Adminhtml\Inventory
 */
class HistoricalStock extends \Magestore\ReportSuccess\Controller\Adminhtml\Inventory
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_ReportSuccess::stock_value');
        $resultPage->addBreadcrumb(__('Historical Stock Report'), __('Historical Stock Report'));
        $resultPage->getConfig()->getTitle()->prepend(__('Historical Stock Report'));
        return $resultPage;
    }
}