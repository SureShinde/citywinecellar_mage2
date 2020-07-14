<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Staff\Login;

/**
 * @api
 */
/**
 * Interface LocationResultInterface
 * @package Magestore\Webpos\Api\Data\Staff\Login
 */
interface LocationResultInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const LOCATION_ID = 'location_id';
    const LOCATION_NAME = 'location_name';
    const LOCATION_CODE = 'warehouse_code';
    const WEBSITE_ID = 'website_id';
    const STORE_ID = 'store_id';
    const STORE_CODE = 'store_code';
    const TELEPHONE = 'telephone';
    const ADDRESS = 'address';
    const POS = 'pos';
    const RECEIPT_HEADER = 'receipt_header';
    const RECEIPT_FOOTER = 'receipt_footer';
    const STORE_NAME = 'store_name';

    /**
     * Get location id
     *
     * @api
     * @return int
     */
    public function getLocationId();

    /**
     * Set location id
     *
     * @api
     * @param int $locationId
     * @return LocationResultInterface
     */
    public function setLocationId($locationId);

    /**
     * Get location name
     *
     * @api
     * @return string
     */
    public function getName();

    /**
     * Set location name
     *
     * @api
     * @param string $locationName
     * @return LocationResultInterface
     */
    public function setName($locationName);

    /**
     * Get location code
     *
     * @api
     * @return string
     */
    public function getLocationCode();

    /**
     * Set location code
     *
     * @api
     * @param string $locationCode
     * @return LocationResultInterface
     */
    public function setLocationCode($locationCode);

    /**
     * Get address
     *
     * @return \Magestore\Webpos\Api\Data\Staff\Login\Location\AddressInterface
     */
    public function getAddress();

    /**
     * Set address
     *
     * @param \Magestore\Webpos\Api\Data\Staff\Login\Location\AddressInterface $address
     * @return LocationResultInterface
     */
    public function setAddress($address);

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone();

    /**
     * Set telephone
     *
     * @param string $telephone
     * @return LocationResultInterface
     */
    public function setTelephone($telephone);
    /**
     * Get store id
     *
     * @api
     * @return int
     */
    public function getStoreId();

    /**
     * Set store id
     *
     * @api
     * @param int $storeId
     * @return LocationResultInterface
     */
    public function setStoreId($storeId);
    /**
     * Get store code
     *
     * @api
     * @return string
     */
    public function getStoreCode();

    /**
     * Set store code
     *
     * @api
     * @param string $storeCode
     * @return LocationResultInterface
     */
    public function setStoreCode($storeCode);
    /**
     * Get Website id
     *
     * @api
     * @return int
     */
    public function getWebsiteId();

    /**
     * Set Website id
     *
     * @api
     * @param int $websiteId
     * @return LocationResultInterface
     */
    public function setWebsiteId($websiteId);

    /**
     * Get pos
     *
     * @api
     * @return \Magestore\Webpos\Api\Data\Staff\Login\PosResultInterface[]
     */
    public function getPos();

    /**
     * Set pos
     *
     * @api
     * @param \Magestore\Webpos\Api\Data\Staff\Login\PosResultInterface $pos
     * @return LocationResultInterface
     */
    public function setPos($pos);

    /**
     * Get receipt header
     *
     * @return string
     */
    public function getReceiptHeader();

    /**
     * Set receipt footer
     *
     * @param string $receiptHeader
     * @return LocationResultInterface
     */
    public function setReceiptHeader($receiptHeader);

    /**
     * Get receipt header
     *
     * @return string
     */
    public function getReceiptFooter();

    /**
     * Set receipt footer
     *
     * @param string $receiptFooter
     * @return LocationResultInterface
     */
    public function setReceiptFooter($receiptFooter);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Staff\Login\LocationResultExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Staff\Login\LocationResultExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Staff\Login\LocationResultExtensionInterface $extensionAttributes
    );

    /**
     * Get store name
     *
     * @return string
     */
    public function getStoreName();

    /**
     * Set store name
     *
     * @param string $storeName
     * @return LocationResultInterface
     */
    public function setStoreName($storeName);
}
