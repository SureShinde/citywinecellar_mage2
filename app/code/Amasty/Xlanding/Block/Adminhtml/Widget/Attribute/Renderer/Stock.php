<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Block\Adminhtml\Widget\Attribute\Renderer;

class Stock extends \Amasty\Xlanding\Block\Adminhtml\Widget\Attribute\Renderer
{
    /**
     * @return string
     */
    public function getValue()
    {
        return parent::getValue() ? __('In Stock') : __('Out of Stock');
    }
}
