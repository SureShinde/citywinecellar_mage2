<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilSuccess\Controller\Adminhtml\PackRequest;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;

class Pack extends \Magestore\FulfilSuccess\Controller\Adminhtml\PackRequest\GetInfo
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magestore_FulfilSuccess::pack_request';

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->_initOrder();
        $resultPage = $this->resultPageFactory->create();

        $resultPage->addHandle('fulfilsuccess_packrequest_pack');
        $result = $resultPage->getLayout()->getBlock('fulfil_packaging')->toHtml();

        $this->getResponse()->setBody($result);
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
