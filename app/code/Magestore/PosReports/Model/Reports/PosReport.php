<?php
/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Model\Reports;

/**
 * Class PosReport
 *
 * Used to create Pos Report
 */
class PosReport implements PosReportInterface
{
    /**
     * @var string
     */
    protected $reportId = "";

    /**
     * @var string
     */
    protected $reportTitle = "";

    /**
     * @var bool
     */
    protected $exportRawValue = false;

    /**
     * @var string
     */
    protected $exportCsvFileName = "export.csv";

    /**
     * PosReport constructor.
     *
     * @param int $id
     * @param string $title
     * @param bool $exportRawValue
     * @param string $exportCsvFileName
     */
    public function __construct(
        $id,
        $title,
        $exportRawValue = false,
        $exportCsvFileName = "export.csv"
    ) {
        $this->reportId = $id;
        $this->reportTitle = $title;
        $this->exportRawValue = $exportRawValue;
        $this->exportCsvFileName = $exportCsvFileName;
    }

    /**
     * Get report id
     *
     * @return string
     */
    public function getReportId()
    {
        return $this->reportId;
    }

    /**
     * Get report title
     *
     * @return string
     */
    public function getReportTitle()
    {
        return __($this->reportTitle);
    }

    /**
     * Is export raw value
     *
     * @return bool
     */
    public function isExportRawValue()
    {
        return boolval($this->exportRawValue);
    }

    /**
     * Get export csv file name
     *
     * @return string
     */
    public function getExportCsvFileName()
    {
        return $this->exportCsvFileName;
    }
}
