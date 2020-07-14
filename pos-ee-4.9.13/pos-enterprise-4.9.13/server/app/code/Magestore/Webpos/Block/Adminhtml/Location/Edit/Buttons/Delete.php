<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Adminhtml\Location\Edit\Buttons;

/**
 * Class Delete
 * @package Magestore\Webpos\Block\Adminhtml\Location\Edit\Buttons
 */
class Delete extends Generic {
    /**
     * @return array
     */
    public function getButtonData()
    {
        if (!$this->getLocation() || !$this->getLocation()->getId()) {
            return [];
        }

        if(!$this->authorization->isAllowed('Magestore_Webpos::location_delete')) {
            return [];
        }

        $url = $this->getUrl('*/*/delete', ['id' => $this->getLocation()->getId()]);

        return [
            'label' => __('Delete'),
            'class' => 'delete',
            'on_click' => sprintf("deleteConfirm(
                    'Are you sure you want to do this?', 
                    '%s'
                )", $url),
            'sort_order' => 20
        ];
    }
}