<?php
/**
 *  Copyright © Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Block\Adminhtml\Report;

use Magestore\ReportSuccess\Block\Adminhtml\Report\Panel\AbstractReportPanel;

/**
 * Class PosReportPanel
 *
 * Used to create pos report panel block
 */
class PosReportPanel extends AbstractReportPanel
{

    /**
     * Admin Resource Acl
     * */
    const ADMIN_RESOURCE = 'Magestore_PosReports::report_listing';

    /**
     * Panel Heading Title
     *
     * @var string
     * */
    protected $_panelHeadingTitle = 'POS Reports';
}
