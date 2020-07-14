<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilSuccess\Controller\Adminhtml\Package;

class GetInfo extends \Magestore\FulfilSuccess\Controller\Adminhtml\Package
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $packageId = $this->getRequest()->getParam('package_id');
        $resultPage = $this->resultPageFactory->create();
        $result = $resultPage->getLayout()->createBlock(
            'Magestore\FulfilSuccess\Block\Adminhtml\Package\PackageDetail', 'package_detail'
        )->setData(
            'package_id', $packageId
        )->toHtml();
        $this->getResponse()->setBody($result);
    }
}
