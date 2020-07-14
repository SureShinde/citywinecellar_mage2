<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\InventorySales;

/**
 * Interface SalesChannelInterface
 * @package Magestore\Webpos\Api\Data\InventorySales
 */
interface SalesChannelInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const TYPE = 'type';
    const CODE = 'code';
    const STOCK_ID = 'stock_id';

    /**
     * Get type
     *
     * @return string
     */
    public function getType();

    /**
     * Set type
     *
     * @param string $type
     * @return SalesChannelInterface
     */
    public function setType($type);
    /**
     * Get code
     *
     * @return string
     */
    public function getCode();

    /**
     * Set code
     *
     * @param string $code
     * @return SalesChannelInterface
     */
    public function setCode($code);
    /**
     * Get stock id
     *
     * @return int
     */
    public function getStockId();

    /**
     * Set stock id
     *
     * @param int $stockId
     * @return SalesChannelInterface
     */
    public function setStockId($stockId);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\InventorySales\SalesChannelExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\InventorySales\SalesChannelExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\InventorySales\SalesChannelExtensionInterface $extensionAttributes
    );
}
