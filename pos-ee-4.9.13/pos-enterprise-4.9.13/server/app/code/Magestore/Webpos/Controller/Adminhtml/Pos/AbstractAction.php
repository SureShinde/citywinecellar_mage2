<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Controller\Adminhtml\Pos;

/**
 * Class AbstractAction
 * @package Magestore\Webpos\Controller\Adminhtml
 */
abstract class AbstractAction extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * @var \Magento\Framework\Logger\Monolog
     */
    protected $logger;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magestore\Webpos\Api\Data\Pos\PosInterfaceFactory
     */
    protected $posInterfaceFactory;

    /**
     * @var \Magestore\Webpos\Model\Pos\PosRepository
     */
    protected $posRepository;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;
    /**
     * @var \Magestore\Webpos\Api\Staff\SessionRepositoryInterface
     */
    protected $sessionRepository;
    /**
     * @var \Magestore\Appadmin\Api\Event\DispatchServiceInterface
     */
    protected $dispatchService;

    /**
     * @var \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface
     */
    protected $staffRepository;

    /**
     * @var \Magestore\Webpos\Api\Shift\ShiftRepositoryInterface
     */
    protected $shiftRepository;
    /**
     * @var \Magestore\Webpos\Model\Shift\ShiftFactory
     */
    protected $_shiftFactory;
    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Shift\CashTransaction
     */
    protected $cashTransactionResource;
    /**
     * @var \Magestore\Webpos\Api\Data\Shift\CashTransactionInterface
     */
    protected $cashTransactionInterface;
    /**
     * @var \Magestore\Webpos\Api\Data\Shift\ShiftInterface
     */
    protected $shipInterface;

    /** @var $shiftResource  \Magestore\Webpos\Model\ResourceModel\Shift\Shift */
    protected $shiftResource;

    /** @var \Magestore\Webpos\Model\ResourceModel\Shift\Shift\CollectionFactory */
    protected $_shiftCollectionFactory;

    /**
     * AbstractAction constructor.
     * @param Context $context
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param \Magestore\Webpos\Helper\Data $helper
     * @param \Magestore\Webpos\Api\Data\Pos\PosInterfaceFactory $posInterfaceFactory
     * @param \Magestore\Webpos\Api\Pos\PosRepositoryInterface $posRepository
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Magestore\Webpos\Api\Staff\SessionRepositoryInterface $sessionRepository
     * @param \Magestore\Appadmin\Api\Event\DispatchServiceInterface $dispatchService
     * @param \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface $staffRepository
     * @param \Magestore\Webpos\Api\Shift\ShiftRepositoryInterface $shiftRepository
     * @param \Magestore\Webpos\Model\ResourceModel\Shift\CashTransaction $cashTransactionResource
     * @param \Magestore\Webpos\Model\Shift\ShiftFactory $shiftFactory
     * @param \Magestore\Webpos\Api\Data\Shift\CashTransactionInterface $cashTransactionInterface
     * @param \Magestore\Webpos\Api\Data\Shift\ShiftInterface $shipInterface
     */
    public function __construct(
        \Magestore\Webpos\Controller\Adminhtml\Pos\Context $context,
        \Magento\Framework\View\Page\Config $pageConfig,
        \Magestore\Webpos\Helper\Data $helper,
        \Magestore\Webpos\Api\Data\Pos\PosInterfaceFactory $posInterfaceFactory,
        \Magestore\Webpos\Api\Pos\PosRepositoryInterface $posRepository,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magestore\Webpos\Api\Staff\SessionRepositoryInterface $sessionRepository,
        \Magestore\Appadmin\Api\Event\DispatchServiceInterface $dispatchService,
        \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface $staffRepository,
        \Magestore\Webpos\Api\Shift\ShiftRepositoryInterface $shiftRepository,
        \Magestore\Webpos\Model\ResourceModel\Shift\CashTransaction $cashTransactionResource,
        \Magestore\Webpos\Model\Shift\ShiftFactory $shiftFactory,
        \Magestore\Webpos\Api\Data\Shift\CashTransactionInterface $cashTransactionInterface,
        \Magestore\Webpos\Api\Data\Shift\ShiftInterface $shipInterface,
        \Magestore\Webpos\Model\ResourceModel\Shift\Shift $shiftResource,
        \Magestore\Webpos\Model\ResourceModel\Shift\Shift\CollectionFactory $shiftCollectionFactory
    ) {
        parent::__construct($context);
        $this->eventManager = $context->getEventManager();
        $this->coreRegistry = $context->getCoreRegistry();
        $this->resultForwardFactory = $context->getResultForwardFactory();
        $this->resultPageFactory = $context->getResultPageFactory();
        $this->resultLayoutFactory = $context->getResultLayoutFactory();
        $this->logger = $context->getLogger();
        $this->pageConfig = $pageConfig;
        $this->helper = $helper;
        $this->posInterfaceFactory = $posInterfaceFactory;
        $this->posRepository = $posRepository;
        $this->jsonFactory = $jsonFactory;
        $this->sessionRepository = $sessionRepository;
        $this->dispatchService = $dispatchService;
        $this->staffRepository = $staffRepository;
        $this->shiftRepository = $shiftRepository;
        $this->_shiftFactory = $shiftFactory;
        $this->cashTransactionResource = $cashTransactionResource;
        $this->cashTransactionInterface = $cashTransactionInterface;
        $this->shipInterface = $shipInterface;
        $this->shiftResource = $shiftResource;
        $this->_shiftCollectionFactory = $shiftCollectionFactory;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Webpos::pos');
    }
}