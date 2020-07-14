<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Controller\Adminhtml\PickRequest;

use Magento\Backend\App\Action\Context;
use \Magento\Framework\Controller\Result\JsonFactory;
use \Magestore\FulfilSuccess\Api\Data\PickRequestInterface;

/**
 * Class PickOrder
 * @package Magestore\FulfilSuccess\Controller\Adminhtml\PickRequest
 */
class PickOrder extends \Magento\Backend\App\Action
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
     * ScanBarcode constructor.
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param \Magestore\FulfilSuccess\Api\PickRequestRepositoryInterface $pickRequestRepository
     * @param \Magestore\FulfilSuccess\Api\PickRequestItemRepositoryInterface $pickRequestItemRepository
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
            'action' => 'pick_order'
        ];
        $barcode = $this->getRequest()->getParam('barcode');
        if(!empty($barcode)){
            $request = $this->pickRequestRepository->getById($barcode);
            if($request->getId()){
                $status = $request->getData(PickRequestInterface::STATUS);
                if($status == PickRequestInterface::STATUS_PICKING){
                    $orderlink = $this->getUrl('sales/order/view', ['order_id' => $request->getData(PickRequestInterface::ORDER_ID)]);
                    $response['success'] = true;
                    $response['data'] = [
                        PickRequestInterface::PICK_REQUEST_ID => $request->getId(),
                        PickRequestInterface::ORDER_INCREMENT_ID => $request->getData(PickRequestInterface::ORDER_INCREMENT_ID),
                        'orderlink' => $orderlink,
                        'barcode' => $barcode,
                        'items' => $this->pickService->startPickRequest($request)
                    ];
                }else if($status == PickRequestInterface::STATUS_CANCELED){
                    $response['error'] = true;
                    $response['message'] = __('The Pick Request #%1 of Sales #%2 has been canceled. You should return all picked items to the Warehouse.',
                                                [$barcode, $request->getOrderIncrementId()]
                                            );                    
                }else{
                    $response['error'] = true;
                    $response['message'] = __('The Pick Request #%1 of Sales #%2 has been picked successfully.',
                                                [$barcode, $request->getOrderIncrementId()]
                                            );
                }
            }else{
                $response['error'] = true;
                $response['message'] = __('The Pick Request #%1 does not exist.', $barcode);
            }
        }else{
            $response['error'] = true;
            $response['message'] = __('Barcode can not be empty');
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
