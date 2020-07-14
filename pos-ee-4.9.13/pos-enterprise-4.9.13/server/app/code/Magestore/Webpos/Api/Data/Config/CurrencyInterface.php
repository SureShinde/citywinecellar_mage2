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
interface CurrencyInterface
{
    const CODE = 'code';
    const CURRENCY_NAME = 'currency_name';
    const CURRENCY_RATE = 'currency_rate';
    const CURRENCY_SYMBOL = 'currency_symbol';
    const IS_DEFAULT = 'is_default';

    /**
     * Get code
     *
     * @api
     * @return string
     */
    public function getCode();

    /**
     * Set code
     *
     * @api
     * @param string $code
     * @return CurrencyInterface
     */
    public function setCode($code);
    /**
     * Get currency name
     *
     * @api
     * @return string
     */
    public function getCurrencyName();

    /**
     * Set currency name
     *
     * @api
     * @param string $currencyName
     * @return CurrencyInterface
     */
    public function setCurrencyName($currencyName);
    /**
     * Get currency rate
     *
     * @api
     * @return float
     */
    public function getCurrencyRate();

    /**
     * Set currency rate
     *
     * @api
     * @param float $currencyRate
     * @return CurrencyInterface
     */
    public function setCurrencyRate($currencyRate);
    /**
     * Get currency symbol
     *
     * @api
     * @return string
     */
    public function getCurrencySymbol();

    /**
     * Set currency symbol
     *
     * @api
     * @param string $currencySymbol
     * @return CurrencyInterface
     */
    public function setCurrencySymbol($currencySymbol);
    /**
     * Get is default
     *
     * @api
     * @return int
     */
    public function getIsDefault();

    /**
     * Set is default
     *
     * @api
     * @param int $isDefault
     * @return CurrencyInterface
     */
    public function setIsDefault($isDefault);
}
