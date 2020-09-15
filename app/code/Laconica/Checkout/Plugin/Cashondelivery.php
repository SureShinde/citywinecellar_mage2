<?php

namespace Laconica\Checkout\Plugin;

class Cashondelivery
{

    private $backendSession;

    private $scopeConfig;

    public function __construct(
        \Magento\Backend\Model\Auth\Session $backendSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->backendSession = $backendSession;
        $this->scopeConfig = $scopeConfig;
    }

    public function isAdminLogin()
    {
        return $this->backendSession->getUser() && $this->backendSession->getUser()->getId();
    }

    public function afterIsActive(
        \Magento\OfflinePayments\Model\Cashondelivery $subject,
        $result
    )
    {
        if ($this->isAdminLogin() && $this->scopeConfig->getValue('payment/checkmo/admin_active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            return true;
        }
        return $result;
    }
}