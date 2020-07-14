<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilSuccess\Controller\Adminhtml\PickRequest;

class Index extends \Magestore\FulfilSuccess\Controller\Adminhtml\PickRequest
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    
    /**
     * @var \Magestore\FulfilSuccess\Service\PickRequest\BuilderService 
     */
    protected $pickRequestBuilderService;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magestore\FulfilSuccess\Service\PickRequest\BuilderService $pickRequestBuilderService
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->pickRequestBuilderService = $pickRequestBuilderService;
        parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->getConfig()->getTitle()->prepend(__('Fulfillment - Picking Items'));

        return $resultPage;
    }
}
