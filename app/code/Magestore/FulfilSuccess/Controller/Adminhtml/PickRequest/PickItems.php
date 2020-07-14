<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Controller\Adminhtml\PickRequest;

use Magento\Backend\App\Action\Context;
use \Magento\Framework\Controller\Result\JsonFactory;
use \Magestore\FulfilSuccess\Api\Data\PickRequestInterface;
use \Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface;

/**
 * Class PickItems
 * @package Magestore\FulfilSuccess\Controller\Adminhtml\PickRequest
 */
class PickItems extends \Magento\Backend\App\Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magestore\FulfilSuccess\Api\PickRequestRepositoryInterface
     */
    protected $pickRequestRepository;

    /**
     * @var \Magestore\FulfilSuccess\Api\PickRequestItemRepositoryInterface
     */
    protected $pickRequestItemRepository;

    /**
     * @var \Magestore\FulfilSuccess\Service\PickRequest\PickService
     */
    protected $pickService;

    /**
     * PickItems constructor.
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param \Magestore\FulfilSuccess\Api\PickRequestRepositoryInterface $pickRequestRepository
     * @param \Magestore\FulfilSuccess\Api\PickRequestItemRepositoryInterface $pickRequestItemRepository
     * @param \Magestore\FulfilSuccess\Service\PickRequest\PickService $pickService
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        \Magestore\FulfilSuccess\Api\PickRequestRepositoryInterface $pickRequestRepository,
        \Magestore\FulfilSuccess\Api\PickRequestItemRepositoryInterface $pickRequestItemRepository,
        \Magestore\FulfilSuccess\Service\PickRequest\PickService $pickService
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->pickRequestRepository = $pickRequestRepository;
        $this->pickRequestItemRepository = $pickRequestItemRepository;
        $this->pickService = $pickService;
    }

    public function execute()
    {
        $response = [
            'action' => 'pick_items'
        ];
        $pickRequestId = $this->getRequest()->getParam('pick_request_id');
        $pickRequestItemData = $this->getRequest()->getParam('pick_request_item_data');
        $pickedAllItems = $this->getRequest()->getParam('picked_all_items', false);
        if(!empty($pickRequestId) && !empty($pickRequestItemData) && is_array($pickRequestItemData)){
            $request = $this->pickRequestRepository->getById($pickRequestId);
            if($request->getId()){
                $status = $request->getData(PickRequestInterface::STATUS);
                if($status == PickRequestInterface::STATUS_PICKING){
                    $pickedData = [];
                    if(count($pickRequestItemData) > 0) {
                        foreach ($pickRequestItemData as $itemData){
                            if(isset($itemData[PickRequestItemInterface::PICK_REQUEST_ITEM_ID])){
                                $pickedData[$itemData[PickRequestItemInterface::PICK_REQUEST_ITEM_ID]] = $itemData;
                            }
                        }
                    }
                    $this->pickService->finishPickingRequest($pickedData, $pickRequestId);
                    if($pickedAllItems == 'true'){
                        $message = 'The request #%1 has been picked successfully';
                    }else{
                        $message = 'The request #%1 has been picked successfully, remaining items in the picking request have been moved to Prepare-Fulfil.';
                    }
                    $response['success'] = true;
                    $response['message'] = __($message, $pickRequestId);
                }else{
                    $this->pickService->removePickingSession();
                    $response['error'] = true;
                    $response['message'] = __('The request #%1 has been picked previously, can not pick it again', $pickRequestId);
                }
            }else{
                $response['error'] = true;
                $response['message'] = __('The pick request #%1 does not exist', $pickRequestId);
            }
        }else{
            $response['error'] = true;
            $response['message'] = __('Invalid Data');
        }
        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($response);
        return $resultJson;
    }

    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_FulfilSuccess::pick_request');
    }
}
