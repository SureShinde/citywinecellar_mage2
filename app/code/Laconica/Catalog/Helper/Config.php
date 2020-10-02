<?php

namespace Laconica\Catalog\Helper;

class Config
{
    const XML_PATH_SKIP_ENABLED = 'cataloginventory/item_options/skip_stock_manage_shipment';
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    private $scopeConfig;

    /**
     * @var \Magento\Backend\Model\Auth\Session $backendSession
     */
    private $backendSession;

    public function __construct(
        \Magento\Backend\Model\Auth\Session $backendSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->backendSession = $backendSession;
    }

    /**
     * @return bool
     */
    public function backendManageStock(){
        return $this->skipStockManageShipmentEnabled() && $this->isAdminLogin();
    }

    /**
     * @return mixed
     */
    private function skipStockManageShipmentEnabled()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SKIP_ENABLED);
    }

    /**
     * @return bool
     */
    private function isAdminLogin()
    {
        return $this->backendSession->getUser() && $this->backendSession->getUser()->getId();
    }
}