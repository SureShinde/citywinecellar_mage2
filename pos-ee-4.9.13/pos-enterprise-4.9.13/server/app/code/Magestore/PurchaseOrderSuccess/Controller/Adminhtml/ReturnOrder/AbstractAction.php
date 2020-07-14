<?php

namespace Magestore\PurchaseOrderSuccess\Controller\Adminhtml\ReturnOrder;

use Magento\Backend\App\Action;
use Magento\InventoryApi\Api\SourceRepositoryInterface;

abstract class AbstractAction extends \Magento\Backend\App\Action {
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
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Model\ReturnOrderFactory
     */
    protected $_returnOrderFactory;

    /**
     * @var \Magento\Framework\Logger\Monolog
     */
    protected $_logger;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\ReturnOrderRepositoryInterface
     */
    protected $returnOrderRepository;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\ReturnOrder\ReturnOrderService
     */
    protected $returnService;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\ReturnOrder\Item\ItemService
     */
    protected $itemService;

    /**
     * @var \Magestore\SupplierSuccess\Api\SupplierRepositoryInterface
     */
    protected $supplierRepository;
    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\ReturnOrderItemRepositoryInterface
     */
    protected $returnItemRepository;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\DateTime
     */
    protected $dateFilter;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;
    /**
     * @var SourceRepositoryInterface
     */
    protected $sourceRepository;

    /**
     * AbstractAction constructor.
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Framework\Logger\Monolog $logger
     * @param \Magestore\PurchaseOrderSuccess\Model\ReturnOrderFactory $returnOrderFactory
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Magestore\PurchaseOrderSuccess\Api\ReturnOrderRepositoryInterface $returnOrderRepository
     * @param \Magestore\PurchaseOrderSuccess\Service\ReturnOrder\ReturnOrderService $returnService
     * @param \Magestore\PurchaseOrderSuccess\Service\ReturnOrder\Item\ItemService $itemService
     * @param \Magestore\SupplierSuccess\Api\SupplierRepositoryInterface $supplierRepository
     * @param \Magestore\PurchaseOrderSuccess\Api\ReturnOrderItemRepositoryInterface $returnItemRepository
     * @param Action\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Framework\Stdlib\DateTime\Filter\DateTime $dateFilter
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param SourceRepositoryInterface $sourceRepository
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Logger\Monolog $logger,
        \Magestore\PurchaseOrderSuccess\Model\ReturnOrderFactory $returnOrderFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magestore\PurchaseOrderSuccess\Api\ReturnOrderRepositoryInterface $returnOrderRepository,
        \Magestore\PurchaseOrderSuccess\Service\ReturnOrder\ReturnOrderService $returnService,
        \Magestore\PurchaseOrderSuccess\Service\ReturnOrder\Item\ItemService $itemService,
        \Magestore\SupplierSuccess\Api\SupplierRepositoryInterface $supplierRepository,
        \Magestore\PurchaseOrderSuccess\Api\ReturnOrderItemRepositoryInterface $returnItemRepository,
        Action\Context $context,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Stdlib\DateTime\Filter\DateTime $dateFilter,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        SourceRepositoryInterface $sourceRepository
    ) {
        parent::__construct($context);
        $this->_registry = $registry;
        $this->_resultForwardFactory = $resultForwardFactory;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultLayoutFactory = $resultLayoutFactory;
        $this->_returnOrderFactory = $returnOrderFactory;
        $this->dataPersistor = $dataPersistor;
        $this->returnOrderRepository = $returnOrderRepository;
        $this->returnService = $returnService;
        $this->itemService = $itemService;
        $this->supplierRepository = $supplierRepository;
        $this->returnItemRepository = $returnItemRepository;
        $this->_logger = $logger;
        $this->timezone = $timezone;
        $this->dateFilter = $dateFilter;
        $this->localeDate = $localeDate;
        $this->sourceRepository = $sourceRepository;

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
     * Redirect to grid quotation or purchase order
     *
     * @return $this
     */
    public function redirectGrid($message = null){
        $resultRedirect = $this->resultRedirectFactory->create();
        if($message)
            $this->messageManager->addErrorMessage($message);
        $controllerName = 'returnOrder';
        return $resultRedirect->setPath('*/'.$controllerName.'/');
    }

    /**
     * @param null $id
     * @return $this
     */
    public function redirectForm($id = null, $message = null, $messageType = 'success'){
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
        $controllerName = 'returnOrder';
        $action = $id?'view':'new';
        $params = $id?['id' => $id]:[];
        return $resultRedirect->setPath('*/'.$controllerName.'/'. $action, $params);
    }
}