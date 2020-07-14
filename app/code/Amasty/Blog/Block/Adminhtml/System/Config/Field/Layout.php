<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Adminhtml\System\Config\Field;

use Amasty\Blog\Block\Adminhtml\System\Config\Field\Layout\Renderer;

/**
 * Class
 */
class Layout extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \Amasty\Blog\Helper\Data
     */
    private $helperData;

    /**
     * @var \Amasty\Blog\Helper\Data\Layout
     */
    private $helperLayout;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Amasty\Blog\Helper\Data $helperData,
        \Amasty\Blog\Helper\Data\Layout $helperLayout,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helperData = $helperData;
        $this->helperLayout = $helperLayout;
    }

    /**
     * @return array
     */
    protected function getContentBlocks()
    {
        $result = $this->helperLayout->getBlocks('content');

        return $result;
    }

    /**
     * @return array
     */
    protected function getLayouts()
    {
        $config = [
            ['value' => 'one-column', 'label' => __("One Column")],
            ['value' => 'two-columns-left', 'label' => __("Two Columns and Left Sidebar")],
            ['value' => 'two-columns-right', 'label' => __("Two Columns and Right Sidebar")],
            ['value' => 'three-columns', 'label' => __("Three Columns")],
        ];

        return $config;
    }

    /**
     * @param array $blocks
     * @return array
     */
    private function wrapSkinImages(array $blocks)
    {
        $data = [];
        foreach ($blocks as $block) {
            if (isset($block['backend_image'])) {
                $backendImage = $block['backend_image'];
                $backendImage = $this->getViewFileUrl('Amasty_Blog/' . $backendImage);
                $block['backend_image'] = $backendImage;
            }
            $data[] = $block;
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getLayoutConfig()
    {
        $contentBlocks = $this->getContentBlocks();
        $sidebarBlocks = $this->helperLayout->getBlocks('sidebar');

        return [
            'content' => $this->wrapSkinImages($contentBlocks),
            'sidebar' => $this->wrapSkinImages($sidebarBlocks),
            'layouts' => $this->getLayouts(),
            'delete_message' => __("Are you sure?"),
        ];
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return bool|string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $result = false;
        $renderer = $this->getLayout()->createBlock(Renderer::class);

        if ($renderer) {
            $result = $renderer->setElementId($element->getHtmlId())
                ->setElementName($element->getName())
                ->setElementValue($element->getValue())
                ->setLayoutConfig($this->getLayoutConfig())
                ->toHtml();
        }

        return $result;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $id = $element->getHtmlId();
        $checkboxLabel = '';
        $html = '<td colspan="5">';
        $namePrefix = preg_replace('#\[value\](\[\])?$#', '', $element->getName());

        $html .= '<div class="label">';
        $html .= $element->getLabel();
        $html .= '</div>';

        $addInheritCheckbox = false;
        if ($element->getCanUseWebsiteValue()) {
            $addInheritCheckbox = true;
            $checkboxLabel = __('Use Website');
        } elseif ($element->getCanUseDefaultValue()) {
            $addInheritCheckbox = true;
            $checkboxLabel = __('Use Default');
        }

        $inherit = '';
        if ($addInheritCheckbox) {
            $inherit = $element->getInherit() == 1 ? 'checked="checked"' : '';
            if ($inherit) {
                $element->setDisabled(true);
            }
        }

        $html .= '<div class="scope-label">';
        if ($element->getScope()) {
            $html .= $element->getScopeLabel();
        }
        $html .= '</div>';

        if ($addInheritCheckbox) {
            $defText = $this->escapeHtml($element->getDefaultValue());
            $html .= '<div class="use-default">';
            $html .= '<input id="' . $id . '_inherit" name="'
                . $namePrefix . '[inherit]" type="checkbox" value="1" class="checkbox config-inherit" '
                . $inherit . ' " /> ';
            $html .= '<label for="' . $id . '_inherit" class="inherit" title="'
                . $defText . '">' . $checkboxLabel . '</label>';
            $html .= '</div>';
        }

        $html .= "<div class=\"fixed\"></div>";

        $html .= "<div class=\"layout-element\">";
        $html .= $this->_getElementHtml($element);
        $html .= "</div>";
        $html .= "</td>";

        return '<tr id="row_' . $element->getHtmlId() . '">' . $html . '</tr>';
    }
}
