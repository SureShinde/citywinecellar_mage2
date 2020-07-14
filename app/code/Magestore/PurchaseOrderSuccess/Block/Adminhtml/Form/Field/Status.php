<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\Form\Field;

/**
 * Class Status
 * @package Magestore\PurchaseOrderSuccess\Block\Adminhtml\Form\Field
 */
class Status extends \Magento\Framework\View\Element\Html\Select
{
    const ENABLE_VALUE = 1;
    const DISABLE_VALUE = 0;
    const ENABLE_LABEL = 'Enable';
    const DISABLE_LABEL = 'Disable';
    
    
    public function getAllOptions(){
        return [self::ENABLE_VALUE => __(self::ENABLE_LABEL), self::DISABLE_VALUE => __(self::DISABLE_LABEL)];
    }
    
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
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            foreach ($this->getAllOptions() as $value => $label) {
                $this->addOption($value, addslashes($label));
            }
        }
        return parent::_toHtml();
    }
    
    
}
