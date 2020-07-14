<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Controller\Adminhtml;

use Magento\Framework\Controller\ResultFactory as ResultFactory;


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
     * @var \Magento\CatalogInventory\Model\Configuration
     */
    protected $_catalogInventoryConfiguration;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Model\PurchaseOrderFactory
     */
    protected $_purchaseOrderFactory;

    /**
     * @var \Magento\Framework\Logger\Monolog
     */
    protected $_logger;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\Config\ProductConfig
     */
    protected $productConfig;

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
     * @param \Magestore\PurchaseOrderSuccess\Model\PurchaseOrderFactory $purchaseOrderFactory
     * @param \Magestore\PurchaseOrderSuccess\Service\Config\ProductConfig $productConfig
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
        \Magestore\PurchaseOrderSuccess\Model\PurchaseOrderFactory $purchaseOrderFactory,
        \Magestore\PurchaseOrderSuccess\Service\Config\ProductConfig $productConfig,
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
        $this->_registry = $registry;
        $this->_resultForwardFactory = $resultForwardFactory;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultLayoutFactory = $resultLayoutFactory;
        $this->_purchaseOrderFactory = $purchaseOrderFactory;
        $this->_logger = $logger;
        $this->productConfig = $productConfig;
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
    public function getRegistry()
    {
        return $this->_registry;
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
     * @return \Magestore\PurchaseOrderSuccess\Model\PurchaseOrderFactory
     */
    public function getPurchaseOrderFactory(){
        return $this->_purchaseOrderFactory;
    }

    /**
     * @return int
     */
    public function getProductSourceConfig(){
        return $this->productConfig->getProductSource();
    }
}