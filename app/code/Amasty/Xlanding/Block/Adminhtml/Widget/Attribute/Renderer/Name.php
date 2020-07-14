<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Block\Adminhtml\Widget\Attribute\Renderer;

class Name extends \Amasty\Xlanding\Block\Adminhtml\Widget\Attribute\Renderer
{
    /**
     * @return string
     */
    public function render()
    {
        return '<span>' . $this->escaper->escapeHtml($this->getValue()) . '</span></br>';
    }
}
