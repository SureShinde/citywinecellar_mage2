<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Block\Adminhtml\Staff\Staff\Edit\Buttons;
class Save extends Generic {
    /**
     * @return array
     */
    public function getButtonData()
    {
        if(!$this->authorization->isAllowed('Magestore_Appadmin::manageStaffs')) {
            return [];
        }

        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 40,
        ];
    }
}