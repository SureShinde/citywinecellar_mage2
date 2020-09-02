<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Controller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\File\UploaderFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Model\Order\Email\Sender\ShipmentSender;
use Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader;
use Magento\Store\Model\StoreManagerInterface;
use Magestore\DropshipSuccess\Api\Data\SupplierPricelistUploadInterface;
use Magestore\DropshipSuccess\Api\SupplierPricelistUploadRepositoryInterface;
use Magestore\DropshipSuccess\Service\EmailService;
use Magestore\DropshipSuccess\Service\PricelistUploadService;
use Magestore\SupplierSuccess\Api\SupplierRepositoryInterface;
use Magestore\SupplierSuccess\Model\Session;
use Magestore\DropshipSuccess\Api\DropshipRequestRepositoryInterface;
use Magestore\DropshipSuccess\Service\DropshipRequestService;

/**
 * Class AbstractAccount
 * @package Magento\Customer\Controller
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class AbstractSupplier extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Session
     */
    protected $supplierSession;

    /**
     * @var ShipmentLoader
     */
    protected $shipmentLoader;

    /**
     * @var DropshipRequestService
     */
    protected $dropshipRequestService;

    /**
     * @var DropshipRequestRepositoryInterface
     */
    protected $dropshipRequestRepository;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var SupplierRepositoryInterface
     */
    protected $supplierRepository;

    /**
     * @var ShipmentSender
     */
    protected $shipmentSender;

    /**
     * @var EmailService
     */
    protected $emailService;

    /**
     * @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\CollectionFactory
     */
    protected $supplierCollectionFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var PricelistUploadService
     */
    protected $pricelistUploadService;

    /**
     * @var SupplierPricelistUploadInterface
     */
    protected $pricelistUploadInterface;

    /**
     * @var SupplierPricelistUploadRepositoryInterface
     */
    protected $pricelistUploadRepositoryInterface;

    /**
     * AbstractSupplier constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Session $supplierSession
     * @param ShipmentLoader $shipmentLoader
     * @param DropshipRequestService $dropshipRequestService
     * @param DropshipRequestRepositoryInterface $dropshipRequestRepository
     * @param \Magento\Framework\Registry $registry
     * @param SupplierRepositoryInterface $supplierRepository
     * @param ShipmentSender $shipmentSender
     * @param EmailService $emailService
     * @param \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\CollectionFactory $supplierCollectionFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param UploaderFactory $uploaderFactory
     * @param StoreManagerInterface $storeManagerInterface
     * @param PricelistUploadService $pricelistUploadService
     * @param SupplierPricelistUploadInterface $pricelistUploadInterface
     * @param SupplierPricelistUploadRepositoryInterface $pricelistUploadRepositoryInterface
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Session $supplierSession,
        ShipmentLoader $shipmentLoader,
        DropshipRequestService $dropshipRequestService,
        DropshipRequestRepositoryInterface $dropshipRequestRepository,
        \Magento\Framework\Registry $registry,
        SupplierRepositoryInterface $supplierRepository,
        ShipmentSender $shipmentSender,
        EmailService $emailService,
        \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\CollectionFactory $supplierCollectionFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        UploaderFactory $uploaderFactory,
        StoreManagerInterface $storeManagerInterface,
        PricelistUploadService $pricelistUploadService,
        SupplierPricelistUploadInterface $pricelistUploadInterface,
        SupplierPricelistUploadRepositoryInterface $pricelistUploadRepositoryInterface
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->supplierSession = $supplierSession;
        $this->shipmentLoader = $shipmentLoader;
        $this->dropshipRequestService = $dropshipRequestService;
        $this->dropshipRequestRepository = $dropshipRequestRepository;
        $this->coreRegistry = $registry;
        $this->supplierRepository = $supplierRepository;
        $this->shipmentSender = $shipmentSender;
        $this->emailService = $emailService;
        $this->supplierCollectionFactory = $supplierCollectionFactory;
        $this->filesystem = $filesystem;
        $this->_fileFactory = $fileFactory;
        $this->uploaderFactory = $uploaderFactory;
        $this->storeManager = $storeManagerInterface;
        $this->pricelistUploadService = $pricelistUploadService;
        $this->pricelistUploadInterface = $pricelistUploadInterface;
        $this->pricelistUploadRepositoryInterface = $pricelistUploadRepositoryInterface;
    }

    /**
     * check supplier login
     * @return bool|\Magento\Framework\App\ResponseInterface
     */
    public function checkLogin()
    {
        if ($this->supplierSession->isLoggedIn()) {
            return true;
        } else {
            return $this->_redirect('dropship/supplier/login');
        }
    }
}
