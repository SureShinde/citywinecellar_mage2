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
class Region extends \Magento\Framework\DataObject implements \Magestore\Webpos\Api\Data\Country\RegionInterface
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
     * Get code
     *
     * @api
     * @return string
     */
    public function getCode(){
        return $this->getData(self::CODE);
    }

    /**
     * Set code
     *
     * @api
     * @param string $code
     * @return $this
     */
    public function setCode($code){
        return $this->setData(self::CODE, $code);
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
        \Magestore\Webpos\Api\Data\Country\RegionExtensionInterface $extensionAttributes
    ){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}