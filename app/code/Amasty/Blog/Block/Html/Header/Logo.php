<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Html\Header;

/**
 * Class Logo
 */
class Logo extends \Magento\Theme\Block\Html\Header\Logo
{
    const AMASTY_BLOG_LOGO_HTML = 'amasty/blog/logo_html';

    /**
     * @return int
     */
    public function getLogoWidth()
    {
        return $this->getData('settings_helper')->getLogoWidth();
    }

    /**
     * @return int
     */
    public function getLogoHeight()
    {
        return $this->getData('settings_helper')->getLogoHeight();
    }

    /**
     * @return string
     */
    public function getLogoUrl()
    {
        $folderName = self::AMASTY_BLOG_LOGO_HTML;
        $storeLogoPath = $this->_scopeConfig->getValue(
            'amblog/accelerated_mobile_pages/logo/image',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $path = $folderName . '/' . $storeLogoPath;
        $logoUrl = $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . $path;

        if ($storeLogoPath == null || !$this->_isFile($path)) {
            $logoUrl = $this->_getLogoUrl();
        }

        return $logoUrl;
    }

    /**
     * @return \Amasty\Blog\Helper\Settings
     */
    public function getSettings()
    {
        return $this->getData('settings_helper');
    }
}
