<?php

namespace Laconica\Checkout\Helper;

use Magento\Store\Model\ScopeInterface;

class StateConfig
{
    const COMMON_ALLOWED_STATE_PATH = 'checkout/state_filter/common_allowed_states';
    const SPECIFIC_ALLOWED_STATE_PATH = 'checkout/state_filter/specific_allowed_states';
    const STATE_FILTER_ENABLED = 'checkout/state_filter/enabled';
    const ZIP_VALIDATION_ENABLED = 'checkout/state_filter/enabled_zip';
    const INVALID_CATEGORIES_LIST = 'checkout/state_filter/invalid_category';
    const JS_ERROR_MESSAGE_PATH = 'checkout/state_filter/js_error_message';
    const ZIP_JS_ERROR_MESSAGE_PATH = 'checkout/state_filter/zip_js_error_message';
    const ZIP_STATE_CONNECTION_TABLE = 'la_zip_region_connection';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    private $scopeConfig;

    /**
     * StateConfig constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Check is module enabled
     *
     * @return mixed
     */
    public function isEnabled()
    {
        return $this->scopeConfig->getValue(self::STATE_FILTER_ENABLED, ScopeInterface::SCOPE_WEBSITE);
    }

    public function isZipValidationEnabled()
    {
        return $this->scopeConfig->getValue(self::ZIP_VALIDATION_ENABLED, ScopeInterface::SCOPE_WEBSITE);
    }

    /**
     * Returns invalid categories array
     *
     * @return mixed
     */
    public function getInValidCategories()
    {
        $inValidCategories = $this->scopeConfig->getValue(self::INVALID_CATEGORIES_LIST, ScopeInterface::SCOPE_WEBSITE);
        return explode(",", $inValidCategories);
    }

    /**
     * Returns common allowed states
     *
     * @return array
     */
    public function getCommonAllowedStates()
    {
        $commonAllowedStates = $this->scopeConfig->getValue(self::COMMON_ALLOWED_STATE_PATH, ScopeInterface::SCOPE_WEBSITE);
        return explode(",", $commonAllowedStates);
    }


    /**
     * Returns specific allowed states
     *
     * @return array
     */
    public function getSpecificAllowedStates()
    {
        $specificAllowedStates = $this->scopeConfig->getValue(self::SPECIFIC_ALLOWED_STATE_PATH, ScopeInterface::SCOPE_WEBSITE);
        return explode(",", $specificAllowedStates);
    }

    /**
     * Returns js error message text
     * @return mixed
     */
    public function getJsMessageText()
    {
        return $this->scopeConfig->getValue(self::JS_ERROR_MESSAGE_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Returns zip js error message text
     * @return mixed
     */
    public function getZipJsMessageText()
    {
        return $this->scopeConfig->getValue(self::ZIP_JS_ERROR_MESSAGE_PATH, ScopeInterface::SCOPE_STORE);
    }
}