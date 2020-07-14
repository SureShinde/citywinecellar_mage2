<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Country;
/**
 * Class Config
 * @package Magestore\Webpos\Model\Country
 */
class Country extends \Magento\Framework\DataObject implements \Magestore\Webpos\Api\Data\Country\CountryInterface
{
    /**
     * Get ID
     *
     * @api
     * @return int
     */
    public function getId(){
        return $this->getData(self::ID);
    }

    /**
     * Set ID
     *
     * @api
     * @param int $id
     * @return $this
     */
    public function setId($id){
        return $this->setData(self::ID, $id);
    }
    /**
     * Get name
     *
     * @api
     * @return string
     */
    public function getName(){
        return $this->getData(self::NAME);
    }

    /**
     * Set name
     *
     * @api
     * @param string $name
     * @return $this
     */
    public function setName($name){
        return $this->setData(self::NAME, $name);
    }
    /**
     * Get region require
     *
     * @api
     * @return int
     */
    public function getRegionRequire(){
        return $this->getData(self::REGION_REQUIRE);
    }

    /**
     * Set region require
     *
     * @api
     * @param int $regionRequire
     * @return $this
     */
    public function setRegionRequire($regionRequire){
        return $this->setData(self::REGION_REQUIRE, $regionRequire);
    }
    /**
     * Get regions
     *
     * @api
     * @return \Magestore\Webpos\Api\Data\Country\RegionInterface[]
     */
    public function getRegions(){
        return $this->getData(self::REGIONS);
    }

    /**
     * Set regions
     *
     * @api
     * @param \Magestore\Webpos\Api\Data\Country\RegionInterface[] $regions
     * @return $this
     */
    public function setRegions($regions){
        return $this->setData(self::REGIONS, $regions);
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
        \Magestore\Webpos\Api\Data\Country\CountryExtensionInterface $extensionAttributes
    ){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}