<?php

namespace Magestore\PosSampleData\Api\Data;

interface CurrencyRateInterface {
    const CURRENCY_CODE = 'currency_code';
    const CURRENCY_TO = 'currency_to';
    const RATE = 'rate';


    /**
     * Get currency code
     *
     * @return string|null
     */
    public function getCurrencyCode();
    /**
     * Set currency code
     *
     * @param string|null $currencyCode
     * @return $this
     */
    public function setCurrencyCode($currencyCode);
    /**
     * Get currency to
     *
     * @return string|null
     */
    public function getCurrencyTo();
    /**
     * Set currency to
     *
     * @param string|null $currencyTo
     * @return $this
     */
    public function setCurrencyTo($currencyTo);
    /**
     * Get rate
     *
     * @return float
     */
    public function getRate();
    /**
     * Set rate
     *
     * @param float $rate
     * @return $this
     */
    public function setRate($rate);
}