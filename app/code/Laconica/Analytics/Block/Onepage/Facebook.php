<?php

namespace Laconica\Analytics\Block\Onepage;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Facebook extends Template
{
    const ENABLED_XML_PATH = 'google/facebook_conversion/enable';
    const TRACK_ID_XML_PATH = 'google/facebook_conversion/id';

    /**
     * @var \Magento\Checkout\Model\Session $checkoutSession
     */
    private $checkoutSession;

    private $enabled;
    private $trackId;

    /**
     * Facebook constructor.
     * @param Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = []
    )
    {
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function isEnabled()
    {
        if ($this->enabled === null) {
            $this->enabled = $this->_scopeConfig->getValue(self::ENABLED_XML_PATH, ScopeInterface::SCOPE_STORE);
        }
        return $this->enabled;
    }

    /**
     * @return mixed
     */
    public function getTrackId()
    {
        if ($this->trackId === null) {
            $this->trackId = $this->_scopeConfig->getValue(self::TRACK_ID_XML_PATH, ScopeInterface::SCOPE_STORE);
        }
        return $this->trackId;
    }

    /**
     * @return false|string
     */
    public function getPushJson()
    {
        $order = $this->checkoutSession->getLastRealOrder();
        if (!$order || !$order->getId()) {
            return false;
        }
        $data = [
            'value' => $this->formatPrice($order->getGrandTotal()),
            'currency' => $order->getOrderCurrencyCode()
        ];
        return json_encode($data);
    }

    /**
     * @return float|int
     */
    public function getGrandTotal()
    {
        $order = $this->checkoutSession->getLastRealOrder();
        if (!$order || !$order->getId()) {
            return $this->formatPrice(0);
        }
        return $this->formatPrice($order->getGrandTotal());
    }

    /**
     * @return string|null
     */
    public function getCurrencyCode()
    {
        $order = $this->checkoutSession->getLastRealOrder();
        if (!$order || !$order->getId()) {
            try {
                return $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
            } catch (NoSuchEntityException $e) {
                return null;
            }
        }
        return $order->getOrderCurrencyCode();
    }

    /**
     * @param $price
     * @return float
     */
    public function formatPrice($price)
    {
        return floatval(number_format($price, PriceCurrencyInterface::DEFAULT_PRECISION, '.', ''));
    }
}