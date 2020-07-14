<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Staff\Login\Location;

/**
 * @api
 */
/**
 * Interface LoginResultInterface
 * @package Magestore\Webpos\Api\Data\Staff\Login
 */
interface AddressInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const STREET = 'street';
    const CITY = 'city';
    const REGION = 'region';
    const REGION_ID = 'region_id';
    const COUNTRY_ID = 'country_id';
    const COUNTRY = 'country';
    const POSTCODE = 'postcode';

    /**
     * Get Street
     *
     * @return string|null
     */
    public function getStreet();	
    /**
     * Set Street
     *
     * @param string|null $street
     * @return AddressInterface
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
     * @param string|null $city
     * @return AddressInterface
     */
    public function setCity($city);

    /**
     * Get Region
     *
     * @return \Magento\Customer\Api\Data\RegionInterface
     */
    public function getRegion();	
    /**
     * Set Region
     *
     * @param \Magento\Customer\Api\Data\RegionInterface $region
     * @return AddressInterface
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
     * @param int|null $regionId
     * @return AddressInterface
     */
    public function setRegionId($regionId);

    /**
     * Get Country
     *
     * @return string|null
     */
    public function getCountryId();
    /**
     * Set Country
     *
     * @param string|null $country
     * @return AddressInterface
     */
    public function setCountryId($country);

    /**
     * Get Country
     *
     * @return string|null
     */
    public function getCountry();
    /**
     * Set Country
     *
     * @param string|null $country
     * @return AddressInterface
     */
    public function setCountry($country);

    /**
     * Get Postcode
     *
     * @return string|null
     */
    public function getPostcode();	
    /**
     * Set Postcode
     *
     * @param string|null $postcode
     * @return AddressInterface
     */
    public function setPostcode($postcode);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Staff\Login\Location\AddressExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Staff\Login\Location\AddressExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Staff\Login\Location\AddressExtensionInterface $extensionAttributes
    );
}