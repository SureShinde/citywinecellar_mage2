<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Config;

/**
 * @api
 */
/**
 * Interface ConfigInterface
 * @package Magestore\Webpos\Api\Data\Config
 */
interface PriceFormatInterface
{
    const CURRENCY_CODE = 'currency_code';
    const DECIMAL_SYMBOL = 'decimal_symbol';
    const GROUP_LENGTH = 'group_length';
    const GROUP_SYMBOL = 'group_symbol';
    const INTEGER_REQUIRED = 'integer_required';
    const PATTERN = 'pattern';
    const PRECISION = 'precision';
    const REQUIRED_PRECISION = 'required_precision';

    /**
     * Get code
     *
     * @api
     * @return string
     */
    public function getCurrencyCode();

    /**
     * Set code
     *
     * @api
     * @param string $currencyCode
     * @return PriceFormatInterface
     */
    public function setCurrencyCode($currencyCode);
    /**
     * Get decimal symbol
     *
     * @api
     * @return string
     */
    public function getDecimalSymbol();

    /**
     * Set decimal symbol
     *
     * @api
     * @param string $decimalSymbol
     * @return PriceFormatInterface
     */
    public function setDecimalSymbol($decimalSymbol);
    /**
     * Get group symbol
     *
     * @api
     * @return string
     */
    public function getGroupSymbol();

    /**
     * Set group symbol
     *
     * @api
     * @param string $groupSymbol
     * @return PriceFormatInterface
     */
    public function setGroupSymbol($groupSymbol);
    /**
     * Get group length
     *
     * @api
     * @return int
     */
    public function getGroupLength();

    /**
     * Set group length
     *
     * @api
     * @param int $groupLength
     * @return PriceFormatInterface
     */
    public function setGroupLength($groupLength);
    /**
     * Get integer required
     *
     * @api
     * @return int
     */
    public function getIntegerRequired();

    /**
     * Set integer required
     *
     * @api
     * @param int $integerRequired
     * @return PriceFormatInterface
     */
    public function setIntegerRequired($integerRequired);
    /**
     * Get pattern
     *
     * @api
     * @return string
     */
    public function getPattern();

    /**
     * Set pattern
     *
     * @api
     * @param string $pattern
     * @return PriceFormatInterface
     */
    public function setPattern($pattern);
    /**
     * Get precision
     *
     * @api
     * @return int
     */
    public function getPrecision();

    /**
     * Set precision
     *
     * @api
     * @param int $precision
     * @return PriceFormatInterface
     */
    public function setPrecision($precision);
    /**
     * Get required precision
     *
     * @api
     * @return int
     */
    public function getRequiredPrecision();

    /**
     * Set required precision
     *
     * @api
     * @param int $requiredPrecision
     * @return PriceFormatInterface
     */
    public function setRequiredPrecision($requiredPrecision);
}
