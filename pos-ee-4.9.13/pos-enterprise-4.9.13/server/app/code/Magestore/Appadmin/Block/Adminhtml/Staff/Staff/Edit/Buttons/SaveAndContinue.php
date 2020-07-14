<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Block\Adminhtml\Staff\Staff\Edit\Buttons;

class SaveAndContinue extends Generic {
    /**
     * @return array
     */
    public function getButtonData()
    {
        if(!$this->authorization->isAllowed('Magestore_Appadmin::manageStaffs')) {
            return [];
        }

        return [
            'id_hard' => 'save_and_continue',
            'label' => __('Save and Continue Edit'),
            'on_click' => '',
            'sort_order' => 30,
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'appadmin_staff_form.appadmin_staff_form',
                                'actionName' => 'save',
                                'params' => [
                                    true,
                                    [
                                        'back' => 'edit'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];
    }
}