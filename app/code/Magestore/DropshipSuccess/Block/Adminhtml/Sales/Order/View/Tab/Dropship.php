<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Block\Adminhtml\Sales\Order\View\Tab;

/**
 * Class Dropship
 * @package Magestore\DropshipSuccess\Block\Adminhtml\Sales\Order\View\Tab
 */
class Dropship extends \Magento\Framework\View\Element\Text\ListText implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Dropship');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Dropship');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
