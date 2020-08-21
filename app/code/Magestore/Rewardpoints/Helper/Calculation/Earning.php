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

namespace Magestore\Rewardpoints\Helper\Calculation;

/**
 * RewardPoints Earning Calculation Helper
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Earning extends \Magestore\Rewardpoints\Helper\Calculation\AbstractCalculation
{

    const XML_PATH_EARNING_EXPIRE = 'rewardpoints/earning/expire';
    const XML_PATH_EARNING_ORDER_INVOICE = 'rewardpoints/earning/order_invoice';
    const XML_PATH_HOLDING_DAYS = 'rewardpoints/earning/holding_days';
    const XML_PATH_ORDER_CANCEL_STATUS = 'rewardpoints/earning/order_cancel_state';
    const XML_PATH_EARNING_BY_SHIPPING = 'rewardpoints/earning/by_shipping';
    const XML_PATH_EARNING_BY_TAX = 'rewardpoints/earning/by_tax';

    /**
     * @var \Magestore\Rewardpoints\Model\RateFactory
     */
    protected $_rateFactory;

    /**
     * @var \Magestore\Rewardpoints\Helper\Calculator
     */
    protected $_helperCalculator;

    /**
     * @var \Magestore\Rewardpoints\Helper\Calculator
     */
    protected $_helperConfig;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * @var \Magento\Backend\Model\Session\QuoteFactory
     */
    protected $_adminQuoteSessionFactory;

    /**
     * Earning constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Checkout\Model\SessionFactory $sessionFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magestore\Rewardpoints\Model\RateFactory $rateFactory
     * @param \Magestore\Rewardpoints\Helper\Calculator $helperCalculator
     * @param \Magestore\Rewardpoints\Helper\Config $helperConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Customer\Model\SessionFactory $customerSessionFactory
     * @param \Magento\Backend\Model\Session\QuoteFactory $adminQuoteSessionFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Checkout\Model\SessionFactory $sessionFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\Rewardpoints\Model\RateFactory $rateFactory,
        \Magestore\Rewardpoints\Helper\Calculator $helperCalculator,
        \Magestore\Rewardpoints\Helper\Config $helperConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\State $appState,
        \Magento\Customer\Model\SessionFactory $customerSessionFactory,
        \Magento\Backend\Model\Session\QuoteFactory $adminQuoteSessionFactory
    ) {
        $this->_rateFactory = $rateFactory;
        $this->_appState = $appState;
        $this->_helperCalculator = $helperCalculator;
        $this->_helperConfig = $helperConfig;
        $this->_adminQuoteSessionFactory = $adminQuoteSessionFactory;
        parent::__construct($context, $storeManager, $customerSessionFactory, $sessionFactory, $objectManager);
    }

    /**
     * Get Total Point that customer can earn by purchase current order/ quote
     *
     * @param null|\Magento\Quote\Model\Quote $quote
     * @return int
     */
    public function getTotalPointsEarning($quote = null)
    {
        if ($quote === null) {
            $quote = $this->getQuote();
        }
        if ($quote->isVirtual()) {
            $address = $quote->getBillingAddress();
        } else {
            $address = $quote->getShippingAddress();
        }
//        if(!$address->getRewardpointsEarn()){
//            $quote->collectTotals();
//        }

        return $address->getRewardpointsEarn();
    }

    /**
     * Get Total Point earning by discount
     *
     * @param null|\Magento\Quote\Model\Quote $quote
     * @return int
     */
    public function getEarningPointByCoupon($quote = null)
    {
        $needConvert = $this->_helperConfig->getGeneralConfig('convert_point');
        if (!$needConvert) {
            return 0;
        }

        if ($quote === null) {
            $quote = $this->getQuote();
        }
        if ($quote->isVirtual()) {
            $address = $quote->getBillingAddress();
        } else {
            $address = $quote->getShippingAddress();
        }
        return $address->getRewardpointsPointsByDiscount();
    }

    /**
     * Get Total Point earning by using coupon code
     *
     * @param null|\Magento\Quote\Model\Quote $quote
     * @return int
     */
    public function getCouponEarnPoints($quote = null)
    {
        $needConvert =  $this->_helperConfig->getGeneralConfig('convert_point');
        if (!$needConvert) {
            return 0;
        }

        if ($quote === null) {
            $quote = $this->getQuote();
        }
        if ($quote->isVirtual()) {
            $address = $quote->getBillingAddress();
        } else {
            $address = $quote->getShippingAddress();
        }
        return $address->getCouponCode();
    }

    /**
     * Calculate quote earning points by system rate
     *
     * @param float $baseGrandTotal
     * @param mixed $store
     * @return int
     */
    public function getRateEarningPoints($baseGrandTotal, $store = null)
    {
        $customerGroupId = $this->getCustomerGroupId();

        $websiteId = $this->getWebsiteId();

        $rate = $this->_rateFactory->create()->getRate(
            \Magestore\Rewardpoints\Model\Rate::MONEY_TO_POINT,
            $customerGroupId,
            $websiteId
        );

        if ($rate && $rate->getId()) {
            /**
             * end update
             */
            if ($baseGrandTotal < 0) {
                $baseGrandTotal = 0;
            }
            $points = $this->_helperCalculator->round(
                $baseGrandTotal * $rate->getPoints() / $rate->getMoney(),
                $store
            );
        } else {
            $points = 0;
        }

        return $points;
    }

    /**
     * Get current checkout quote
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        if ($this->_appState->getAreaCode() ==  \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {
            return $this->_adminQuoteSessionFactory->create()->getQuote();
        }
        return $this->_checkoutSessionFactory->create()->getQuote();
    }

    /**
     * Get shipping earning point from $order
     *
     * @param \Magento\Sales\Model\Order $order
     * @return int
     */
    public function getShippingEarningPoints($order)
    {
        if (!$order instanceof \Magento\Sales\Model\Order) {
            return 0;
        }
        $shippingEarningPoints = $order->getRewardpointsEarn();
        foreach ($order->getAllItems() as $item) {
            if ($item->getParentItemId()) {
                continue;
            }
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildrenItems() as $child) {
                    $shippingEarningPoints -= $child->getRewardpointsEarn();
                }
            } elseif ($item->getProduct()) {
                $shippingEarningPoints -= $item->getRewardpointsEarn();
            }
        }
        return $shippingEarningPoints;
    }
}
