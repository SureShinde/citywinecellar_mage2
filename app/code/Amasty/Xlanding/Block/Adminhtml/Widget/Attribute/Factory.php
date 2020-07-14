<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Block\Adminhtml\Widget\Attribute;

class Factory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $attributeCode
     * @return \Amasty\Xlanding\Block\Adminhtml\Widget\Attribute\Renderer
     */
    public function create($attributeCode)
    {
        $attributeCode = strtolower($attributeCode);
        $renderers = $this->_prepareAttributeRenderers();

        $renderer = isset($renderers[$attributeCode]) ? $renderers[$attributeCode]['renderer']
            : \Amasty\Xlanding\Block\Adminhtml\Widget\Attribute\Renderer::class;

        return $this->objectManager->create($renderer);
    }

    /**
     * Formatted as 'attribute_code' => [config]
     * @return array
     */
    protected function _prepareAttributeRenderers()
    {
        $renderers = [
            'price' => [
                'renderer' => \Amasty\Xlanding\Block\Adminhtml\Widget\Attribute\Renderer\Price::class
            ],
            'stock' => [
                'renderer' => \Amasty\Xlanding\Block\Adminhtml\Widget\Attribute\Renderer\Stock::class
            ],
            'name' => [
                'renderer' => \Amasty\Xlanding\Block\Adminhtml\Widget\Attribute\Renderer\Name::class
            ]
        ];
        return $renderers;
    }
}
