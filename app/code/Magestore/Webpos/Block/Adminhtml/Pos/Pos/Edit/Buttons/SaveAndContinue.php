<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Adminhtml\Pos\Pos\Edit\Buttons;

class SaveAndContinue extends Generic {
    /**
     * @return array
     */
    public function getButtonData()
    {
        if(!$this->authorization->isAllowed('Magestore_Webpos::pos')) {
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
                                'targetName' => 'webpos_pos_form.webpos_pos_form',
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