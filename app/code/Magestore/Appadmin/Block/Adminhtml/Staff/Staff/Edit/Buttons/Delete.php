<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Block\Adminhtml\Staff\Staff\Edit\Buttons;

class Delete extends Generic {
    /**
     * @return array
     */
    public function getButtonData()
    {
        if (!$this->getStaff() || !$this->getStaff()->getId()) {
            return [];
        }

        if(!$this->authorization->isAllowed('Magestore_Appadmin::manageStaffs')) {
            return [];
        }

        $url = $this->getUrl('*/*/delete', ['id' => $this->getStaff()->getId()]);

        return [
            'label' => __('Delete'),
            'class' => 'delete',
            'on_click' => sprintf("deleteConfirm(
                    'Are you sure you want to delete this staff?', 
                    '%s'
                )", $url),
            'sort_order' => 20
        ];
    }
}