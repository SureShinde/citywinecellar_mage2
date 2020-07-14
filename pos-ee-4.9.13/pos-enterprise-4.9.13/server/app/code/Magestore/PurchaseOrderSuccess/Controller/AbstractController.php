<?php

namespace Magestore\PurchaseOrderSuccess\Controller;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

abstract class AbstractController extends \Magento\Framework\App\Action\Action {

    protected $purchaseOrderRepository;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Magestore\SupplierSuccess\Api\SupplierRepositoryInterface
     */
    protected $supplierRepository;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    protected $_rootDirectory;

    protected $csvProcessor;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $currencyFactory;

    protected $filesystem;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\Config\TaxAndShipping
     */
    protected $taxShippingService;

    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\File\Csv $csvProcessor,
        \Magestore\PurchaseOrderSuccess\Service\Config\TaxAndShipping $taxShippingService,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magestore\SupplierSuccess\Api\SupplierRepositoryInterface $supplierRepository,
        \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface $purchaseOrderRepository
    ) {
        parent::__construct($context);
        $this->csvProcessor = $csvProcessor;
        $this->filesystem = $filesystem;
        $this->fileFactory = $fileFactory;
        $this->taxShippingService = $taxShippingService;
        $this->supplierRepository = $supplierRepository;
        $this->purchaseOrderRepository = $purchaseOrderRepository;
        $this->_resultPageFactory = $resultPageFactory;
        $this->currencyFactory = $currencyFactory;
        $this->_rootDirectory = $filesystem->getDirectoryRead(DirectoryList::ROOT);
    }
}