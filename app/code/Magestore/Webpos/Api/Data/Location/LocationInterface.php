<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Location;

interface LocationInterface extends \Magento\Framework\Api\ExtensibleDataInterface {
    const LOCATION_ID = 'location_id';
    const LOCATION_CODE = 'warehouse_code';
    const NAME = 'name';
    const DESCRIPTION = 'description';
    const TELEPHONE = 'telephone';
    const EMAIL = 'email';
    const STREET = 'street';
    const CITY = 'city';
    const REGION = 'region';
    const REGION_ID = 'region_id';
    const COUNTRY = 'country';
    const COUNTRY_ID = 'country_id';
    const POSTCODE = 'postcode';

    const WAREHOUSE_ID = 'warehouse_id';
    const WAREHOUSE_NAME = 'warehouse_name';

    const STOCK_ID = 'stock_id';

    const STORE_ID = 'store_id';
    const STORE_CODE = 'store_code';
    const WEBSITE_ID = 'website_id';

    /**
     * Get Location Id
     *
     * @return int|null
     */
    public function getLocationId();	
    /**
     * Set Location Id
     *
     * @param int $locationId
     * @return LocationInterface
     */
    public function setLocationId($locationId);

    /**
     * Get Name
     *
     * @return string|null
     */
    public function getName();	
    /**
     * Set Name
     *
     * @param string $name
     * @return LocationInterface
     */
    public function setName($name);
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
     * @return LocationInterface
     */
    public function setLocationCode($locationCode);

    /**
     * Get Description
     *
     * @return string|null
     */
    public function getDescription();	
    /**
     * Set Description
     *
     * @param string $description
     * @return LocationInterface
     */
    public function setDescription($description);

    /**
     * Get Telephone
     *
     * @return string
     */
    public function getTelephone();	
    /**
     * Set Telephone
     *
     * @param string $telephone
     * @return LocationInterface
     */
    public function setTelephone($telephone);

    /**
     * Get Email
     *
     * @return string|null
     */
    public function getEmail();	
    /**
     * Set Email
     *
     * @param string $email
     * @return LocationInterface
     */
    public function setEmail($email);

    /**
     * Get Street
     *
     * @return string|null
     */
    public function getStreet();	
    /**
     * Set Street
     *
     * @param string $street
     * @return LocationInterface
     */
    public function setStreet($street);

    /**
     * Get City
     *
     * @return string|null
     */
    public function getCity();	
    /**
     * Set City
     *
     * @param string $city
     * @return LocationInterface
     */
    public function setCity($city);

    /**
     * Get Region
     *
     * @return string|null
     */
    public function getRegion();	
    /**
     * Set Region
     *
     * @param string $region
     * @return LocationInterface
     */
    public function setRegion($region);

    /**
     * Get Region Id
     *
     * @return int|null
     */
    public function getRegionId();	
    /**
     * Set Region Id
     *
     * @param int $regionId
     * @return LocationInterface
     */
    public function setRegionId($regionId);

    /**
     * Get country
     *
     * @return string|null
     */
    public function getCountry();
    /**
     * Set country
     *
     * @param string $country
     * @return LocationInterface
     */
    public function setCountry($country);

    /**
     * Get Country Id
     *
     * @return string|null
     */
    public function getCountryId();	
    /**
     * Set Country Id
     *
     * @param string $countryId
     * @return LocationInterface
     */
    public function setCountryId($countryId);

    /**
     * Get Postcode
     *
     * @return string|null
     */
    public function getPostcode();	
    /**
     * Set Postcode
     *
     * @param string $postcode
     * @return LocationInterface
     */
    public function setPostcode($postcode);

    /**
     * @return \Magestore\Webpos\Api\Data\Staff\Login\Location\AddressInterface
     */
    public function getAddress();

    /**
     * @param \Magestore\Webpos\Api\Data\Staff\Login\Location\AddressInterface $address
     * @return LocationInterface
     */
    public function setAddress($address);

    /**
     * Get Warehouse Id
     *
     * @return int|null
     */
    public function getWarehouseId();
    /**
     * Set Warehouse Id
     *
     * @param int $warehouseId
     * @return LocationInterface
     */
    public function setWarehouseId($warehouseId);

    /**
     * Get Stock Id
     *
     * @return int|null
     */
    public function getStockId();
    /**
     * Set Stock Id
     *
     * @param int|null $stockId
     * @return LocationInterface
     */
    public function setStockId($stockId);
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
     * @return LocationInterface
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
     * @return LocationInterface
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
     * @return LocationInterface
     */
    public function setWebsiteId($websiteId);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Location\LocationExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Location\LocationExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Location\LocationExtensionInterface $extensionAttributes
    );
}