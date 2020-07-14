<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Controller\Adminhtml\PackRequest;

use Magestore\FulfilSuccess\Api\PackRequestRepositoryInterface;
use Magestore\FulfilSuccess\Service\PackRequest\PackRequestService;

class MoveNeedToShip extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magestore_FulfilSuccess::move_to_need_ship';

    /**
     * @var PackRequestRepositoryInterface
     */
    protected $packRequestRepository;

    /**
     * @var PackRequestService
     */
    protected $packRequestService;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        PackRequestRepositoryInterface $packRequestRepository,
        PackRequestService $packRequestService
    ) {
        $this->packRequestRepository = $packRequestRepository;
        $this->packRequestService = $packRequestService;
        parent::__construct($context);
    }

    /**
     * View order detail
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $packRequestId = $this->getRequest()->getParam('pack_request_id');
        $packRequest = $this->packRequestRepository->get($packRequestId);
        if($packRequest->getId()) {
            try{
                $this->packRequestService->moveItemsToNeedToShip($packRequest);
                $this->messageManager->addSuccessMessage(__('The remaining items in the packing request have been moved to Prepare-Fulfil.'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find the packing request to move.'));
    }
}
