<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Adminhtml\Location\Edit\Buttons;

/**
 * Class Save
 * @package Magestore\Webpos\Block\Adminhtml\Location\Edit\Buttons
 */
class Save extends Generic {
    /**
     * @return array
     */
    public function getButtonData()
    {
        if(!$this->authorization->isAllowed('Magestore_Webpos::location_save')) {
            return [];
        }

        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'webpos_location_form.webpos_location_form',
                                'actionName' => 'save',
                                'params' => [
                                    true
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'sort_order' => 40,
        ];
    }
}