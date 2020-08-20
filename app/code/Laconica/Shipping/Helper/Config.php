<?php

namespace Laconica\Shipping\Helper;

use Magento\Store\Model\ScopeInterface;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function getRestrictedRatesNames()
    {
        $names = $this->scopeConfig->getValue(
            'carriers/fedex/restricted_rate_names',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $names = explode(',', $names);
        $names = array_map('trim', $names);

        return $names;
    }
}