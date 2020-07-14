<?php
/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposTracking\Ui\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\Api\Filter;

/**
 * Data Provider for the Admin usage UI component.
 *
 * Class MagestoreSupportNotificationDataProvider
 * @package Magestore\WebposTracking\Ui\DataProvider
 */
class MagestoreSupportNotificationDataProvider extends AbstractDataProvider
{
    /**
     * @inheritdoc
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function addFilter(Filter $filter)
    {
        return null;
    }
}
