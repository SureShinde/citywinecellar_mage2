<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Inventory;

/**
 * @api
 */
interface ExternalStockInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const QTY = 'qty';
    const NAME = 'name';
    const ADDRESS = 'address';

    /**
     * @return float
     */
    public function getQty();

    /**
     * @param float $Qty
     * @return ExternalStockInterface
     */
    public function setQty($Qty);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return ExternalStockInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getAddress();

    /**
     * @param string $address
     * @return ExternalStockInterface
     */
    public function setAddress($address);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Inventory\ExternalStockExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Inventory\ExternalStockExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Inventory\ExternalStockExtensionInterface $extensionAttributes
    );
}