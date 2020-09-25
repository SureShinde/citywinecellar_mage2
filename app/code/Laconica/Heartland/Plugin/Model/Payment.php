<?php

namespace Laconica\Heartland\Plugin\Model;

class Payment
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    private $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function afterIsActive(
        \HPS\Heartland\Model\Payment $subject,
        $result
    )
    {
        if ($this->isEnabled()) {
            return false;
        }
        return $result;
    }

    private function isEnabled()
    {
        return $this->scopeConfig->getValue('payment/heartland_section/heartland/disable_heartland_frontend', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE);
    }
}