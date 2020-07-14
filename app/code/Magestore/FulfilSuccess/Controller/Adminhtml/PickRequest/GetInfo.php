<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilSuccess\Controller\Adminhtml\PickRequest;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;

class GetInfo extends \Magento\Sales\Controller\Adminhtml\Order
{
    /**
     * @var \Magestore\FulfilSuccess\Api\PickRequestRepositoryInterface
     */
    protected $pickRequestRepository;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Sales\Api\OrderManagementInterface $orderManagement,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Psr\Log\LoggerInterface $logger,
        \Magestore\FulfilSuccess\Api\PickRequestRepositoryInterface $pickRequestRepository
    )
    {
        parent::__construct(
            $context,
            $coreRegistry,
            $fileFactory,
            $translateInline,
            $resultPageFactory,
            $resultJsonFactory,
            $resultLayoutFactory,
            $resultRawFactory,
            $orderManagement,
            $orderRepository,
            $logger);
        $this->pickRequestRepository = $pickRequestRepository;
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->_initOrder();
        $resultPage = $this->resultPageFactory->create();

        $resultPage->addHandle('fulfilsuccess_pickrequest_getinfo');
        $result = $resultPage->getLayout()->getBlock('pick_request_detail')->toHtml();

        $this->getResponse()->setBody($result);
    }

    /**
     * Initialize order model instance
     *
     * @return \Magento\Sales\Api\Data\OrderInterface|false
     */
    public function _initOrder()
    {
        $pickRequestId = $this->getRequest()->getParam('pick_request_id');
        $pickRequest = $this->pickRequestRepository->getById($pickRequestId);
        $this->_coreRegistry->register('current_pick_request', $pickRequest);    
        
        $orderId = $pickRequest->getOrderId();
        try {
            $order = $this->orderRepository->get($orderId);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('This order no longer exists.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        } catch (InputException $e) {
            $this->messageManager->addErrorMessage(__('This order no longer exists.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        $this->_coreRegistry->register('sales_order', $order);
        $this->_coreRegistry->register('current_order', $order);
        return $order;
    }
}
