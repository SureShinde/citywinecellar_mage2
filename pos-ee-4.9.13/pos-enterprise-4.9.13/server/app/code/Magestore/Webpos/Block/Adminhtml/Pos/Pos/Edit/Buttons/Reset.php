<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Adminhtml\Pos\Pos\Edit\Buttons;

class Reset extends Generic {
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Reset'),
            'on_click' => sprintf("location.href = '%s';", $this->getUrl('*/*/*')),
            'sort_order' => 15
        ];
    }
}