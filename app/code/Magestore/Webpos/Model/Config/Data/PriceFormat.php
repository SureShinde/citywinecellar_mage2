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
class PriceFormat extends \Magento\Framework\DataObject implements \Magestore\Webpos\Api\Data\Config\PriceFormatInterface
{

    /**
     * Get code
     *
     * @api
     * @return string
     */
    public function getCurrencyCode() {
        return $this->getData(self::CURRENCY_CODE);
    }

    /**
     * Set code
     *
     * @api
     * @param string $currencyCode
     * @return $this
     */
    public function setCurrencyCode($currencyCode) {
        return $this->setData(self::CURRENCY_CODE, $currencyCode);
    }
    /**
     * Get decimal symbol
     *
     * @api
     * @return string
     */
    public function getDecimalSymbol() {
        return $this->getData(self::DECIMAL_SYMBOL);
    }

    /**
     * Set decimal symbol
     *
     * @api
     * @param string $decimalSymbol
     * @return $this
     */
    public function setDecimalSymbol($decimalSymbol) {
        return $this->setData(self::DECIMAL_SYMBOL, $decimalSymbol);
    }
    /**
     * Get group symbol
     *
     * @api
     * @return string
     */
    public function getGroupSymbol(){
        return $this->getData(self::GROUP_SYMBOL);
    }

    /**
     * Set group symbol
     *
     * @api
     * @param string $groupSymbol
     * @return $this
     */
    public function setGroupSymbol($groupSymbol){
        return $this->setData(self::GROUP_SYMBOL, $groupSymbol);
    }
    /**
     * Get group length
     *
     * @api
     * @return int
     */
    public function getGroupLength() {
        return $this->getData(self::GROUP_LENGTH);
    }

    /**
     * Set group length
     *
     * @api
     * @param int $groupLength
     * @return $this
     */
    public function setGroupLength($groupLength) {
        return $this->setData(self::GROUP_LENGTH, $groupLength);
    }
    /**
     * Get integer required
     *
     * @api
     * @return int
     */
    public function getIntegerRequired() {
        return $this->getData(self::INTEGER_REQUIRED);
    }

    /**
     * Set integer required
     *
     * @api
     * @param int $integerRequired
     * @return $this
     */
    public function setIntegerRequired($integerRequired) {
        return $this->setData(self::INTEGER_REQUIRED, $integerRequired);
    }
    /**
     * Get pattern
     *
     * @api
     * @return string
     */
    public function getPattern() {
        return $this->getData(self::PATTERN);
    }

    /**
     * Set pattern
     *
     * @api
     * @param string $pattern
     * @return $this
     */
    public function setPattern($pattern) {
        return $this->setData(self::PATTERN, $pattern);
    }
    /**
     * Get precision
     *
     * @api
     * @return int
     */
    public function getPrecision() {
        return $this->getData(self::PRECISION);
    }

    /**
     * Set precision
     *
     * @api
     * @param int $precision
     * @return $this
     */
    public function setPrecision($precision) {
        return $this->setData(self::PRECISION, $precision);
    }
    /**
     * Get required precision
     *
     * @api
     * @return int
     */
    public function getRequiredPrecision() {
        return $this->getData(self::REQUIRED_PRECISION);
    }

    /**
     * Set required precision
     *
     * @api
     * @param int $requiredPrecision
     * @return $this
     */
    public function setRequiredPrecision($requiredPrecision) {
        return $this->setData(self::REQUIRED_PRECISION, $requiredPrecision);
    }
}
