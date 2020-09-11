<?php

namespace Laconica\Analytics\Helper;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Config
{
    const GTM_ID_XML_PATH = 'google/gtm/id';
    const GTM_ENABLED_XML_PATH = 'google/gtm/enable';
    const GTM_QUOTE_PAGES_XML_PATH = 'google/gtm/quote_pages';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    protected $scopeConfig;

    private $gtmId;
    private $enabled;
    private $quotePages;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getGtmId()
    {
        if ($this->gtmId === null) {
            $this->gtmId = $this->scopeConfig->getValue(self::GTM_ID_XML_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        }
        return $this->gtmId;
    }

    public function isEnabled()
    {
        if ($this->enabled === null) {
            $this->enabled = $this->scopeConfig->getValue(self::GTM_ENABLED_XML_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        }
        return $this->enabled;
    }

    public function getQuotePages()
    {
        if ($this->quotePages === null) {
            $pages = $this->scopeConfig->getValue(self::GTM_QUOTE_PAGES_XML_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $this->quotePages = array_map('trim', explode(',', $pages));
        }
        return $this->quotePages;
    }

    /**
     * @param $price
     * @return float
     */
    public function formatPrice($price)
    {
        return number_format($price, PriceCurrencyInterface::DEFAULT_PRECISION, '.', '');
    }
}