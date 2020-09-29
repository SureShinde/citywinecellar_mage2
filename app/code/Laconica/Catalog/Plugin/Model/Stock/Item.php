<?php

namespace Laconica\Catalog\Plugin\Model\Stock;

use Magento\CatalogInventory\Model\Stock;

class Item
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

    public function afterGetBackorders(
        Stock\Item $subject,
        $result
    )
    {
        if ($this->isEnabled()) {
            return Stock::BACKORDERS_YES_NONOTIFY;
        }
        return $result;
    }

    private function isEnabled()
    {
        return $this->scopeConfig->getValue('cataloginventory/item_options/enable_backorders_backend');
    }
}