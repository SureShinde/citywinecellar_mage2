<?php

namespace Laconica\Analytics\Helper;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Config
{
    const GTM_ID_XML_PATH = 'google/gtm/id';
    const GTM_ENABLED_XML_PATH = 'google/gtm/enable';
    const GTM_QUOTE_PAGES_XML_PATH = 'google/gtm/quote_pages';
    const GTM_EXPRESSIONS_LIMIT_XML_PATH = 'google/gtm/expressions_limit';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    protected $scopeConfig;

    private $gtmId;
    private $enabled;
    private $quotePages;
    private $expressionsLimit;

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

    public function getExpressionsLimit(){
        if ($this->expressionsLimit === null) {
            $limit = $this->scopeConfig->getValue(self::GTM_EXPRESSIONS_LIMIT_XML_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $this->expressionsLimit = intval($limit);
        }
        return $this->expressionsLimit;
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