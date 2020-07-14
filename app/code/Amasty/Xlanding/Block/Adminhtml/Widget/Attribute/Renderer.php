<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Block\Adminhtml\Widget\Attribute;

/**
 * @method string getLabel()
 * @method string getValue()
 */
class Renderer extends \Magento\Framework\DataObject
{
    /**
     * @var \Magento\Framework\Escaper
     */
    public $escaper;

    /**
     * @param \Magento\Framework\Escaper $escaper
     * @param array $data
     */
    public function __construct(\Magento\Framework\Escaper $escaper, array $data = [])
    {
        parent::__construct($data);
        $this->escaper = $escaper;
    }

    /**
     * @return string
     */
    public function render()
    {
        return '<span>' . $this->escaper->escapeHtml($this->getLabel() . ': ' . $this->getValue()) . '</span></br>';
    }
}
