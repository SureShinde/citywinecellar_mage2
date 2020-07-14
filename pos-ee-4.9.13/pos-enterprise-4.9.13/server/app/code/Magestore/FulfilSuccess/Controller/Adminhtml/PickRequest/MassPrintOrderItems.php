<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilSuccess\Controller\Adminhtml\PickRequest;

use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action\Context;
use Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\PickRequest\CollectionFactory;
use Magestore\FulfilSuccess\Service\PickRequest\PickRequestPrintService;

class MassPrintOrderItems extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    
    /**
     * @var PickRequestPrintService 
     */
    protected $printService;
  
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;    

    /**
     * 
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param PickRequestPrintService $printService
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context, 
        Filter $filter, 
        CollectionFactory $collectionFactory, 
        PickRequestPrintService $printService,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->printService = $printService;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $printData = $this->printService->printPickedOrderItems($collection->getItems());

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->getConfig()->getTitle()->prepend(__('Sales Picked Items'));

        $resultPage->getLayout()->getBlock('fulfilsuccess.pickrequest.printorderitems')
                        ->setPrintData($printData);
        
        return $resultPage;
    }
    
    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->addBreadcrumb(__('Fulfillment Success'), __('Fulfillment Success'))
            ->addBreadcrumb(__('Picking Items'), __('Order Picked Items'));
        return $resultPage;
    }    
   
}
