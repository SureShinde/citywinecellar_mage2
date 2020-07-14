<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Controller\Adminhtml;

use Magento\Framework\Controller\ResultFactory as ResultFactory;

/**
 * Class Context
 * @package Magestore\TransferStock\Controller\Adminhtml
 */
class Context extends \Magento\Backend\App\Action\Context
{
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

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
     * @var \Magento\CatalogInventory\Model\Configuration
     */
    protected $_catalogInventoryConfiguration;

    /**
     * @var \Magento\Framework\Logger\Monolog
     */
    protected $_logger;
    /**
     * @var \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer
     */
    protected $inventoryTransferResource;
    /**
     * @var \Magestore\TransferStock\Model\InventoryTransferFactory
     */
    protected $inventoryTransferFactory;
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;
    /**
     * @var \Magestore\TransferStock\Api\TransferManagementInterface
     */
    protected $transferManagement;

    /**
     * @var \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\Receive
     */
    protected $receiveResource;
    /**
     * @var \Magestore\TransferStock\Api\Data\InventoryTransfer\ReceiveInterfaceFactory
     */
    protected $receiveFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * Context constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\App\ResponseInterface $response
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param \Magento\Framework\App\ActionFlag $actionFlag
     * @param \Magento\Framework\App\ViewInterface $view
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param ResultFactory $resultFactory
     * @param \Magento\Backend\Model\Session $session
     * @param \Magento\Framework\AuthorizationInterface $authorization
     * @param \Magento\Backend\Model\Auth $auth
     * @param \Magento\Backend\Helper\Data $helper
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Framework\Logger\Monolog $logger
     * @param \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer $inventoryTransferResource
     * @param \Magestore\TransferStock\Model\InventoryTransferFactory $inventoryTransferFactory
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magestore\TransferStock\Api\TransferManagementInterface $transferManagement
     * @param \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\Receive $receiveResource
     * @param \Magestore\TransferStock\Api\Data\InventoryTransfer\ReceiveInterfaceFactory $receiveFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param bool $canUseBaseUrl
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Framework\App\ViewInterface $view,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory,
        ResultFactory $resultFactory,
        \Magento\Backend\Model\Session $session,
        \Magento\Framework\AuthorizationInterface $authorization,
        \Magento\Backend\Model\Auth $auth,
        \Magento\Backend\Helper\Data $helper,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Logger\Monolog $logger,
        \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer $inventoryTransferResource,
        \Magestore\TransferStock\Model\InventoryTransferFactory $inventoryTransferFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magestore\TransferStock\Api\TransferManagementInterface $transferManagement,
        \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\Receive $receiveResource,
        \Magestore\TransferStock\Api\Data\InventoryTransfer\ReceiveInterfaceFactory $receiveFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        $canUseBaseUrl = false
    ) {
        parent::__construct(
            $request,
            $response,
            $objectManager,
            $eventManager,
            $url,
            $redirect,
            $actionFlag,
            $view,
            $messageManager,
            $resultRedirectFactory,
            $resultFactory,
            $session,
            $authorization,
            $auth,
            $helper,
            $backendUrl,
            $formKeyValidator,
            $localeResolver,
            $canUseBaseUrl
        );

        $this->_eventManager = $eventManager;
        $this->_coreRegistry = $registry;
        $this->_resultForwardFactory = $resultForwardFactory;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultLayoutFactory = $resultLayoutFactory;
        $this->_logger = $logger;
        $this->inventoryTransferFactory = $inventoryTransferFactory;
        $this->inventoryTransferResource = $inventoryTransferResource;
        $this->authSession = $authSession;
        $this->transferManagement = $transferManagement;
        $this->receiveResource = $receiveResource;
        $this->receiveFactory = $receiveFactory;
        $this->filesystem = $filesystem;
        $this->fileFactory = $fileFactory;
    }

    /**
     * @return \Magento\Framework\Event\ManagerInterface
     */
    public function getEventManager()
    {
        return $this->_eventManager;
    }

    /**
     * @return \Magento\Framework\Registry
     */
    public function getCoreRegistry()
    {
        return $this->_coreRegistry;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\ForwardFactory
     */
    public function getResultForwardFactory()
    {
        return $this->_resultForwardFactory;
    }

    /**
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function getResultPageFactory()
    {
        return $this->_resultPageFactory;
    }

    /**
     * @return \Magento\Framework\View\Result\LayoutFactory
     */
    public function getResultLayoutFactory()
    {
        return $this->_resultLayoutFactory;
    }

    /**
     * @return \Magento\Framework\Logger\Monolog
     */
    public function getLogger()
    {
        return $this->_logger;
    }

    /**
     * @return \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer
     */
    public function getInventoryTransferResource()
    {
        return $this->inventoryTransferResource;
    }

    /**
     * @return \Magestore\TransferStock\Model\InventoryTransferFactory
     */
    public function getInventoryTransferFactory()
    {
        return $this->inventoryTransferFactory;
    }

    /**
     * @return \Magento\Backend\Model\Auth\Session
     */
    public function getAuthSession()
    {
        return $this->authSession;
    }

    /**
     * @return \Magestore\TransferStock\Api\TransferManagementInterface
     */
    public function getTransferManagement()
    {
        return $this->transferManagement;
    }

    /**
     * @return \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\Receive
     */
    public function getReceiveResource()
    {
        return $this->receiveResource;
    }

    /**
     * @return \Magestore\TransferStock\Api\Data\InventoryTransfer\ReceiveInterfaceFactory
     */
    public function getReceiveFactory()
    {
        return $this->receiveFactory;
    }

    /**
     * @return \Magento\Framework\Filesystem
     */
    public function getFileSystem()
    {
        return $this->filesystem;
    }

    /**
     * @return \Magento\Framework\App\Response\Http\FileFactory
     */
    public function getFileFactory()
    {
        return $this->fileFactory;
    }
}
