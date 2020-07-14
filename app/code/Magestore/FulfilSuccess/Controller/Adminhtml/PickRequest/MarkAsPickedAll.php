<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilSuccess\Controller\Adminhtml\PickRequest;

use Magento\Backend\App\Action\Context;
use Magestore\FulfilSuccess\Api\PickRequestRepositoryInterface;
use Magestore\FulfilSuccess\Service\PickRequest\PickRequestService;

class MarkAsPickedAll extends \Magento\Backend\App\Action
{
    
    /**
     * @var PickRequestRepositoryInterface
     */
    protected $pickRequestRepository;
    
    /**
     * @var PickRequestService 
     */
    protected $pickRequestService;


    /**
     * 
     * @param Context $context
     * @param PickRequestRepositoryInterface $pickRequestRepository
     * @param PickRequestService $pickRequestService
     */
    public function __construct(
        Context $context, 
        PickRequestRepositoryInterface $pickRequestRepository, 
        PickRequestService $pickRequestService
    )
    {
        $this->pickRequestRepository = $pickRequestRepository;
        $this->pickRequestService = $pickRequestService;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $id = $this->_request->getParam('id');
        $pickRequest = $this->pickRequestRepository->getById($id);
        $resultRedirect = $this->resultRedirectFactory->create();
        if($pickRequest->getId()) {
            try{
                $this->pickRequestService->markAsPickedAll($pickRequest);
                $this->messageManager->addSuccess(
                        __('The Request #%1 of Order #%2 has been marked as Picked, then moved to packing process.', [
                                    $pickRequest->getId(), 
                                    $pickRequest->getOrderIncrementId()
                                ]));
                return $resultRedirect->setPath('*/*/');            
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/');            
            }
        }
        $this->messageManager->addError(__('Find no picking request to move'));
        return $resultRedirect->setPath('*/*/');        
    }
  
   
}
