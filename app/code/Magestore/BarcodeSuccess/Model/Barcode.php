<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Model;

use Magestore\BarcodeSuccess\Api\Data\BarcodeInterface;

/**
 * Model barcode
 */
class Barcode extends \Magento\Framework\Model\AbstractModel implements BarcodeInterface
{
    protected $_eventPrefix = 'barcode_success';

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $date;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magestore\BarcodeSuccess\Model\ResourceModel\Barcode::class);
    }

    /**
     * Barcode constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Stdlib\DateTime $date
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Stdlib\DateTime $date,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->date = $date;
        $this->dateTime = $dateTime;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Barcode id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->_getData(self::ID);
    }

    /**
     * Set product id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Barcode
     *
     * @return string
     */
    public function getBarcode()
    {
        return $this->_getData(self::BARCODE);
    }

    /**
     * Set barcode
     *
     * @param string $barcode
     * @return $this
     */
    public function setBarcode($barcode)
    {
        return $this->setData(self::BARCODE, $barcode);
    }

    /**
     * Barcode name
     *
     * @return string|null
     */
    public function getQty()
    {
        return $this->_getData(self::QTY);
    }

    /**
     * Set barcode qty
     *
     * @param string $qty
     * @return $this
     */
    public function setQty($qty)
    {
        return $this->setData(self::QTY, $qty);
    }

    /**
     * Barcode product id
     *
     * @return string|null
     */
    public function getProductId()
    {
        return $this->_getData(self::PRODUCT_ID);
    }

    /**
     * Set barcode product id
     *
     * @param string $productId
     * @return $this
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * Barcode product sku
     *
     * @return string|null
     */
    public function getProductSku()
    {
        return $this->_getData(self::PRODUCT_SKU);
    }

    /**
     * Set barcode product sku
     *
     * @param string $productSku
     * @return $this
     */
    public function setProductSku($productSku)
    {
        return $this->setData(self::PRODUCT_SKU, $productSku);
    }

    /**
     * Barcode supplier id
     *
     * @return string|null
     */
    public function getSupplierId()
    {
        return $this->_getData(self::SUPPLIER_ID);
    }

    /**
     * Set barcode supplier id
     *
     * @param string $supplierId
     * @return $this
     */
    public function setSupplierId($supplierId)
    {
        return $this->setData(self::SUPPLIER_ID, $supplierId);
    }

    /**
     * Barcode supplier code
     *
     * @return string|null
     */
    public function getSupplierCode()
    {
        return $this->_getData(self::SUPPLIER_CODE);
    }

    /**
     * Set barcode supplier code
     *
     * @param string $supplierCode
     * @return $this
     */
    public function setSupplierCode($supplierCode)
    {
        return $this->setData(self::SUPPLIER_CODE, $supplierCode);
    }

    /**
     * Barcode $purchasedId
     *
     * @return string|null
     */
    public function getPurchasedId()
    {
        return $this->_getData(self::PURCHASED_ID);
    }

    /**
     * Set barcode $purchasedId
     *
     * @param string $purchasedId
     * @return $this
     */
    public function setPurchasedId($purchasedId)
    {
        return $this->setData(self::PURCHASED_ID, $purchasedId);
    }

    /**
     * Barcode $purchasedTime
     *
     * @return string|null
     */
    public function getPurchasedTime()
    {
        return $this->_getData(self::PURCHASED_TIME);
    }

    /**
     * Set barcode $purchasedTime
     *
     * @param string $purchasedTime
     * @return $this
     */
    public function setPurchasedTime($purchasedTime)
    {
        return $this->setData(self::PURCHASED_TIME, $purchasedTime);
    }

    /**
     * Barcode $historyId
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->_getData(self::CREATE_AT);
    }

    /**
     * Set barcode $createdAt
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATE_AT, $createdAt);
    }

    /**
     * After save
     *
     * @return $this|Barcode
     */
    public function afterSave()
    {
        parent::afterSave();
        try {
            $productId = $this->getProductId();

            $updatedAt = $this->date->formatDate($this->dateTime->gmtTimestamp());
            $productTable = $this->getResource()->getTable('catalog_product_entity');
            $this->getResource()->updateUpdatedTimeOfProduct($productTable, $updatedAt, $productId);

            $isEnabledFlatCatalog = $this->scopeConfig->isSetFlag(
                \Magento\Catalog\Model\Indexer\Product\Flat\State::INDEXER_ENABLED_XML_PATH,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            if ($isEnabledFlatCatalog) {
                $storeManagerDataList = $this->storeManager->getStores();
                foreach ($storeManagerDataList as $store) {
                    $productTable = $this->getResource()->getTable('catalog_product_flat_' . $store->getId());
                    $this->getResource()->updateUpdatedTimeOfProduct($productTable, $updatedAt, $productId);
                }
            }
        } catch (\Exception $exception) {
            return $this;
        }
        return $this;
    }
}
