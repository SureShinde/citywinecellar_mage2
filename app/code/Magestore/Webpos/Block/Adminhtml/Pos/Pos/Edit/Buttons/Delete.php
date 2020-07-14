<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Adminhtml\Pos\Pos\Edit\Buttons;

class Delete extends Generic {
    /**
     * @return array
     */
    public function getButtonData()
    {
        if (!$this->getPos() || !$this->getPos()->getId()) {
            return [];
        }

        if(!$this->authorization->isAllowed('Magestore_Webpos::pos')) {
            return [];
        }

        $url = $this->getUrl('*/*/delete', ['id' => $this->getPos()->getId()]);

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