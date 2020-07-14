<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Controller\Adminhtml;

/**
 * Class AbstractAction
 * @package Magestore\TransferStock\Controller\Adminhtml
 */
abstract class AbstractAction extends \Magento\Backend\App\Action
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

    protected $_session;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * AbstractAction constructor.
     *
     * @param Context $context
     */
    public function __construct(
        \Magestore\TransferStock\Controller\Adminhtml\Context $context
    ) {
        parent::__construct($context);
        $this->_eventManager = $context->getEventManager();
        $this->_coreRegistry = $context->getCoreRegistry();
        $this->_resultForwardFactory = $context->getResultForwardFactory();
        $this->_resultPageFactory = $context->getResultPageFactory();
        $this->_resultLayoutFactory = $context->getResultLayoutFactory();
        $this->_logger = $context->getLogger();
        $this->inventoryTransferResource = $context->getInventoryTransferResource();
        $this->inventoryTransferFactory = $context->getInventoryTransferFactory();
        $this->authSession = $context->getAuthSession();
        $this->transferManagement = $context->getTransferManagement();
        $this->_session = $context->getSession();
        $this->filesystem = $context->getFileSystem();
        $this->fileFactory = $context->getFileFactory();
    }
}
