<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Country;

/**
 * Country interface.
 */
interface CountryInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ID = 'id';
    const CODE = 'code';
    const NAME = 'name';
    const REGION_REQUIRE = 'region_require';
    const REGIONS = 'regions';
    /**
     * Get ID
     *
     * @api
     * @return string
     */
    public function getId();

    /**
     * Set ID
     *
     * @api
     * @param string $id
     * @return CountryInterface
     */
    public function setId($id);
    /**
     * Get name
     *
     * @api
     * @return string
     */
    public function getName();

    /**
     * Set name
     *
     * @api
     * @param string $name
     * @return CountryInterface
     */
    public function setName($name);
    /**
     * Get region require
     *
     * @api
     * @return int
     */
    public function getRegionRequire();

    /**
     * Set region require
     *
     * @api
     * @param int $regionRequire
     * @return CountryInterface
     */
    public function setRegionRequire($regionRequire);
    /**
     * Get regions
     *
     * @api
     * @return \Magestore\Webpos\Api\Data\Country\RegionInterface[]
     */
    public function getRegions();

    /**
     * Set regions
     *
     * @api
     * @param \Magestore\Webpos\Api\Data\Country\RegionInterface[] $regions
     * @return CountryInterface
     */
    public function setRegions($regions);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Country\CountryExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Country\CountryExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Country\CountryExtensionInterface $extensionAttributes
    );
}
