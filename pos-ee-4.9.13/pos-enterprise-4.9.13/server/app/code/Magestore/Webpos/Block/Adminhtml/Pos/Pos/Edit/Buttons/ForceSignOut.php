<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Adminhtml\Pos\Pos\Edit\Buttons;

class ForceSignOut extends Generic {
    /**
     * @return array
     */
    public function getButtonData()
    {
        if (!$this->getPos() || !$this->getPos()->getId()) {
            return [];
        }

        if ($this->getPos() && !$this->getPos()->getStaffId()) {
            return [];
        }

        if(!$this->authorization->isAllowed('Magestore_Webpos::pos')) {
            return [];
        }

        $url = $this->getUrl('*/*/forceSignOut', ['id' => $this->getPos()->getId()]);

        return [
            'label' => __('Force Sign-out'),
            'class' => 'force_sign_out',
            'on_click' => sprintf("location.href = '%s';", $url),
            'sort_order' => 16
        ];
    }
}