<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Adminhtml\System\Config\Field\Layout;

/**
 * Class
 */
class Renderer extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    private $elementName;

    /**
     * @var int
     */
    private $elementId;

    /**
     * @var string
     */
    private $elementValue;

    /**
     * @var array
     */
    private $layoutConfig = [];

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate("Amasty_Blog::system/config/field/element.phtml");
    }

    /**
     * @return string
     */
    public function getElementValue()
    {
        return $this->elementValue;
    }

    /**
     * @param $elementValue
     *
     * @return $this
     */
    public function setElementValue($elementValue)
    {
        $this->elementValue = $elementValue;

        return $this;
    }

    /**
     * @return string
     */
    public function getElementName()
    {
        return $this->elementName;
    }

    /**
     * @param $elementName
     *
     * @return $this
     */
    public function setElementName($elementName)
    {
        $this->elementName = $elementName;

        return $this;
    }

    /**
     * @return int
     */
    public function getElementId()
    {
        return $this->elementId;
    }

    /**
     * @param $elementId
     *
     * @return $this
     */
    public function setElementId($elementId)
    {
        $this->elementId = $elementId;

        return $this;
    }

    /**
     * @param array $config
     *
     * @return $this
     */
    public function setLayoutConfig($config)
    {
        $this->layoutConfig = $config;

        return $this;
    }

    /**
     * @return array
     */
    public function getLayoutConfig()
    {
        return $this->layoutConfig;
    }

    /**
     * @return string
     */
    public function getLayoutConfigJson()
    {
        return \Zend_Json::encode($this->getLayoutConfig());
    }
}
