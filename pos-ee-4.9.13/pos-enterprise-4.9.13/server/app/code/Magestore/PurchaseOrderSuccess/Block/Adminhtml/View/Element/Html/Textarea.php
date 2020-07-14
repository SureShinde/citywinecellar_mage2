<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\View\Element\Html;

/**
 * Textarea element block
 */
class Textarea extends \Magento\Framework\View\Element\Template
{
    /**
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }
    
    /**
     * Render block HTML
     *
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _toHtml()
    {
        $columnName = $this->getColumnName();
        $html = '<textarea type="text" name="' . $this->getName() . '" id="' . $this->getId() . '" ';
        $html .= 'class="' . $this->getClass() . '" ' . $this->getExtraParams() . '>';
        $html .= '<%-' . $columnName . '%>';
        $html .= '</textarea>';

        return $html;
    }
}
