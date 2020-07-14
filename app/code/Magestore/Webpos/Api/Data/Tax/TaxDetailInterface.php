<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Tax;
/**
 * Tax class interface.
 * @api
 * @since 100.0.2
 */
interface TaxDetailInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ID = 'id';
    const AMOUNT = 'amount';
    const BASE_AMOUNT = 'base_amount';
    const PERCENT = 'percent';
    const PROCESS = 'process';
    const RATES = 'rates';
    const CODE = 'code';
    const TITLE = 'title';
    const BASE_REAL_AMOUNT = 'base_real_amount';
    /**
     * Get id
     *
     * @return string|null
     */
    public function getId();
    /**
     * Set id
     *
     * @param string $id
     * @return $this
     */
    public function setId($id);
    /**
     * Get code
     *
     * @return string|null
     */
    public function getCode();
    /**
     * Set code
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code);
    /**
     * Get title
     *
     * @return string|null
     */
    public function getTitle();
    /**
     * Set title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title);
    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount();
    /**
     * Set amount
     *
     * @param float $amount
     * @return $this
     */
    public function setAmount($amount);
    /**
     * Get base amount
     *
     * @return float
     */
    public function getBaseAmount();
    /**
     * Set base amount
     *
     * @param float $baseAmount
     * @return $this
     */
    public function setBaseAmount($baseAmount);
    /**
     * Get base real amount
     *
     * @return float
     */
    public function getBaseRealAmount();
    /**
     * Set base real amount
     *
     * @param float $baseAmount
     * @return $this
     */
    public function setBaseRealAmount($baseRealAmount);
    /**
     * Get percent
     *
     * @return float
     */
    public function getPercent();
    /**
     * Set percent
     *
     * @param float $percent
     * @return $this
     */
    public function setPercent($percent);
    /**
     * Get process
     *
     * @return int
     */
    public function getProcess();
    /**
     * Set process
     *
     * @param int $process
     * @return $this
     */
    public function setProcess($process);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Tax\TaxDetailExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Tax\TaxDetailExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Tax\TaxDetailExtensionInterface $extensionAttributes
    );
}