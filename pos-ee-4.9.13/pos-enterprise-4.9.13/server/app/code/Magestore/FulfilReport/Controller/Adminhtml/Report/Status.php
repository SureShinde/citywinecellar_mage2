<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilReport\Controller\Adminhtml\Report;


class Status extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory $pageFactory
     */
    protected $_papeFactory;

    /**
     * constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        $this->_papeFactory = $pageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->_papeFactory->create();
        $resultPage->setActiveMenu('Magestore_FulfilReport::fulfil_reports');
        $block = $resultPage->getLayout()
            ->createBlock('Magestore\FulfilReport\Block\Adminhtml\Report\Dashboard')
            ->setTemplate('Magestore_FulfilReport::report/status.phtml')
            ->toHtml();
        $this->getResponse()->setBody($block);
    }

}
