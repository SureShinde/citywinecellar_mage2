<?php
/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Ui\Component;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magestore\PosReports\Model\Reports\PosReportInterface;

/**
 * Class Listing
 *
 * Used to create listing
 */
class Listing extends \Magento\Ui\Component\Listing
{
    /**
     * @var PosReportInterface
     */
    protected $posReport;

    /**
     * Listing constructor.
     *
     * @param ContextInterface $context
     * @param PosReportInterface $posReport
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        PosReportInterface $posReport,
        array $components = [],
        array $data = []
    ) {
        $this->posReport = $posReport;
        parent::__construct($context, $components, $data);
    }

    /**
     * Get report
     *
     * @return PosReportInterface
     */
    public function getReport()
    {
        return $this->posReport;
    }
}
