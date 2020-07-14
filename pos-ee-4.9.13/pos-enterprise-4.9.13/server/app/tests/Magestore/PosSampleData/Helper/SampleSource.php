<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosSampleData\Helper;
use Magento\Framework\App\ResourceConnection;
/**
 * Class PurchaseOrderData
 * @package Magestore\PosSampleData\Helper
 */
class SampleSource extends \Magento\Framework\App\Helper\AbstractHelper
{
    const TESTING_SOURCE_CODE = 'testing_source';

    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;
    /**
     * @var \Magento\Inventory\Model\SourceItemFactory
     */
    protected $collectionFactory;
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;
    /**
     * @var \Magento\InventoryApi\Api\Data\SourceInterface
     */
    protected $sourceInterface;
    /**
     * @var \Magento\InventoryApi\Api\SourceRepositoryInterface
     */
    protected $sourceRepository;
    /**
     * @var \Magestore\PurchaseOrderSuccess\Model\MultiSourceInventory\StockManagement
     */
    protected $stockManagement;
    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;
    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * SampleSource constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Inventory\Model\ResourceModel\SourceItem\CollectionFactory $collectionFactory
     * @param ResourceConnection $resourceConnection
     * @param \Magento\InventoryApi\Api\Data\SourceInterface $sourceInterface
     * @param \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository
     * @param \Magestore\PurchaseOrderSuccess\Model\MultiSourceInventory\StockManagement $stockManagement
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Magento\Framework\App\State $appState
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Inventory\Model\ResourceModel\SourceItem\CollectionFactory $collectionFactory,
        ResourceConnection $resourceConnection,
        \Magento\InventoryApi\Api\Data\SourceInterface $sourceInterface,
        \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository,
        \Magestore\PurchaseOrderSuccess\Model\MultiSourceInventory\StockManagement $stockManagement,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\App\State $appState
    )
    {
        parent::__construct($context);
        $this->objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        $this->collectionFactory = $collectionFactory;
        $this->resourceConnection = $resourceConnection;
        $this->sourceInterface = $sourceInterface;
        $this->sourceRepository = $sourceRepository;
        $this->stockManagement = $stockManagement;
        $this->productMetadata = $productMetadata;
        $this->_appState = $appState;
    }

    /**
     * Create new source and add some products to it
     */
    public function execute() {
        $version = $this->productMetadata->getVersion();

        try {
            if (version_compare($version, '2.2.0', '>=') || $version === 'No version set (parsed as 1.0.0)') {
                $this->_appState->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
            } else {
                $this->_appState->setAreaCode('admin');
            }
        } catch (\Exception $e) {
            $this->_appState->getAreaCode();
        }

        $this->createSourceForTest();
        $this->addProductsToSource();
    }

    /**
     * create testing source
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Validation\ValidationException
     */
    protected function createSourceForTest() {
        $source = $this->sourceInterface;
        $source->setSourceCode(self::TESTING_SOURCE_CODE);
        $source->setName('Testing Source');
        $source->setCountryId('US');
        $source->setPostcode('12345-4321');

        $this->sourceRepository->save($source);
    }

    /**
     * Add some products to source
     */
    protected function addProductsToSource() {
        $this->stockManagement->createSourceItem('24-WG01', self::TESTING_SOURCE_CODE);
    }
}