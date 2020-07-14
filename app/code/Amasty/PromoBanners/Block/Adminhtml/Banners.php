<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PromoBanners
 */


namespace Amasty\PromoBanners\Block\Adminhtml;

class Banners extends \Magento\Backend\Block\Widget\Grid\Container
{
    public function _construct()
    {
        $this->_controller = 'adminhtml_banners';
        $this->_headerText = __('Banners');
        $this->_blockGroup = 'Amasty_PromoBanners';
        $this->_addButtonLabel = 'Add Banner';
        parent::_construct();
    }
}
