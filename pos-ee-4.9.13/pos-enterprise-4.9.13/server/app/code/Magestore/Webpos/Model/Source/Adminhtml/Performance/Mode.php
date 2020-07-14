<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Source\Adminhtml\Performance;

class Mode implements \Magento\Framework\Data\OptionSourceInterface {

    const ONLINE_MODE = 0;
    const OFFLINE_MODE = 1;

    /**
     * @var array
     */
    protected $_options;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->_options = [
            ['value' => self::ONLINE_MODE, 'label' => __('Online')],
            ['value' => self::OFFLINE_MODE, 'label' => __('Offline')],
        ];
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_options;
    }
}