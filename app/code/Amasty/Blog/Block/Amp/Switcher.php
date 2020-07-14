<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Amp;

use Magento\Framework\App\ActionInterface;

/**
 * Class Switcher
 */
class Switcher extends \Magento\Store\Block\Switcher
{
    /**
     * @param \Magento\Store\Model\Store $store
     *
     * @return string
     */
    public function getStoreUrlAmp(\Magento\Store\Model\Store $store)
    {
        $uenc = ActionInterface::PARAM_NAME_URL_ENCODED;

        $urlData = json_decode($this->getTargetStorePostData($store), true);

        $url = str_replace("&amp;", "&", $urlData['action']);

        if (strpos($url, '&' . $uenc) !== false) {
            $url = strstr($url, '&' . $uenc, true);
        }

        $uencValue = $urlData['data'][$uenc];

        $url .= '&' . $uenc . '=' . $uencValue;

        return $url;
    }
}
