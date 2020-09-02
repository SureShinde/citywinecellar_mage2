<?php

namespace Laconica\Checkout\Plugin;

class LayoutProcessor
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    protected $scopeConfig;

    protected $requiredFields = [];
    protected $excludeFields = [];
    protected $pluginEnabled = false;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
        $this->setConfigValues();
    }

    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $layoutProcessor,
        array $result
    )
    {
        if (!$this->pluginEnabled) {
            return $result;
        }

        // Check billing form exist
        if (!isset($result['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children'])) {
            return $result;
        }

        // Check payment variants exist
        if (!isset($result['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children']['payments-list']['children'])) {
            return $result;
        }

        // Try exclude and update billing forms
        foreach ($result['components']['checkout']['children']['steps']['children']['billing-step']['children']
                 ['payment']['children']['payments-list']['children'] as $paymentCode => &$childInfo) {

            if (!isset($childInfo['children']['form-fields']['children'])) {
                continue;
            }

            foreach ($childInfo['children']['form-fields']['children'] as $fieldCode => &$fieldInfo) {
                $this->makeFieldRequire($fieldCode, $fieldInfo);
                if (in_array($fieldCode, $this->excludeFields)) {
                    unset($childInfo['children']['form-fields']['children'][$fieldCode]);
                }
            }
        }

        return $result;
    }

    /**
     * Set config values for plugin work
     */
    private function setConfigValues()
    {
        $this->pluginEnabled = $this->scopeConfig->getValue('laconica_checkout/billing_address/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $requiredFields = $this->scopeConfig->getValue('laconica_checkout/billing_address/required_fields', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->requiredFields = array_map('trim', explode(",", $requiredFields));

        $excludeFields = $this->scopeConfig->getValue('laconica_checkout/billing_address/exclude_fields', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->excludeFields = array_map('trim', explode(",", $excludeFields));
    }

    /**
     * Make address field required
     * @param $fieldCode
     * @param $fieldInfo
     */
    private function makeFieldRequire($fieldCode, &$fieldInfo)
    {
        if (in_array($fieldCode, $this->requiredFields)) {
            $fieldInfo['validation']['required-entry'] = 1;
        }
    }

}