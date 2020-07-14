<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Block\Adminhtml\Widget\Attribute\Renderer;

class Price extends \Amasty\Xlanding\Block\Adminhtml\Widget\Attribute\Renderer
{
    /**
     * {@inheritdoc}
     */
    public function render()
    {
        return '<span>' . $this->escaper->escapeHtml($this->getLabel() . ': ') . $this->getValue() . '</span></br>';
    }
}
