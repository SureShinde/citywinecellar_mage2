<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Inventory\Stock;

use Magento\CatalogInventory\Api\StockConfigurationInterface as StockConfigurationInterface;

/**
 * Class StockItemRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Item extends \Magento\Framework\DataObject
    implements \Magestore\Webpos\Api\Data\Inventory\StockItemInterface
{

    /**
     * @var StockConfigurationInterface
     */
    protected $stockConfiguration;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Customer group id
     *
     * @var int|null
     */
    protected $customerGroupId;

    /**
     * @var float|false
     */
    protected $qtyIncrements;

    /**
     * Item constructor.
     * @param StockConfigurationInterface $stockConfiguration
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        StockConfigurationInterface $stockConfiguration,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    )
    {
        $this->stockConfiguration = $stockConfiguration;
        $this->customerSession = $customerSession;
        parent::__construct($data);
    }

    /**
     * @inheritDoc
     */
    public function getItemId()
    {
        return $this->_getData(self::ITEM_ID);
    }

    /**
     * @inheritDoc
     */
    public function getStockId()
    {
        return $this->_getData(self::STOCK_ID);
    }

    /**
     * @inheritDoc
     */
    public function getProductId()
    {
        return (int) $this->_getData(self::PRODUCT_ID);
    }

    /**
     * @inheritDoc
     */
    public function getQty()
    {
        return null === $this->_getData(self::QTY) ? null : (float)$this->_getData(self::QTY);
    }

    /**
     * @inheritDoc
     */
    public function getIsInStock()
    {
        if (!$this->getManageStock()) {
            return true;
        }
        return (bool) $this->_getData(self::IS_IN_STOCK);
    }

    /**
     * @inheritDoc
     */
    public function getIsQtyDecimal()
    {
        return (bool) $this->_getData(self::IS_QTY_DECIMAL);
    }

    /**
     * @inheritDoc
     */
    public function getUseConfigMinQty()
    {
        return (bool) $this->_getData(self::USE_CONFIG_MIN_QTY);
    }

    /**
     * @inheritDoc
     */
    public function getMinQty()
    {
        if ($this->getUseConfigMinQty()) {
            $minQty = $this->stockConfiguration->getMinQty($this->getStoreId());
        } else {
            $minQty = (float)$this->getData(self::MIN_QTY);
        }
        return $minQty;
    }

    /**
     * @inheritDoc
     */
    public function getUseConfigMinSaleQty()
    {
        return (bool) $this->_getData(self::USE_CONFIG_MIN_SALE_QTY);
    }

    public function getCustomerGroupId()
    {
        if ($this->customerGroupId === null) {
            return $this->customerSession->getCustomerGroupId();
        }
        return $this->customerGroupId;
    }

    /**
     * @inheritDoc
     */
    public function getMinSaleQty()
    {
        if ($this->getUseConfigMinSaleQty()) {
            $customerGroupId = $this->getCustomerGroupId();
            $minSaleQty = $this->stockConfiguration->getMinSaleQty($this->getStoreId(), $customerGroupId);
        } else {
            $minSaleQty = (float) $this->getData(self::MIN_SALE_QTY);
        }
        return $minSaleQty;
    }

    /**
     * @inheritDoc
     */
    public function getUseConfigMaxSaleQty()
    {
        return (bool) $this->_getData(self::USE_CONFIG_MAX_SALE_QTY);
    }

    /**
     * @inheritDoc
     */
    public function getMaxSaleQty()
    {
        if ($this->getUseConfigMaxSaleQty()) {
            $customerGroupId = $this->getCustomerGroupId();
            $maxSaleQty = $this->stockConfiguration->getMaxSaleQty($this->getStoreId(), $customerGroupId);
        } else {
            $maxSaleQty = (float) $this->getData(self::MAX_SALE_QTY);
        }
        return $maxSaleQty;
    }

    /**
     * @inheritDoc
     */
    public function getUseConfigEnableQtyInc()
    {
        return (bool) $this->_getData(self::USE_CONFIG_ENABLE_QTY_INC);
    }

    /**
     * @inheritDoc
     */
    public function getEnableQtyIncrements()
    {
        if ($this->getUseConfigEnableQtyInc()) {
            return $this->stockConfiguration->getEnableQtyIncrements($this->getStoreId());
        }
        return (bool) $this->getData(self::ENABLE_QTY_INCREMENTS);
    }

    /**
     * @inheritDoc
     */
    public function getUseConfigQtyIncrements()
    {
        return (bool) $this->_getData(self::USE_CONFIG_QTY_INCREMENTS);
    }

    /**
     * @inheritDoc
     */
    public function getQtyIncrements()
    {
        if ($this->qtyIncrements === null) {
            if ($this->getEnableQtyIncrements()) {
                if ($this->getUseConfigQtyIncrements()) {
                    $this->qtyIncrements = $this->stockConfiguration->getQtyIncrements($this->getStoreId());
                } else {
                    $this->qtyIncrements = $this->getData(self::QTY_INCREMENTS);
                }

                if ($this->getIsQtyDecimal()) { // Cast accordingly to decimal qty usage
                    $this->qtyIncrements = (float) $this->qtyIncrements;
                } else {
                    $this->qtyIncrements = (int) $this->qtyIncrements;
                }
            }
            if ($this->qtyIncrements <= 0) {
                $this->qtyIncrements = false;
            }
        }
        return $this->qtyIncrements;
    }

    /**
     * @inheritDoc
     */
    public function getUseConfigBackorders()
    {
        return (bool) $this->_getData(self::USE_CONFIG_BACKORDERS);
    }

    /**
     * @inheritDoc
     */
    public function getBackorders()
    {
        if ($this->getUseConfigBackorders()) {
            return $this->stockConfiguration->getBackorders($this->getStoreId());
        }
        return (int) $this->getData(self::BACKORDERS);
    }

    /**
     * @inheritDoc
     */
    public function getUseConfigManageStock()
    {
        return (bool) $this->_getData(self::USE_CONFIG_MANAGE_STOCK);
    }

    /**
     * @inheritDoc
     */
    public function getManageStock()
    {
        if ($this->getUseConfigManageStock()) {
            return $this->stockConfiguration->getManageStock($this->getStoreId());
        }
        return (int) $this->getData(self::MANAGE_STOCK);
    }

    /**
     * @inheritDoc
     */
    public function setItemId($itemId)
    {
        return $this->setData(self::ITEM_ID, $itemId);
    }

    /**
     * @inheritDoc
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * @inheritDoc
     */
    public function setStockId($stockId)
    {
        return $this->setData(self::STOCK_ID, $stockId);
    }

    /**
     * @inheritDoc
     */
    public function setQty($qty)
    {
        return $this->setData(self::QTY, $qty);
    }

    /**
     * @inheritDoc
     */
    public function setIsInStock($isInStock)
    {
        return $this->setData(self::IS_IN_STOCK, $isInStock);
    }

    /**
     * @inheritDoc
     */
    public function setIsQtyDecimal($isQtyDecimal)
    {
        return $this->setData(self::IS_QTY_DECIMAL, $isQtyDecimal);
    }

    /**
     * @inheritDoc
     */
    public function setUseConfigMinQty($useConfigMinQty)
    {
        return $this->setData(self::USE_CONFIG_MIN_QTY, $useConfigMinQty);
    }

    /**
     * @inheritDoc
     */
    public function setMinQty($minQty)
    {
        return $this->setData(self::MIN_QTY, $minQty);
    }

    /**
     * @inheritDoc
     */
    public function setUseConfigMinSaleQty($useConfigMinSaleQty)
    {
        return $this->setData(self::USE_CONFIG_MIN_SALE_QTY, $useConfigMinSaleQty);
    }

    /**
     * @inheritDoc
     */
    public function setMinSaleQty($minSaleQty)
    {
        return $this->setData(self::MIN_SALE_QTY, $minSaleQty);
    }

    /**
     * @inheritDoc
     */
    public function setUseConfigMaxSaleQty($useConfigMaxSaleQty)
    {
        return $this->setData(self::USE_CONFIG_MAX_SALE_QTY, $useConfigMaxSaleQty);
    }

    /**
     * @inheritDoc
     */
    public function setMaxSaleQty($maxSaleQty)
    {
        return $this->setData(self::MAX_SALE_QTY, $maxSaleQty);
    }

    /**
     * @inheritDoc
     */
    public function setUseConfigBackorders($useConfigBackorders)
    {
        return $this->setData(self::USE_CONFIG_BACKORDERS, $useConfigBackorders);
    }

    /**
     * @inheritDoc
     */
    public function setBackorders($backOrders)
    {
        return $this->setData(self::BACKORDERS, $backOrders);
    }

    /**
     * @inheritDoc
     */
    public function setUseConfigQtyIncrements($useConfigQtyIncrements)
    {
        return $this->setData(self::USE_CONFIG_QTY_INCREMENTS, $useConfigQtyIncrements);
    }

    /**
     * @inheritDoc
     */
    public function setQtyIncrements($qtyIncrements)
    {
        return $this->setData(self::QTY_INCREMENTS, $qtyIncrements);
    }

    /**
     * @inheritDoc
     */
    public function setUseConfigEnableQtyInc($useConfigEnableQtyInc)
    {
        return $this->setData(self::USE_CONFIG_ENABLE_QTY_INC, $useConfigEnableQtyInc);
    }

    /**
     * @inheritDoc
     */
    public function setEnableQtyIncrements($enableQtyIncrements)
    {
        return $this->setData(self::ENABLE_QTY_INCREMENTS, $enableQtyIncrements);
    }

    /**
     * @inheritDoc
     */
    public function setUseConfigManageStock($useConfigManageStock)
    {
        return $this->setData(self::USE_CONFIG_MANAGE_STOCK, $useConfigManageStock);
    }

    /**
     * @inheritDoc
     */
    public function setManageStock($manageStock)
    {
        return $this->setData(self::MANAGE_STOCK, $manageStock);
    }

    public function getName() {
        return $this->getData('name');
    }

    public function getSku() {
        return $this->getData('sku');
    }

    public function setName($name) {
        $this->setData('name', $name);
        return $this;
    }

    public function setSku($sku) {
        $this->setData('sku', $sku);
        return $this;        
    }

    public function getUpdatedTime() {
        return $this->getData('updated_time');
    }

    public function setUpdatedTime($updatedTime) {
        $this->setData('updated_time', $updatedTime);
        return $this;        
    }
    /**
     * @inheritDoc
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Inventory\StockItemExtensionInterface $extensionAttributes
    ){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }

    /**
     * @inheritDoc
     */
    public function getQuantity()
    {
        return $this->getData(self::QUANTITY);
    }


    /**
     * @inheritDoc
     */
    public function setQuantity($qtyInLocation)
    {
        return $this->setData(self::QUANTITY, $qtyInLocation);
    }
}
