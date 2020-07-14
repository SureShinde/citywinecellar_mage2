<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Config\Data;
/**
 * Class Currency
 * @package Magestore\Webpos\Model\Config\Data
 */
class CustomerGroup extends \Magento\Framework\DataObject implements \Magestore\Webpos\Api\Data\Config\CustomerGroupInterface
{
    /**
     * @inheritdoc
     */
    public function getId(){
        return $this->getData(self::ID);
    }

    /**
     * @inheritdoc
     */
    public function setId($id){
        return $this->setData(self::ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getCode(){
        return $this->getData(self::CODE);
    }

    /**
     * @inheritdoc
     */
    public function setCode($code){
        return $this->setData(self::CODE, $code);
    }

    /**
     * @inheritdoc
     */
    public function getTaxClassId(){
        return $this->getData(self::TAX_CLASS_ID);
    }

    /**
     * @inheritdoc
     */
    public function setTaxClassId($taxClassId){
        return $this->setData(self::TAX_CLASS_ID, $taxClassId);
    }
}