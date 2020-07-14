<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposTracking\Cron;

/**
 * Class SyncingTrackingData
 *
 * @package Magestore\WebposTracking\Cron
 */
class SyncingTrackingData
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;
    /**
     * @var \Magestore\WebposTracking\Helper\Data
     */
    protected $helper;
    /**
     * @var \Magestore\WebposTracking\Model\Service\TrackingService
     */
    protected $trackingService;

    /**
     * SyncingTrackingData constructor.
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magestore\WebposTracking\Helper\Data $helper
     * @param \Magestore\WebposTracking\Model\Service\TrackingService $trackingService
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magestore\WebposTracking\Helper\Data $helper,
        \Magestore\WebposTracking\Model\Service\TrackingService $trackingService
    ) {
        $this->localeDate = $localeDate;
        $this->helper = $helper;
        $this->trackingService = $trackingService;
    }

    /**
     * Update BI data when cron run
     */
    public function execute()
    {
        if (!$this->trackingService->isTrackingEnable()) {
            return;
        }

        $this->trackingService->sendTrackingApi();
    }
}
