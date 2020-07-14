<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Controller\Adminhtml;

/**
 * Class AbstractAction
 * @package Magestore\PurchaseOrderSuccess\Controller\Adminhtml
 */
abstract class AbstractAction extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magestore_PurchaseOrderSuccess::purchase_order';
    
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $_resultForwardFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $_resultLayoutFactory;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Model\PurchaseOrderFactory
     */
    protected $_purchaseOrderFactory;

    /**
     * @var \Magento\Framework\Logger\Monolog
     */
    protected $_logger;

    /**
     * AbstractAction constructor.
     * @param Context $context
     */
    public function __construct(
        \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Context $context
    ) {
        parent::__construct($context);
        $this->_eventManager = $context->getEventManager();
        $this->_registry = $context->getRegistry();
        $this->_resultForwardFactory = $context->getResultForwardFactory();
        $this->_resultPageFactory = $context->getResultPageFactory();
        $this->_resultLayoutFactory = $context->getResultLayoutFactory();
        $this->_purchaseOrderFactory = $context->getPurchaseOrderFactory();
        $this->_logger = $context->getLogger();
    }

    /**
     * Init layout, menu and breadcrumb
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_PurchaseOrderSuccess::purchase_order');
        return $resultPage;
    }
    
    /**
     * Get type label of current item
     *
     * @param int $type
     * @return string
     */
    public function getTypeLabel($type){
        return \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Type::getTypeLabel($type);
    }

    /**
     * Redirect to grid quotation or purchase order
     *
     * @param int $type
     * @return $this
     */
    public function redirectGrid($type, $message = null){
        $resultRedirect = $this->resultRedirectFactory->create();
        if($message)
            $this->messageManager->addErrorMessage($message);
        $controllerName = $type==1?'quotation':'purchaseOrder';
        return $resultRedirect->setPath('*/'.$controllerName.'/');
    }

    /**
     * @param $type
     * @param null $id
     * @return $this
     */
    public function redirectForm($type, $id = null, $message = null, $messageType = 'success'){
        $resultRedirect = $this->resultRedirectFactory->create();
        if($message){
            switch ($messageType){
                case \Magento\Framework\Message\MessageInterface::TYPE_ERROR:
                    $this->messageManager->addErrorMessage($message);
                    break;
                case \Magento\Framework\Message\MessageInterface::TYPE_WARNING:
                    $this->messageManager->addWarningMessage($message);
                    break;
                default:
                    $this->messageManager->addSuccessMessage($message);
                    break;
            }
        }
        $controllerName = $type==1?'quotation':'purchaseOrder';
        $action = $id?'view':'new';
        $params = $id?['id' => $id]:[];
        return $resultRedirect->setPath('*/'.$controllerName.'/'. $action, $params);
    }
    
}