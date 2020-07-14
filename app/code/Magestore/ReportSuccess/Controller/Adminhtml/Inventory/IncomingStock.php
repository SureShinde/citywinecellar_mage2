<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Controller\Adminhtml\Inventory;

/**
 * Class IncomingStock
 * @package Magestore\ReportSuccess\Controller\Adminhtml\Inventory
 */
class IncomingStock extends \Magestore\ReportSuccess\Controller\Adminhtml\Inventory
{
    /**
     * @var \Magento\Framework\Module\Manager $moduleManager
     */
    protected $moduleManager;

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
        \Magento\Framework\Module\Manager $moduleManager
    )
    {
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $resultPageFactory, $resultJsonFactory, $resultLayoutFactory, $resultForwardFactory);
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_ReportSuccess::incomming_stock');
        $resultPage->addBreadcrumb(__('Incoming Stock Report'), __('Incoming Stock Report'));
        $resultPage->getConfig()->getTitle()->prepend(__('Incoming Stock Report'));
        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->moduleManager->isEnabled("Magestore_PurchaseOrderSuccess")
            && $this->_authorization->isAllowed($this->resource);
    }
}