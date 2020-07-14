<?php
/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magestore\WebposTracking\Model\Condition;

use Magestore\WebposTracking\Helper\Data;
use Magestore\WebposTracking\Model\Service\TrackingService;
use Magento\Framework\View\Layout\Condition\VisibilityConditionInterface;

/**
 * Dynamic validator for UI admin analytics notification, control UI component visibility.
 */
class CanViewNotification implements VisibilityConditionInterface
{
    /**
     * Unique condition name.
     *
     * @var string
     */
    private static $conditionName = 'can_view_magestore_support_notification';

    /**
     * @var Data
     */
    private $helper;

    /**
     * CanViewNotification constructor.
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Validate if notification popup can be shown and set the notification flag
     *
     * @param array $arguments Attributes from element node.
     * @inheritdoc
     */
    public function isVisible(array $arguments)
    {
        if ($this->helper->getConfig(TrackingService::IS_SHOW_NOTIFICATION_PATH) == 0
            || $this->helper->getConfig(TrackingService::ENABLE_SUPPORT)) {
            return false;
        }

        return true;
    }

    /**
     * Get condition name
     *
     * @return string
     */
    public function getName()
    {
        return self::$conditionName;
    }
}
