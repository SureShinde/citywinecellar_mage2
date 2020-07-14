<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Plugin\ShopbyBrand\Block;

use Amasty\Blog\Helper\Data;

class BrandsPopupPlugin
{
    /**
     * @var Data
     */
    private $helper;

    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param \Amasty\ShopbyBrand\Block\BrandsPopup $subject
     */
    public function beforeToHtml(\Amasty\ShopbyBrand\Block\BrandsPopup $subject)
    {
        if ($this->helper->isCurrentPageAmp()) {
            $subject->setTemplate('Amasty_Blog::amp/brands_popup.phtml');
        }
    }
}
