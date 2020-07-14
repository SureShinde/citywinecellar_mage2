<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Adminhtml\Settings;

/**
 * Class
 */
class Colors extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return bool|string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $render = $this->getLayout()->createBlock(
            \Amasty\Blog\Block\Adminhtml\System\Config\Form\Element\Colors\Render::class
        );

        return $render ? $render->toHtml() : false;
    }
}
