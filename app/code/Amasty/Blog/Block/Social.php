<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block;

class Social extends \Amasty\Blog\Block\Content\Post
{
    /**
     * Social Networks
     *
     * @return array
     */
    public function getButtons()
    {
        return $this->getNetworksModel()->getNetworks();
    }

    /**
     * @return int|void
     */
    public function getButtonsCount()
    {
        return count($this->getButtons());
    }

    /**
     * @param $button
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getButtonUrl($button)
    {
        $url = $button->getUrl();

        $url = str_replace("{url}", urlencode($this->getPost()->getUrl()), $url);
        $url = str_replace("{title}", urlencode($this->getPost()->getTitle()), $url);
        $url = str_replace("{description}", urlencode($this->getPost()->getMetaDescription()), $url);

        if ($button->getImage()) {
            $url = str_replace("{image}", urlencode($this->getPost()->getPostThumbnailSrc()), $url);
        }

        return $url;
    }

    /**
     * @param $button
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getHasImage($button)
    {
        return ($button->getImage() && (bool)$this->getPost()->hasThumbnail()) || !$button->getImage();
    }

    /**
     * @param $button
     *
     * @return bool
     */
    public function isPreConfigAmpType($button)
    {
        $preConfigType = [
            'system',
            'email',
            'facebook',
            'linkedin',
            'pinterest',
            'tumblr',
            'twitter',
            'whatsapp',
            'line',
            'sms'
        ];

        return in_array($button->getValue(), $preConfigType);
    }

    /**
     * @param $buttonValue
     *
     * @return string
     */
    public function getAmpSocialButton($buttonValue)
    {
        switch ($buttonValue) {
            case 'vkontakte':
                $htmlIcon = $this->getSocialIcon('fab fa-vk -vk');
                break;
            case 'odnoklassniki':
                $htmlIcon = $this->getSocialIcon('fab fa-odnoklassniki -od');
                break;
            case 'blogger':
                $htmlIcon = $this->getSocialIcon('fab fa-blogger-b -blog');
                break;
            case 'digg':
                $htmlIcon = $this->getSocialIcon('fab fa-digg -digg');
                break;
            case 'delicious':
                $htmlIcon = $this->getSocialIcon('fab fa-delicious -del');
                break;
            case 'stumbleupon':
                $htmlIcon = $this->getSocialIcon('fab fa-stumbleupon -stum');
                break;
            case 'slashdot':
                $htmlIcon = $this->getSocialIcon('-slash');
                break;
            case 'reddit':
                $htmlIcon = $this->getSocialIcon('fab fa-reddit-alien -redd');
                break;
            default:
                $htmlIcon = '';
                break;
        }

        return $htmlIcon;
    }

    /**
     * @param $class
     *
     * @return string
     */
    private function getSocialIcon($class)
    {
        return '<i class="amblog-social-icon ' . $class . '"></i>';
    }
}
