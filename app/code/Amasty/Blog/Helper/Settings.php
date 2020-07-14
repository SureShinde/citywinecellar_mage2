<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\AbstractHelper;

class Settings extends AbstractHelper implements \Magento\Framework\Data\CollectionDataSourceInterface
{
    /**
     * @param $path
     * @param int $storeId
     * @return mixed
     */
    public function getModuleConfig($path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            'amblog/' . $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return bool
     */
    public function isDisplayAtFooter()
    {
        return $this->getStoreConfig('amblog/display_settings/display_at_footer');
    }

    /**
     * @return bool
     */
    public function isDisplayAtToolbar()
    {
        return $this->getStoreConfig('amblog/display_settings/display_at_toolbar');
    }

    /**
     * @return bool
     */
    public function isDisplayAtCategoryMenu()
    {
        return $this->getStoreConfig('amblog/display_settings/display_at_category');
    }

    /**
     * @return string
     */
    public function getBlogPostfix()
    {
        return $this->getStoreConfig('amblog/redirect/url_postfix');
    }

    /**
     * @return string
     */
    public function getSeoRoute()
    {
        return trim($this->getStoreConfig('amblog/search_engine/route'));
    }

    /**
     * @return string
     */
    public function getSeoTitle()
    {
        return $this->getStoreConfig('amblog/search_engine/title');
    }

    /**
     * @return string
     */
    public function getBlogLabel()
    {
        return $this->getStoreConfig('amblog/display_settings/label');
    }

    /**
     * @return bool
     */
    public function showInNavMenu()
    {
        return (bool)$this->getStoreConfig('amblog/display_settings/display_at_category');
    }

    /**
     * @return int
     */
    public function getPostsLimit()
    {
        return $this->getStoreConfig('amblog/list/count_per_page');
    }

    /**
     * @return bool
     */
    public function getRedirectToSeoFormattedUrl()
    {
        return $this->getFlag('amblog/redirect/redirect_to_seo_formatted_url');
    }

    /**
     * @return string
     */
    public function getIconColorClass()
    {
        return $this->getStoreConfig('amblog/style/color_sheme');
    }

    /**
     * @return string
     */
    public function getBlogMetaDescription()
    {
        return $this->getStoreConfig('amblog/search_engine/meta_description');
    }

    /**
     * @return string
     */
    public function getBlogMetaTitle()
    {
        return $this->getStoreConfig('amblog/search_engine/meta_title');
    }

    /**
     * @return string
     */
    public function getBlogMetaKeywords()
    {
        return $this->getStoreConfig('amblog/search_engine/meta_keywords');
    }

    /**
     * @return array
     */
    public function getMobileList()
    {
        return $this->getStoreConfig('amblog/layout/mobile_list');
    }

    /**
     * @return string
     */
    public function getMobilePost()
    {
        return $this->getStoreConfig('amblog/layout/mobile_post');
    }

    /**
     * @return string
     */
    public function getDesktopPost()
    {
        return $this->getStoreConfig('amblog/layout/desktop_post');
    }

    /**
     * @return mixed
     */
    public function getDesktopList()
    {
        return $this->getStoreConfig('amblog/layout/desktop_list');
    }

    /**
     * @return bool
     */
    public function getShowAuthor()
    {
        return $this->getStoreConfig('amblog/post/display_author');
    }

    /**
     * @return bool
     */
    public function getDisplayViews()
    {
        return $this->getStoreConfig('amblog/post/display_views');
    }

    /**
     * @return string
     */
    public function getDateFormat()
    {
        return $this->getStoreConfig('amblog/post/date_manner');
    }

    /**
     * @return bool
     */
    public function getUseTags()
    {
        return $this->getFlag('amblog/post/display_tags');
    }

    /**
     * @return bool
     */
    public function getUseCategories()
    {
        return $this->getFlag('amblog/post/display_categories');
    }

    /**
     * @return int
     */
    public function getCategoriesLimit()
    {
        return $this->getStoreConfig('amblog/post/categories_limit');
    }

    /**
     * @return int
     */
    public function getRecentPostsLimit()
    {
        return $this->getStoreConfig('amblog/recent_posts/record_limit');
    }

    /**
     * @return int
     */
    public function getRecentPostsImageWidth()
    {
        return $this->getStoreConfig('amblog/recent_posts/image_width') ?: 60;
    }

    /**
     * @return int
     */
    public function getRecentPostsImageHeight()
    {
        return $this->getStoreConfig('amblog/recent_posts/image_height') ?: 60;
    }

    /**
     * @return bool
     */
    public function isRecentPostsUseImage()
    {
        return (bool)$this->getStoreConfig('amblog/recent_posts/display_image');
    }

    /**
     * @return bool
     */
    public function getRecentPostsDisplayShort()
    {
        return $this->getStoreConfig('amblog/recent_posts/display_short');
    }

    /**
     * @return bool
     */
    public function getRecentPostsDisplayDate()
    {
        return $this->getStoreConfig('amblog/recent_posts/display_date');
    }

    /**
     * @return int
     */
    public function getCommentsLimit()
    {
        return $this->getStoreConfig('amblog/comments/record_limit');
    }

    /**
     * @return bool
     */
    public function getRecentCommentsDisplayShort()
    {
        return $this->getStoreConfig('amblog/comments/display_short');
    }

    /**
     * @return bool
     */
    public function getRecentCommentsDisplayDate()
    {
        return $this->getStoreConfig('amblog/comments/display_date');
    }

    /**
     * @return int
     */
    public function getTagsMinimalPostCount()
    {
        return $this->getStoreConfig('amblog/tags/minimal_post_count') ? : 0;
    }

    /**
     * @return bool
     */
    public function getTagsMtEnabled()
    {
        return $this->getStoreConfig('amblog/tags/mt_enabled');
    }

    /**
     * @return int
     */
    public function getTagsMtHeight()
    {
        return $this->getStoreConfig('amblog/tags/mt_height');
    }

    /**
     * @return string
     */
    public function getTagsMtTextcolor()
    {
        return $this->getStoreConfig('amblog/tags/mt_textcolor');
    }

    /**
     * @return string
     */
    public function getTagsMtTextcolor2()
    {
        return $this->getStoreConfig('amblog/tags/mt_textcolor2');
    }

    /**
     * @return string
     */
    public function getTagsMtHiColor()
    {
        return $this->getStoreConfig('amblog/tags/mt_hicolor');
    }

    /**
     * @return int
     */
    public function getRecentPostsShortLimit()
    {
        return $this->getStoreConfig('amblog/recent_posts/short_limit');
    }

    /**
     * @return bool
     */
    public function getShowPrintLink()
    {
        return $this->getStoreConfig('amblog/post/display_print');
    }

    /**
     * @param $title
     *
     * @return string
     */
    public function getPrefixTitle($title)
    {
        if ($prefix = $this->getStoreConfig('amblog/search_engine/title')) {
            $title = $prefix . " - " . $title;
        }

        return $title;
    }

    /**
     * @param $title
     *
     * @return string
     */
    public function getSuffixMetaTitle($title)
    {
        if ($postfix = $this->getStoreConfig('amblog/search_engine/title_suffix')) {
            $title = $title . " | " . $postfix;
        }

        return $title;
    }

    /**
     * @return bool
     */
    public function getSocialEnabled()
    {
        return $this->getStoreConfig('amblog/social/enabled');
    }

    /**
     * @return bool
     */
    public function getHelpfulEnabled()
    {
        return $this->getStoreConfig('amblog/post/helpful');
    }

    /**
     * @return bool
     */
    public function getCommentsAutoapprove()
    {
        return $this->getStoreConfig('amblog/comments/autoapprove');
    }

    /**
     * @param null $route
     *
     * @return mixed
     */
    public function getConfPlace($route = null)
    {
        return $this->getStoreConfig('amblog/general/' . $route);
    }

    /**
     * @return bool
     */
    public function getCommentsAllowGuests()
    {
        return $this->getStoreConfig('amblog/comments/allow_guests');
    }

    /**
     * @return bool
     */
    public function getUseComments()
    {
        return $this->getStoreConfig('amblog/comments/use_comments');
    }

    /**
     * @return string
     */
    public function getBreadcrumb()
    {
        return $this->getStoreConfig('amblog/search_engine/bread') ?: __('Blog');
    }

    /**
     * @return int
     */
    public function getImageWidth()
    {
        return (int)$this->getStoreConfig('amblog/post/image_width');
    }

    /**
     * @return int
     */
    public function getImageHeight()
    {
        return (int)$this->getStoreConfig('amblog/post/image_height');
    }

    /**
     * @return int
     */
    public function getLogoWidth()
    {
        return (int)$this->getStoreConfig('amblog/accelerated_mobile_pages/logo/image_width');
    }

    /**
     * @return int
     */
    public function getLogoHeight()
    {
        return (int)$this->getStoreConfig('amblog/accelerated_mobile_pages/logo/image_height');
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    private function getStoreConfig($key)
    {
        return $this->scopeConfig->getValue($key, \Magento\Store\Model\ScopeInterface::SCOPE_STORES);
    }

    /**
     * @param $key
     *
     * @return bool
     */
    private function getFlag($key)
    {
        $configValue = $this->scopeConfig->isSetFlag(
            $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );

        return $configValue;
    }

    /**
     * @return string
     */
    public function getTagColor()
    {
        return $this->getStoreConfig('amblog/accelerated_mobile_pages/design/tag_color');
    }

    /**
     * @return string
     */
    public function getLinkColor()
    {
        return $this->getStoreConfig('amblog/accelerated_mobile_pages/design/link_color');
    }

    /**
     * @return string
     */
    public function getLinkColorHover()
    {
        return $this->getStoreConfig('amblog/accelerated_mobile_pages/design/link_color_hover');
    }

    /**
     * @return string
     */
    public function getButtonBackground()
    {
        return $this->getStoreConfig('amblog/accelerated_mobile_pages/design/button_background_color');
    }

    /**
     * @return string
     */
    public function getButtonBackgroundHover()
    {
        return $this->getStoreConfig('amblog/accelerated_mobile_pages/design/button_background_color_hover');
    }

    /**
     * @return string
     */
    public function getButtonTextColor()
    {
        return $this->getStoreConfig('amblog/accelerated_mobile_pages/design/button_text_color');
    }

    /**
     * @return string
     */
    public function getButtonTextColorHover()
    {
        return $this->getStoreConfig('amblog/accelerated_mobile_pages/design/button_text_color_hover');
    }

    /**
     * @return string
     */
    public function getFooterBackground()
    {
        return $this->getStoreConfig('amblog/accelerated_mobile_pages/design/footer_background');
    }

    /**
     * @return string
     */
    public function getFooterLinkColor()
    {
        return $this->getStoreConfig('amblog/accelerated_mobile_pages/design/footer_link');
    }

    /**
     * @return bool
     */
    public function getCommentNotificationsEnabled()
    {
        return (bool)$this->getModuleConfig('notify_admin_new_comment/enabled');
    }

    /**
     * @return string
     */
    public function getNotificationEmailTemplate()
    {
        return $this->getModuleConfig('notify_admin_new_comment/email_template');
    }

    /**
     * @return string
     */
    public function getNotificationSender()
    {
        return $this->getModuleConfig('notify_admin_new_comment/sender');
    }

    /**
     * @return array[]|false|string[]
     */
    public function getNotificationRecievers()
    {
        return preg_split('/\n|\r\n?/', $this->getModuleConfig('notify_admin_new_comment/receiver'));
    }
}
