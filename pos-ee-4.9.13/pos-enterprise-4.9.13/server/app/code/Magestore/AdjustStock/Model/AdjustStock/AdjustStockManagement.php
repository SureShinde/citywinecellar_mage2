<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Model\AdjustStock;

use Magestore\AdjustStock\Api\AdjustStock\AdjustStockManagementInterface;
use Magestore\AdjustStock\Api\Data\AdjustStock\AdjustStockInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\InventorySourceDeductionApi\Model\GetSourceItemBySourceCodeAndSku;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;
use Magento\InventoryImportExport\Model\Import\SourceItemConvert;
use Magento\InventoryImportExport\Model\Import\Sources;
use Magestore\AdjustStock\Model\ResourceModel\AdjustStock\GlobalStock\Collection as GlobalStockCollection;
use Magestore\AdjustStock\Model\ResourceModel\AdjustStock\Product\Collection as AdjustProductCollection;

/**
 * Class AdjustStockManagement
 *
 * @package Magestore\AdjustStock\Model\AdjustStock
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class AdjustStockManagement implements AdjustStockManagementInterface
{

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     *
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * @var \Magestore\AdjustStock\Api\IncrementIdManagementInterface
     */
    protected $_incrementIdManagement;

    /**
     * @var \Magento\InventoryApi\Api\SourceRepositoryInterface
     */
    protected $sourceRepository;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magestore\AdjustStock\Model\AdjustStockFactory
     */
    protected $adjustStockFactory;
    /**
     * @var \Magestore\AdjustStock\Model\AdjustStock\ProductFactory
     */
    protected $adjustStockProductFactory;
    /**
     * @var \Magestore\AdjustStock\Model\ResourceModel\AdjustStock\Product
     */
    protected $adjustStockProductResource;
    /**
     * @var GetSourceItemBySourceCodeAndSku
     */
    protected $getSourceItemBySourceCodeAndSku;
    /**
     * @var SourceItemsSaveInterface
     */
    protected $sourceItemsSave;
    /**
     * @var \Magestore\AdjustStock\Api\MultiSourceInventory\StockManagementInterface
     */
    protected $stockManagement;
    /**
     * @var array|null
     */
    protected $data;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    /**
     * @var SourceItemConvert
     */
    private $sourceItemConvert;

    /**
     * AdjustStockManagement constructor.
     * @param \Magestore\AdjustStock\Api\IncrementIdManagementInterface $incrementIdManagement
     * @param DateTime $dateTime
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magestore\AdjustStock\Model\AdjustStockFactory $adjustStockFactory
     * @param ProductFactory $adjustStockProductFactory
     * @param \Magestore\AdjustStock\Model\ResourceModel\AdjustStock\Product $adjustStockProductResource
     * @param GetSourceItemBySourceCodeAndSku $getSourceItemBySourceCodeAndSku
     * @param SourceItemsSaveInterface $sourceItemsSave
     * @param \Magestore\AdjustStock\Api\MultiSourceInventory\StockManagementInterface $stockManagement
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param SourceItemConvert $sourceItemConvert
     * @param array|null $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magestore\AdjustStock\Api\IncrementIdManagementInterface $incrementIdManagement,
        DateTime $dateTime,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magestore\AdjustStock\Model\AdjustStockFactory $adjustStockFactory,
        \Magestore\AdjustStock\Model\AdjustStock\ProductFactory $adjustStockProductFactory,
        \Magestore\AdjustStock\Model\ResourceModel\AdjustStock\Product $adjustStockProductResource,
        GetSourceItemBySourceCodeAndSku $getSourceItemBySourceCodeAndSku,
        SourceItemsSaveInterface $sourceItemsSave,
        \Magestore\AdjustStock\Api\MultiSourceInventory\StockManagementInterface $stockManagement,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        SourceItemConvert $sourceItemConvert,
        array $data = null
    ) {
        $this->_incrementIdManagement = $incrementIdManagement;
        $this->dateTime = $dateTime;
        $this->authSession = $authSession;
        $this->sourceRepository = $sourceRepository;
        $this->scopeConfig = $scopeConfig;
        $this->adjustStockFactory = $adjustStockFactory;
        $this->adjustStockProductFactory = $adjustStockProductFactory;
        $this->adjustStockProductResource = $adjustStockProductResource;
        $this->getSourceItemBySourceCodeAndSku = $getSourceItemBySourceCodeAndSku;
        $this->sourceItemsSave = $sourceItemsSave;
        $this->stockManagement = $stockManagement;
        $this->_objectManager = $objectManager;
        $this->sourceItemConvert = $sourceItemConvert;
        $this->data = $data;
    }

    /**
     * Create adjustment
     *
     * @param AdjustStockInterface $adjustStock
     * @param array $data
     * @return AdjustStockInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function createAdjustment(AdjustStockInterface $adjustStock, $data)
    {
        $this->data = $data;
        $adjustStockCode = isset($data[AdjustStockInterface::ADJUSTSTOCK_CODE]) ?
            $data[AdjustStockInterface::ADJUSTSTOCK_CODE] :
            $this->generateCode();

        if (!$adjustStock->getId()) {
            $createdAt = isset($data[AdjustStockInterface::CREATED_AT]) ?
                $data[AdjustStockInterface::CREATED_AT] :
                $this->dateTime->gmtDate();
            $curUser = $this->authSession->getUser();
            $createdBy = isset($data[AdjustStockInterface::CREATED_BY]) ?
                $data[AdjustStockInterface::CREATED_BY] :
                ($curUser ? $curUser->getUserName() : null);

            /* load warehouse data if $data[AdjustStockInterface::WAREHOUSE_NAME] is null */
            if (!isset($data[AdjustStockInterface::SOURCE_NAME])) {
                $warehouse = $this->sourceRepository->get($data[AdjustStockInterface::SOURCE_CODE]);
                $data[AdjustStockInterface::SOURCE_NAME] = $warehouse->getName();
                $data[AdjustStockInterface::SOURCE_CODE] = $warehouse->getSourceCode();
            }

            /* prepare data for stock adjustment */
            $adjustStock->setReason($data[AdjustStockInterface::REASON])
                ->setStatus(AdjustStockInterface::STATUS_PENDING)
                ->setSourceCode($data[AdjustStockInterface::SOURCE_CODE])
                ->setSourceName($data[AdjustStockInterface::SOURCE_NAME])
                ->setCreatedAt($createdAt)
                ->setCreatedBy($createdBy)
                ->setAdjuststockCode($adjustStockCode);
        } else {
            $adjustStock->setReason($data[AdjustStockInterface::REASON])
                ->setAdjuststockCode($adjustStockCode);
        }

        $this->createSelection($adjustStock, $data);

        return $adjustStock;
    }

    /**
     * Modify adjust products data
     *
     * @param AdjustStockInterface $adjustStock
     * @param array $data
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function createSelection($adjustStock, $data)
    {
        $products = $data['products'];
        $adjustStock->getResource()->save($adjustStock);

        // clear stock adjustment products before update product data
        /** @var AdjustProductCollection $adjustStockProductCollection */
        $adjustStockProductCollection = $this->adjustStockProductFactory->create()->getCollection();
        $adjustStockProductCollection->addFieldToFilter('adjuststock_id', $adjustStock->getId());
        foreach ($adjustStockProductCollection as $item) {
            $this->adjustStockProductResource->delete($item);
        }

        foreach ($products as $product) {
            /** @var \Magestore\AdjustStock\Api\Data\AdjustStock\ProductInterface $adjustStockProduct */
            $adjustStockProduct = $this->adjustStockProductFactory->create();
            $adjustStockProduct->setData($product);
            $adjustStockProduct->setAdjuststockId($adjustStock->getId());

            $this->adjustStockProductResource->save($adjustStockProduct);
        }
    }

    /**
     * Generate unique code of Stock Adjustment
     *
     * @return string
     */
    public function generateCode()
    {
        return $this->_incrementIdManagement->getNextCode(AdjustStockInterface::PREFIX_CODE);
    }

    /**
     * @inheritdoc
     */
    public function checkAdjustmentCode($adjustmentId, $adjustmentCode)
    {
        /** @var \Magestore\AdjustStock\Model\ResourceModel\AdjustStock\Collection $collection */
        $collection = $this->adjustStockFactory->create()->getCollection();
        $collection->addFieldToFilter('adjuststock_code', $adjustmentCode);
        if ($adjustmentId) {
            $collection->addFieldToFilter('adjuststock_id', ['neq' => $adjustmentId]);
        }

        if ($collection->getSize()) {
            return false;
        }
        return true;
    }

    /**
     * Complete a stock adjustment
     *
     * @param AdjustStockInterface $adjustStock
     */
    public function complete(AdjustStockInterface $adjustStock)
    {
        // Correct current qty in source before complete adjustment
        $this->adjustStockProductResource->correctCurrectQty($adjustStock);

        /** @var AdjustProductCollection $adjustStockProductCollection */
        $adjustStockProductCollection = $this->adjustStockProductFactory->create()->getCollection();
        $adjustStockProductCollection->addFieldToFilter('adjuststock_id', $adjustStock->getId());
        $productData = [];
        if ($adjustStockProductCollection->getSize()) {
            foreach ($adjustStockProductCollection as $product) {
                $productData[$product->getProductSku()] = $product->getNewQty();
            }
        }

        // apply stock adjustment
        $this->applyStockAdjustment($adjustStock, $productData);

        /* mark as completed */
        $confirmedAt = isset($this->data[AdjustStockInterface::CONFIRMED_AT]) ?
                $this->data[AdjustStockInterface::CONFIRMED_AT] :
            $this->dateTime->gmtDate();
        $curUser = $this->authSession->getUser();
        $confirmedBy = isset($this->data[AdjustStockInterface::CONFIRMED_BY]) ?
                $this->data[AdjustStockInterface::CONFIRMED_BY] :
                ($curUser ? $curUser->getUserName() : null);

        $adjustStock->setStatus(AdjustStockInterface::STATUS_COMPLETED)
                ->setConfirmedBy($confirmedBy)
                ->setConfirmedAt($confirmedAt);
        $adjustStock->getResource()->save($adjustStock);
    }

    /**
     * Change quantity in source
     *
     * @param AdjustStockInterface $adjustStock
     * @param array $productData
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Validation\ValidationException
     */
    public function applyStockAdjustment($adjustStock, $productData)
    {
        if (!$adjustStock->getId() || !$adjustStock->getSourceCode()) {
            return;
        }

        $sourceItems = [];
        $packItems = 0;
        $i = 0;

        foreach ($productData as $sku => $newQty) {
            $sourceItem = [
                Sources::COL_SOURCE_CODE => $adjustStock->getSourceCode(),
                Sources::COL_SKU => (string)$sku,
                Sources::COL_QTY => $newQty,
                Sources::COL_STATUS => 1
            ];
            $sourceItems[$packItems][] = $sourceItem;
            $i++;
            if ($i == 500) {
                $i = 0;
                $packItems++;
            }
        }

        if (!empty($sourceItems)) {
            foreach ($sourceItems as $items) {
                $items = $this->sourceItemConvert->convert($items);
                $this->sourceItemsSave->execute($items);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function isShowThumbnail()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getSelectBarcodeProductListJson($adjustStockId = null)
    {
        $result = [];
        $collection = $this->getProductCollection();
        if (!$collection->isLoaded()) {
            $collection->load();
        }
        $items = $collection->toArray();

        foreach ($items as $item) {
            if (isset($item['barcode'])) {
                $barcodes = explode(',', (string)$item['barcode']);
                foreach ($barcodes as $barcode) {
                    $result[$barcode] = $item;
                }
            }
        }
        // set image url
        $storeManager = $this->_objectManager->get(\Magento\Store\Model\StoreManagerInterface::class);
        $path = $storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
        foreach ($result as &$item) {
            if (isset($item['image'])) {
                $item['image_url'] = $path . 'catalog/product' . $item['image'];
            }
        }

        return $this->_objectManager
            ->create(\Magento\Framework\Json\EncoderInterface::class)
            ->encode($result);
    }

    /**
     * @inheritdoc
     */
    public function getProductCollection()
    {
        $collection = $this->_objectManager->create(GlobalStockCollection::class);

        return $collection;
    }
}
