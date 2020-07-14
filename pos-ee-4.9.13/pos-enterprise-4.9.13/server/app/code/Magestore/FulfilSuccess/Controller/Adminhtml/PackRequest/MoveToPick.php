<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilSuccess\Controller\Adminhtml\PackRequest;

use Magento\Backend\App\Action\Context;
use Magestore\FulfilSuccess\Api\PackRequestRepositoryInterface;
use Magestore\FulfilSuccess\Service\PackRequest\PackRequestService;

class MoveToPick extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magestore_FulfilSuccess::move_to_pick';

    /**
     * @var PackRequestRepositoryInterface
     */
    protected $packRequestRepository;

    /**
     * @var PackRequestService
     */
    protected $packRequestService;


    /**
     *
     * @param Context $context
     * @param PackRequestRepositoryInterface $packRequestRepository
     * @param PackRequestService $packRequestService
     */
    public function __construct(
        Context $context,
        PackRequestRepositoryInterface $packRequestRepository,
        PackRequestService $packRequestService
    )
    {
        $this->packRequestRepository = $packRequestRepository;
        $this->packRequestService = $packRequestService;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $packRequest = $this->packRequestRepository->get($id);
        $resultRedirect = $this->resultRedirectFactory->create();
        if($packRequest->getId()) {
            try{
                $this->packRequestService->moveItemsToPick($packRequest);
                $this->messageManager->addSuccessMessage(__('The remaining items in packing request have been moved to pick.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find the packing request to move.'));
        return $resultRedirect->setPath('*/*/');
    }


}
