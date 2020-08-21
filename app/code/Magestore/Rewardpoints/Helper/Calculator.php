<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
namespace Magestore\Rewardpoints\Helper;

/**
 * RewardPoints Calculator Helper
 */
class Calculator extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Config
     */
    protected $helper;

    /**
     * @var \Magento\Tax\Model\ConfigFactory
     */
    protected $_taxConfigFactory;

    /**
     * @var \Magento\Tax\Model\CalculationFactory
     */
    protected $_taxCalculationFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    const XML_PATH_ROUNDING_METHOD = 'rewardpoints/earning/rounding_method';

    /**
     * Calculator constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param Config $globalConfig
     * @param \Magento\Tax\Model\ConfigFactory $taxConfigFactory
     * @param \Magento\Tax\Model\CalculationFactory $taxCalculationFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magestore\Rewardpoints\Helper\Config $globalConfig,
        \Magento\Tax\Model\ConfigFactory $taxConfigFactory,
        \Magento\Tax\Model\CalculationFactory $taxCalculationFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->helper = $globalConfig;
        $this->_taxConfigFactory = $taxConfigFactory;
        $this->_taxCalculationFactory = $taxCalculationFactory;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Rounding number by reward points configuration
     *
     * @param mixed $number
     * @param mixed $store
     * @return int
     */
    public function round($number, $store = null)
    {
        switch ($this->helper->getConfig(self::XML_PATH_ROUNDING_METHOD, $store)) {
            case 'floor':
                return floor($number);
            case 'ceil':
                return ceil($number);
        }
        return round($number);
    }

    /**
     * Calculate price including tax or excluding tax
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param float $price
     * @param null|boolean $includingTax
     * @param bool $item
     * @return float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getPrice($product, $price, $includingTax = null, $item = false)
    {
        if (!$price) {
            return $price;
        }
        $store = $this->_storeManager->getStore();

        if ($item) {
            $priceIncludingTax = false;
        } else {
            $priceIncludingTax = $this->_taxConfigFactory->create()->priceIncludesTax($store);
        }

        if (($priceIncludingTax && $includingTax) || (!$priceIncludingTax && !$includingTax)) {
            return $price;
        }

        $percent = $product->getTaxPercent();
        $includingPercent = null;

        $taxClassId = $product->getTaxClassId();
        if ($percent === null) {
            if ($taxClassId) {
                $request = $this->_taxCalculationFactory->create()
                    ->getRateRequest(null, null, null, $store);
                $percent = $this->_taxCalculationFactory->create()
                    ->getRate($request->setProductClassId($taxClassId));
            }
        }
        if ($taxClassId && $priceIncludingTax) {
            $request = $this->_taxCalculationFactory->create()->getRateRequest(false, false, false, $store);
            $includingPercent = $this->_taxCalculationFactory->create()
                ->getRate($request->setProductClassId($taxClassId));
        }
        if ($percent === false || $percent === null || $percent == 0) {
            if ($priceIncludingTax && !$includingPercent) {
                return $price;
            }
        }
        $product->setTaxPercent($percent);
        if ($includingTax && !$priceIncludingTax) {
            $price = $this->_calculatePrice($price, $percent, true);
        } else {
            if ($includingPercent != $percent) {
                $price = $this->_calculatePrice($price, $includingPercent, false);
                if ($percent != 0) {
                    $price = $this->_taxCalculationFactory->create()->round($price);
                    $price = $this->_calculatePrice($price, $percent, true);
                }
            } else {
                $price = $this->_calculatePrice($price, $percent, false);
            }
        }
        return $store->roundPrice($price);
    }

    /**
     * Calculate Price
     *
     * @param float $price
     * @param float $percent
     * @param boolean $type
     * @return mixed
     */
    public function _calculatePrice($price, $percent, $type)
    {
        $calculator = $this->_taxCalculationFactory->create();
        if ($type) {
            $taxAmount = $calculator->calcTaxAmount($price, $percent, false, false);
            return $price + $taxAmount;
        } else {
            $taxAmount = $calculator->calcTaxAmount($price, $percent, true, false);
            return $price - $taxAmount;
        }
    }
}
