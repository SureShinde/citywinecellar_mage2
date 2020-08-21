<?php
/**
 *  Copyright © Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Block\Adminhtml\Report\Panel;

/**
 * Class InventoryReportPanel
 *
 * Used to create Inventory Report Panel
 */
class InventoryReportPanel extends AbstractReportPanel
{
    const ADMIN_RESOURCE = 'Magestore_ReportSuccess::inventory';

    /**
     * @var string
     */
    protected $_panelHeadingTitle = 'Inventory Reports';
}
