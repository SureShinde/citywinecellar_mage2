<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Tax;
class TaxDetail extends \Magento\Framework\DataObject implements \Magestore\Webpos\Api\Data\Tax\TaxDetailInterface
{
    /**
     * Get id
     *
     * @return string|null
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }
    /**
     * Set id
     *
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }
    /**
     * Get code
     *
     * @return string|null
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }
    /**
     * Set code
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
    }
    /**
     * Get title
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }
    /**
     * Set title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }
    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->getData(self::AMOUNT);
    }
    /**
     * Set amount
     *
     * @param float $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        return $this->setData(self::AMOUNT, $amount);
    }
    /**
     * Get base real amount
     *
     * @return float
     */
    public function getBaseRealAmount()
    {
        return $this->getData(self::BASE_REAL_AMOUNT);
    }
    /**
     * Set base real amount
     *
     * @param float $baseAmount
     * @return $this
     */
    public function setBaseRealAmount($baseRealAmount)
    {
        return $this->setData(self::BASE_REAL_AMOUNT, $baseRealAmount);
    }
    /**
     * Get base amount
     *
     * @return float
     */
    public function getBaseAmount()
    {
        return $this->getData(self::BASE_AMOUNT);
    }
    /**
     * Set base amount
     *
     * @param float $baseAmount
     * @return $this
     */
    public function setBaseAmount($baseAmount)
    {
        return $this->setData(self::BASE_AMOUNT, $baseAmount);
    }
    /**
     * Get percent
     *
     * @return float
     */
    public function getPercent()
    {
        return $this->getData(self::PERCENT);
    }
    /**
     * Set percent
     *
     * @param float $percent
     * @return $this
     */
    public function setPercent($percent)
    {
        return $this->setData(self::PERCENT, $percent);
    }
    /**
     * Get process
     *
     * @return int
     */
    public function getProcess()
    {
        return $this->getData(self::PROCESS);
    }
    /**
     * Set process
     *
     * @param int $process
     * @return $this
     */
    public function setProcess($process)
    {
        return $this->setData(self::PROCESS, $process);
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
        \Magestore\Webpos\Api\Data\Tax\TaxDetailExtensionInterface $extensionAttributes
    ){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}