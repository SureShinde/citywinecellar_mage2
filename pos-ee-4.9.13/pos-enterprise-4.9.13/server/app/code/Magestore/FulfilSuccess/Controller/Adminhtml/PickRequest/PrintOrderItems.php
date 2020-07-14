<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilSuccess\Controller\Adminhtml\PickRequest;

use Magento\Backend\App\Action\Context;
use Magestore\FulfilSuccess\Api\PickRequestRepositoryInterface;
use Magestore\FulfilSuccess\Service\PickRequest\PickRequestPrintService;

class PrintOrderItems extends \Magento\Backend\App\Action
{

    /**
     * @var PickRequestRepositoryInterface
     */
    protected $pickRequestRepository;
    
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
     * @param PickRequestRepositoryInterface $pickRequestRepository
     * @param PickRequestPrintService $printService
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context, 
        PickRequestRepositoryInterface $pickRequestRepository, 
        PickRequestPrintService $printService,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        $this->pickRequestRepository = $pickRequestRepository;
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
        $id = $this->_request->getParam('id');
        $pickRequest = $this->pickRequestRepository->getById($id);
        $printData = $this->printService->printPickedOrderItems([$pickRequest]);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->getConfig()->getTitle()->prepend(__('Sales Picked Items'));

        $resultPage->getLayout()->getBlock('fulfilsuccess.pickrequest.printorderitems')
                        ->setPrintData($printData)->setPickRequestId($id);
        
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
