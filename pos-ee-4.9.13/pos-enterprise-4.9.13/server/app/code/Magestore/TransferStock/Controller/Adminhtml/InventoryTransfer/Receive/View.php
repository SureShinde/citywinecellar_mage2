<?php
/**
 * Copyright © 2019 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer\Receive;

/**
 * Class View
 * @package Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer\Receive
 */
class View extends \Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer\InventoryTransfer
{
    /**
     * @var \Magestore\TransferStock\Api\Data\InventoryTransfer\ReceiveInterfaceFactory
     */
    protected $_receiveFactory;

    /**
     * @var \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\Receive
     */
    protected $_receiveResource;

    /**
     * View constructor.
     * @param \Magestore\TransferStock\Controller\Adminhtml\Context $context
     */
    public function __construct(
        \Magestore\TransferStock\Controller\Adminhtml\Context $context
    ){
        parent::__construct($context);
        $this->_receiveFactory = $context->getReceiveFactory();
        $this->_receiveResource = $context->getReceiveResource();

    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id', null);

        /** @var \Magestore\TransferStock\Model\InventoryTransfer\Receive $receive */
        $receive = $this->_receiveFactory->create();
        $resultPage = $this->_resultPageFactory->create();
        if ($id) {
            $this->_receiveResource->load($receive, $id);
            if (!$receive->getReceiveId()) {
                $this->messageManager->addErrorMessage(__('This receiving no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->_session->setData('current_receive_id', $receive->getReceiveId());
        $resultPage->getConfig()->getTitle()->prepend(__('View Receiving #%1', $receive->getReceiveId()));

        return $resultPage;
    }

    /**
     * Init page.
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Magestore_TransferStock::inventorytransfer');
        return $resultPage;
    }
}
