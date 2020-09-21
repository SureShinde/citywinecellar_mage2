<?php

namespace Laconica\Checkout\Plugin;

class StateFilter
{
    const STATE_FILTER_PATH = 'checkout/state_filter/allowed_states';
    const STATE_FILTER_ENABLED = 'checkout/state_filter/enabled';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    private $scopeConfig;

    private $allowedUsStates;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function afterToOptionArray(
        \Magento\Directory\Model\ResourceModel\Region\Collection $subject,
        $options
    ) {
        if (!$this->isEnabled()) {
            return $options;
        }
        $allowedStates = $this->scopeConfig->getValue(self::STATE_FILTER_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE);
        $this->allowedUsStates = explode(",", $allowedStates);
        $result[] = (isset($options[0]) && is_array($options)) ? array_shift($options) : [];
        foreach ($options as $option) {
            if (isset($option['value']) && in_array($option['value'], $this->allowedUsStates)) {
                array_push($result, $option);
            }
        }
        return $result;
    }

    public function isEnabled()
    {
        return $this->scopeConfig->getValue(self::STATE_FILTER_ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE);
    }
}