<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Adminhtml\System\Config\Form\Element\Colors;

/**
 * Class Render
 */
class Render extends \Magento\Backend\Block\Template
{
    /**
     * @var \Amasty\Blog\Helper\Config
     */
    private $helperConfig;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Amasty\Blog\Helper\Config $helperConfig,
        array $data = []
    ) {

        parent::__construct($context, $data);
        $this->helperConfig = $helperConfig;
    }

    protected function _construct()
    {
        $this->setTemplate('Amasty_Blog::system/config/form/elements/colors.phtml');
        parent::_construct();
    }

    /**
     * @return array
     */
    private function getSchemesData()
    {
        $data = [];
        $schemeKeys = $this->helperConfig->getArrayFromPath('color_schemes');
        foreach ($schemeKeys as $key => $value) {
            if ($value && isset($value['data'])) {
                $data[$key] = $value['data'];
            }
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getSchemesDataJson()
    {
        return \Zend_Json::encode($this->getSchemesData());
    }

    /**
     * @return array
     */
    public function getSchemes()
    {
        $schemeKeys = $this->helperConfig->getArrayFromPath('color_schemes');
        $schemes['_select_'] = __("Select one and press Apply");
        foreach ($schemeKeys as $key => $value) {
            $schemes[$key] = __($value['label']);
        }

        return $schemes;
    }
}
