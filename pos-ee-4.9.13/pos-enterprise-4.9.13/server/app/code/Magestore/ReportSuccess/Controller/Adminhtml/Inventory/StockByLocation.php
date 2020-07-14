<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Controller\Adminhtml\Inventory;

/**
 * Class StockByLocation
 * @package Magestore\ReportSuccess\Controller\Adminhtml\Inventory
 */
class StockByLocation extends \Magestore\ReportSuccess\Controller\Adminhtml\Inventory
{
    /**
     * @var \Magento\Framework\Module\Manager $moduleManager
     */
    protected $moduleManager;

    protected $reportManagement;
    /**
     * IncomingStock constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magestore\ReportSuccess\Api\ReportManagementInterface $reportManagement
    )
    {
        $this->moduleManager = $moduleManager;
        $this->reportManagement = $reportManagement;
        parent::__construct($context, $resultPageFactory, $resultJsonFactory, $resultLayoutFactory, $resultForwardFactory);
    }


    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $label = $this->reportManagement->isMSIEnable()
            ? $lable = __('Stock by Source Report')
            : $lable = __('Stock by Warehouse Report');
        $resultPage->setActiveMenu('Magestore_ReportSuccess::stock_by_location');
        $resultPage->addBreadcrumb($label, $label);
        $resultPage->getConfig()->getTitle()->prepend($label);
        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return !$this->moduleManager->isEnabled("Magestore_StockManagementSuccess")
                && $this->moduleManager->isEnabled("Magestore_PurchaseOrderSuccess")
                && $this->_authorization->isAllowed($this->resource);
    }
}