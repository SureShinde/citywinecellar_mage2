<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Staff\Data\Login;

use Magestore\Webpos\Api\Data\Staff\Login\LocationResultInterface;

/**
 * Class \Magestore\Webpos\Model\Staff\Data\Login\LocationResult
 */
class LocationResult extends \Magento\Framework\DataObject implements LocationResultInterface
{
    /**
     * @var \Magento\Directory\Model\Country
     */
    protected $country;

    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $regionFactory;

    /**
     * LocationResult constructor.
     *
     * @param \Magento\Directory\Model\Country $country
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Directory\Model\Country $country,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        array $data = []
    ) {
        parent::__construct($data);
        $this->country = $country;
        $this->regionFactory = $regionFactory;
    }

    /**
     * Get location id
     *
     * @api
     * @return int
     */
    public function getLocationId()
    {
        return $this->getData(self::LOCATION_ID);
    }

    /**
     * Set location id
     *
     * @api
     * @param int $locationId
     * @return $this
     */
    public function setLocationId($locationId)
    {
        return $this->setData(self::LOCATION_ID, $locationId);
    }
    /**
     * Get location code
     *
     * @api
     * @return string
     */
    public function getLocationCode()
    {
        return $this->getData(self::LOCATION_CODE);
    }

    /**
     * Set location code
     *
     * @api
     * @param string $locationCode
     * @return $this
     */
    public function setLocationCode($locationCode)
    {
        return $this->setData(self::LOCATION_CODE, $locationCode);
    }
    /**
     * Get location name
     *
     * @api
     * @return string
     */
    public function getName()
    {
        return $this->getData(self::LOCATION_NAME);
    }

    /**
     * Set location name
     *
     * @api
     * @param string $locationName
     * @return $this
     */
    public function setName($locationName)
    {
        return $this->setData(self::LOCATION_NAME, $locationName);
    }
    /**
     * @inheritdoc
     */
    public function getAddress()
    {
        return $this->getData(self::ADDRESS);
    }

    /**
     * @inheritdoc
     */
    public function setAddress($address)
    {
        return $this->setData(self::ADDRESS, $address);
    }
    /**
     * @inheritdoc
     */
    public function getTelephone()
    {
        return $this->getData(self::TELEPHONE);
    }

    /**
     * @inheritdoc
     */
    public function setTelephone($telephone)
    {
        return $this->setData(self::TELEPHONE, $telephone);
    }

    /**
     * Get store id
     *
     * @api
     * @return int
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * Set store id
     *
     * @api
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function getStoreCode()
    {
        return $this->getData(self::STORE_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setStoreCode($storeCode)
    {
        return $this->setData(self::STORE_CODE, $storeCode);
    }
    /**
     * @inheritDoc
     */
    public function getWebsiteId()
    {
        return $this->getData(self::WEBSITE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setWebsiteId($websiteId)
    {
        return $this->setData(self::WEBSITE_ID, $websiteId);
    }

    /**
     * Get pos
     *
     * @api
     * @return \Magestore\Webpos\Api\Data\Staff\Login\PosResultInterface[]
     */
    public function getPos()
    {
        return $this->getData(self::POS);
    }

    /**
     * Set pos
     *
     * @api
     * @param \Magestore\Webpos\Api\Data\Staff\Login\PosResultInterface $pos
     * @return $this
     */
    public function setPos($pos)
    {
        return $this->setData(self::POS, $pos);
    }

    /**
     * Get receipt header
     *
     * @return string
     */
    public function getReceiptHeader()
    {
        return $this->getData(self::RECEIPT_HEADER);
    }

    /**
     * Set receipt footer
     *
     * @param string $receiptHeader
     * @return LocationResultInterface
     */
    public function setReceiptHeader($receiptHeader)
    {
        return $this->setData(self::RECEIPT_HEADER, $receiptHeader);
    }

    /**
     * Get receipt header
     *
     * @return string
     */
    public function getReceiptFooter()
    {
        return $this->getData(self::RECEIPT_FOOTER);
    }

    /**
     * Set receipt footer
     *
     * @param string $receiptFooter
     * @return LocationResultInterface
     */
    public function setReceiptFooter($receiptFooter)
    {
        return $this->setData(self::RECEIPT_FOOTER, $receiptFooter);
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
        \Magestore\Webpos\Api\Data\Staff\Login\LocationResultExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }

    /**
     * Get store name
     *
     * @return string
     */
    public function getStoreName()
    {
        return $this->getData(self::STORE_NAME);
    }

    /**
     * Set store name
     *
     * @param string $storeName
     * @return LocationResultInterface
     */
    public function setStoreName($storeName)
    {
        return $this->setData(self::STORE_NAME, $storeName);
    }
}
