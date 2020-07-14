<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Controller\Adminhtml;

use Magento\Framework\Registry;
use Magestore\SupplierSuccess\Model\Locator\LocatorInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Ui\Component\MassAction\Filter;

abstract class AbstractSupplier extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $_resultForwardFactory;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $_resultLayoutFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $_dateFilter;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var \Magestore\SupplierSuccess\Service\Supplier\ProductService
     */
    protected $_supplierProductService;

    /**
     * @var \Magestore\SupplierSuccess\Service\SupplierService
     */
    protected $supplierService;

    /**
     * @var \Magestore\SupplierSuccess\Service\Supplier\PricingListService
     */
    protected $supplierPricingListService;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product\CollectionFactory
     */
    protected $supplierProductCollectionFactory;

    /**
     * @var \Magestore\SupplierSuccess\Api\SupplierProductRepositoryInterface
     */
    protected $supplierProductRepositoryInterface;

    /**
     * @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\CollectionFactory
     */
    protected $supplierCollectionFactory;

    /**
     * @var Filter
     */
    protected $filter;

    /** @var JsonFactory  */
    protected $jsonFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\PricingList\CollectionFactory
     */
    protected $supplierPricingListCollectionFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\DateTime
     */
    protected $dateFilter;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * AbstractSupplier constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param Registry $coreRegistry
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param LocatorInterface $locator
     * @param \Magestore\SupplierSuccess\Service\Supplier\ProductService $supplierProductService
     * @param \Magestore\SupplierSuccess\Service\SupplierService $supplierService
     * @param \Magestore\SupplierSuccess\Service\Supplier\PricingListService $supplierPricingListService
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product\CollectionFactory $supplierProductCollectionFactory
     * @param \Magestore\SupplierSuccess\Api\SupplierProductRepositoryInterface $supplierProductRepositoryInterface
     * @param \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\CollectionFactory $supplierCollectionFactory
     * @param \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\PricingList\CollectionFactory $supplierPricingListCollectionFactory
     * @param Filter $filter
     * @param JsonFactory $jsonFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Stdlib\DateTime\Filter\DateTime $dateFilter
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        Registry $coreRegistry,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        LocatorInterface $locator,
        \Magestore\SupplierSuccess\Service\Supplier\ProductService $supplierProductService,
        \Magestore\SupplierSuccess\Service\SupplierService $supplierService,
        \Magestore\SupplierSuccess\Service\Supplier\PricingListService $supplierPricingListService,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product\CollectionFactory $supplierProductCollectionFactory,
        \Magestore\SupplierSuccess\Api\SupplierProductRepositoryInterface $supplierProductRepositoryInterface,
        \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\CollectionFactory $supplierCollectionFactory,
        \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\PricingList\CollectionFactory $supplierPricingListCollectionFactory,
        Filter $filter,
        JsonFactory $jsonFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Stdlib\DateTime\Filter\DateTime $dateTimeFilter,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultLayoutFactory = $resultLayoutFactory;
        $this->_resultForwardFactory = $resultForwardFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_dateFilter = $dateFilter;
        $this->_fileFactory = $fileFactory;
        $this->locator = $locator;
        $this->_supplierProductService = $supplierProductService;
        $this->supplierService = $supplierService;
        $this->supplierPricingListService = $supplierPricingListService;
        $this->layoutFactory = $layoutFactory;
        $this->resultFactory = $context->getResultFactory();
        $this->supplierProductCollectionFactory = $supplierProductCollectionFactory;
        $this->supplierProductRepositoryInterface = $supplierProductRepositoryInterface;
        $this->supplierCollectionFactory = $supplierCollectionFactory;
        $this->supplierPricingListCollectionFactory = $supplierPricingListCollectionFactory;
        $this->filter = $filter;
        $this->jsonFactory = $jsonFactory;
        $this->filesystem = $filesystem;
        $this->dateFilter = $dateTimeFilter;
        $this->localeDate = $localeDate;
        parent::__construct($context);
    }
}