<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposStripeTerminal\Block\Adminhtml\Config;


class Setupguide extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'Magestore_WebposStripeTerminal::config/setupguide.phtml';
    public $testButtonId = 'webpos-stripe-terminal-test-api-btn';
    public $testResponseId = 'webpos-stripe-terminal-test-api-response';
    public $setupGuideClassName = 'stripe-terminal-installation-guide';

    /**
     * Get api test url
     * @return string
     */
    public function getTestApiUrl(){
        return $this->getUrl('webposstripeterminal/api/test');
    }

}