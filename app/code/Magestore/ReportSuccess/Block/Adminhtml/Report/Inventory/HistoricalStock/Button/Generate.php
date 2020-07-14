<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Block\Adminhtml\Report\Inventory\HistoricalStock\Button;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
/**
 * Class Generate
 * @package Magestore\ReportSuccess\Block\Adminhtml\Report\Inventory\HistoricalStock\Button
 */
class Generate implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Generate Report'),
            'class' => 'primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'open']]
            ],
            'sort_order' => 90,
            'on_click' => "jQuery('#popup-generate').modal('openModal')"
        ];
    }
}
