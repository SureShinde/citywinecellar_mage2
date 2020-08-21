<?php
/**
 * Copyright © Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Block\Adminhtml\Report;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class ShowReportButton
 *
 * Used to create Show Report Button
 */
class ShowReportButton implements ButtonProviderInterface
{
    /**
     * Get Button Data
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Show Report'),
            'class' => 'primary',
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'index = report_data',
                                'actionName' => 'showReport'
                            ]
                        ]
                    ]
                ]
            ],
            'on_click' => '',
            'sort_order' => 40
        ];
    }
}
