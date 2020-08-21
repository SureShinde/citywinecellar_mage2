<?php
/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Model\Reports;

/**
 * Interface PosReportInterface
 *
 * Used to create PosReportInterface
 */
interface PosReportInterface
{
    /**
     * Get report id
     *
     * @return string
     */
    public function getReportId();

    /**
     * Get report title
     *
     * @return string
     */
    public function getReportTitle();

    /**
     * Is export raw value
     *
     * @return bool
     */
    public function isExportRawValue();

    /**
     * Get export csv file name
     *
     * @return string
     */
    public function getExportCsvFileName();
}
