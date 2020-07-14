<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\InventorySales;

use Magestore\Webpos\Api\Data\InventorySales\SalesChannelInterface;

/**
 * Class Currency
 * @package Magestore\Webpos\Model\Config\Data
 */
class SalesChannel extends \Magento\Framework\DataObject implements SalesChannelInterface
{
    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * Set type
     *
     * @param string $type
     * @return SalesChannelInterface
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * Set code
     *
     * @param string $code
     * @return SalesChannelInterface
     */
    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * Get stock id
     *
     * @return int
     */
    public function getStockId()
    {
        return $this->getData(self::STOCK_ID);
    }

    /**
     * Set stock id
     *
     * @param int $stockId
     * @return SalesChannelInterface
     */
    public function setStockId($stockId)
    {
        return $this->setData(self::STOCK_ID, $stockId);
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
        \Magestore\Webpos\Api\Data\InventorySales\SalesChannelExtensionInterface $extensionAttributes
    ){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}