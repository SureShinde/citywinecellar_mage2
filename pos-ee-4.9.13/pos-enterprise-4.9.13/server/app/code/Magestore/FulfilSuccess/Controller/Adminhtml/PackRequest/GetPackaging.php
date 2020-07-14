<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilSuccess\Controller\Adminhtml\PackRequest;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;

class GetPackaging extends \Magento\Sales\Controller\Adminhtml\Order
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magestore_FulfilSuccess::pack_request';

    /**
     * @var \Magestore\FulfilSuccess\Api\PackRequestRepositoryInterface
     */
    protected $packRequestRepository;

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
        \Magestore\FulfilSuccess\Api\PackRequestRepositoryInterface $packRequestRepository
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
        $this->packRequestRepository = $packRequestRepository;
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

        $packagingBlock = $resultPage->getLayout()->createBlock('Magestore\FulfilSuccess\Block\Adminhtml\PackRequest\Packaging',
            'fulfil_packaging',
            ['template' => 'Magestore_FulfilSuccess::packRequest/packaging/popup.phtml']
        );

        $this->getResponse()->setBody($packagingBlock->toHtml());
    }

    /**
     * Initialize order model instance
     *
     * @return \Magento\Sales\Api\Data\OrderInterface|false
     */
    protected function _initOrder()
    {
        $packRequestId = $this->getRequest()->getParam('pack_request_id');
        $packRequest = $this->packRequestRepository->get($packRequestId);
        $this->_coreRegistry->register('current_pack_request', $packRequest);

        $orderId = $packRequest->getOrderId();

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
