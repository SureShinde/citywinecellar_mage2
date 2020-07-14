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
class Currency extends \Magento\Framework\DataObject implements \Magestore\Webpos\Api\Data\Config\CurrencyInterface
{
    /**
     * Get code
     *
     * @api
     * @return string
     */
    public function getCode() {
        return $this->getData(self::CODE);
    }

    /**
     * Set code
     *
     * @api
     * @param string $code
     * @return $this
     */
    public function setCode($code) {
        return $this->setData(self::CODE, $code);
    }
    /**
     * Get currency name
     *
     * @api
     * @return string
     */
    public function getCurrencyName() {
        return $this->getData(self::CURRENCY_NAME);
    }

    /**
     * Set currency name
     *
     * @api
     * @param string $currencyName
     * @return $this
     */
    public function setCurrencyName($currencyName) {
        return $this->setData(self::CURRENCY_NAME, $currencyName);
    }
    /**
     * Get currency rate
     *
     * @api
     * @return float
     */
    public function getCurrencyRate() {
        return $this->getData(self::CURRENCY_RATE);
    }

    /**
     * Set currency rate
     *
     * @api
     * @param float $currencyRate
     * @return $this
     */
    public function setCurrencyRate($currencyRate) {
        return $this->setData(self::CURRENCY_RATE, $currencyRate);
    }
    /**
     * Get currency symbol
     *
     * @api
     * @return string
     */
    public function getCurrencySymbol() {
        return $this->getData(self::CURRENCY_SYMBOL);
    }

    /**
     * Set currency symbol
     *
     * @api
     * @param string $currencySymbol
     * @return $this
     */
    public function setCurrencySymbol($currencySymbol) {
        return $this->setData(self::CURRENCY_SYMBOL, $currencySymbol);
    }
    /**
     * Get is default
     *
     * @api
     * @return int
     */
    public function getIsDefault() {
        return $this->getData(self::IS_DEFAULT);
    }

    /**
     * Set is default
     *
     * @api
     * @param int $isDefault
     * @return $this
     */
    public function setIsDefault($isDefault) {
        return $this->setData(self::IS_DEFAULT, $isDefault);
    }
}