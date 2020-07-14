<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosSampleData\Model;

/**
 * Class Currency
 * @package Magestore\Webpos\Model\Config\Data
 */
class CurrencyRate extends \Magento\Framework\DataObject implements \Magestore\PosSampleData\Api\Data\CurrencyRateInterface
{
    /**
     * @inheritdoc
     */
    public function getCurrencyCode(){
        return $this->getData(self::CURRENCY_CODE);
    }
    /**
     * @inheritdoc
     */
    public function setCurrencyCode($currencyCode){
        return $this->setData(self::CURRENCY_CODE, $currencyCode);
    }
    /**
     * @inheritdoc
     */
    public function getCurrencyTo(){
        return $this->getData(self::CURRENCY_TO);

    }
    /**
     * @inheritdoc
     */
    public function setCurrencyTo($currencyTo){
        return $this->setData(self::CURRENCY_TO, $currencyTo);
    }
    /**
     * @inheritdoc
     */
    public function getRate(){
        return $this->getData(self::RATE);

    }
    /**
     * @inheritdoc
     */
    public function setRate($rate){
        return $this->setData(self::RATE, $rate);
    }
}