<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposTracking\Controller\Adminhtml\Config;

/**
 * Class EnableTrackingUsage
 *
 * @package Magestore\WebposTracking\Controller\Adminhtml\Config
 * @SuppressWarnings(PHPMD.AllPurposeAction)
 */
class EnableTrackingUsage extends AbstractConfig
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        $this->helper->setConfig(\Magestore\WebposTracking\Model\Service\TrackingService::ENABLE_SUPPORT, 1);
        parent::execute();
    }
}
