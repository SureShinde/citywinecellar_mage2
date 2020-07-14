<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Adminhtml\Location\Edit\Buttons;
/**
 * Class SaveAndContinue
 * @package Magestore\Webpos\Block\Adminhtml\Location\Edit\Buttons
 */
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
            'label' => __('Save and Continue Edit'),
            'class' => 'save',
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'saveAndContinueEdit'],
                ],
            ],
            'sort_order' => 30,
        ];
    }
}