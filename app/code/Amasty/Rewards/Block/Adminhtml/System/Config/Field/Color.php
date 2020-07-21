<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Block\Adminhtml\System\Config\Field;

class Color extends \Magento\Config\Block\System\Config\Form\Field
{
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->setData('readonly', 1);
        $html = $element->getElementHtml();
        $value = $element->getData('value');
        $inverseHex = $value ? '#' . dechex(16777215 - hexdec($value)) : "";

        $html .= '<script type ="text/x-magento-init"> 
            {
                "*": {
                    "Amasty_Rewards/js/color": {
                        "htmlId":"' . $element->getHtmlId() . '",
                        "value":"' . $value . '",
                        "inverseHex": "' . $inverseHex . '"
                    }
                }
            }
        </script>';

        return $html;
    }
}
