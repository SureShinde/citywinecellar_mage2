<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Controller\Adminhtml\PickRequest;

use Magento\Backend\App\Action\Context;
use \Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class CancelPicking
 * @package Magestore\FulfilSuccess\Controller\Adminhtml\PickRequest
 */
class CancelPicking extends \Magento\Backend\App\Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

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
        \Magestore\FulfilSuccess\Service\PickRequest\PickService $pickService
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->pickService = $pickService;
    }

    public function execute()
    {
        $response = [
            'action' => 'cancel_picking'
        ];
        $this->pickService->removePickingSession();
        $response['success'] = true;
        $response['data'] = [];
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
