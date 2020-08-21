<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\Form\Field;

/**
 * Block Field Status
 */
class Status extends \Magento\Framework\View\Element\Html\Select
{
    const ENABLE_VALUE = 1;
    const DISABLE_VALUE = 0;
    const ENABLE_LABEL = 'Enable';
    const DISABLE_LABEL = 'Disable';

    /**
     * Get All Options
     *
     * @return array
     */
    public function getAllOptions()
    {
        return [self::ENABLE_VALUE => __('Enable'), self::DISABLE_VALUE => __('Disable')];
    }

    /**
     * Set Input Name
     *
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
                // phpcs:ignore Magento2.Functions.DiscouragedFunction
                $this->addOption($value, addslashes($label));
            }
        }
        return parent::_toHtml();
    }
}
