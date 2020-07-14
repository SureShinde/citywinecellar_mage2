<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PromoBanners
 */

namespace Amasty\PromoBanners\Block;

use Amasty\PromoBanners\Model\Rule;
use Magento\Framework\View\Element\Template;

class Container extends Template
{
    public function isVisible()
    {
        return $this->getPosition() != Rule::POS_AMONG_PRODUCTS;
    }
}
