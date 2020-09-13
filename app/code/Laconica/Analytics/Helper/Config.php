<?php

namespace Laconica\Analytics\Helper;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    const GTM_ID_XML_PATH = 'google/gtm/id';
    const GTM_ENABLED_XML_PATH = 'google/gtm/enable';
    const GTM_QUOTE_PAGES_XML_PATH = 'google/gtm/quote_pages';
    const GTM_EXPRESSIONS_LIMIT_XML_PATH = 'google/gtm/expressions_limit';
    const GTM_TRANSACTION_AFFILIATION_XML_PATH = 'google/gtm/transaction_affiliation';
    const GTM_AFFILIATION_XML_PATH = 'google/gtm/affiliation';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    protected $scopeConfig;

    private $gtmId;
    private $enabled;
    private $quotePages;
    private $expressionsLimit;
    private $transactionAffiliation;
    private $affiliation;

    /**
     * Config constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return mixed
     */
    public function getGtmId()
    {
        if ($this->gtmId === null) {
            $this->gtmId = $this->scopeConfig->getValue(self::GTM_ID_XML_PATH, ScopeInterface::SCOPE_STORE);
        }
        return $this->gtmId;
    }

    /**
     * @return mixed
     */
    public function isEnabled()
    {
        if ($this->enabled === null) {
            $this->enabled = $this->scopeConfig->getValue(self::GTM_ENABLED_XML_PATH, ScopeInterface::SCOPE_STORE);
        }
        return $this->enabled;
    }

    /**
     * @return array
     */
    public function getQuotePages()
    {
        if ($this->quotePages === null) {
            $pages = $this->scopeConfig->getValue(self::GTM_QUOTE_PAGES_XML_PATH, ScopeInterface::SCOPE_STORE);
            $this->quotePages = array_map('trim', explode(',', $pages));
        }
        return $this->quotePages;
    }

    /**
     * @return int
     */
    public function getExpressionsLimit()
    {
        if ($this->expressionsLimit === null) {
            $limit = $this->scopeConfig->getValue(self::GTM_EXPRESSIONS_LIMIT_XML_PATH, ScopeInterface::SCOPE_STORE);
            $this->expressionsLimit = intval($limit);
        }
        return $this->expressionsLimit;
    }

    public function getTransactionAffiliation(){
        if($this->transactionAffiliation === null){
            $this->transactionAffiliation = $this->scopeConfig->getValue(self::GTM_TRANSACTION_AFFILIATION_XML_PATH, ScopeInterface::SCOPE_STORE);
        }
        return $this->transactionAffiliation;
    }

    public function getAffiliation(){
        if($this->affiliation === null){
            $this->affiliation = $this->scopeConfig->getValue(self::GTM_AFFILIATION_XML_PATH, ScopeInterface::SCOPE_STORE);
        }
        return $this->affiliation;
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