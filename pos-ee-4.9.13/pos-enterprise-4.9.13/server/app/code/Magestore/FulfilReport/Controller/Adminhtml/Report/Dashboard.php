<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilReport\Controller\Adminhtml\Report;

class Dashboard extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;

    /**
     * constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory)
    {
        $this->_pageFactory = $pageFactory;
        return parent::__construct($context);
    }

    public function execute()
    {
        $resultPage =  $this->_pageFactory->create();
        $resultPage->setActiveMenu('Magestore_FulfilReport::fulfil_reports');
        $resultPage->getConfig()->getTitle()->prepend(__('Dashboard'));
        return $resultPage;
    }
}
