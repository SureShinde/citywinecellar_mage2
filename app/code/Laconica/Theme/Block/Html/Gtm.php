<?php

namespace Laconica\Theme\Block\Html;

use Magento\Framework\View\Element\Template;

class Gtm extends Template
{
    const GTM_ID_XML_PATH = 'google/gtm/id';
    const GTM_ENABLED_XML_PATH = 'google/gtm/enable';

    public function getGtmId()
    {
        return $this->_scopeConfig->getValue(self::GTM_ID_XML_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function isEnabled()
    {
        return $this->_scopeConfig->getValue(self::GTM_ENABLED_XML_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}