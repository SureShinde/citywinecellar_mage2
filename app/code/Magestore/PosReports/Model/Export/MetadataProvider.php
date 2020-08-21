<?php
/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Model\Export;

use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magestore\PosReports\Model\Reports\PosReportInterface;
use Magestore\PosReports\Model\Source\Location as PosReportLocationSource;
use Magestore\PosReports\Model\Filter as ReportFilter;

/**
 * Class MetadataProvider
 *
 * Used to create Metadata Provider
 */
class MetadataProvider extends \Magento\Ui\Model\Export\MetadataProvider
{

    /**
     * @var ReportFilter
     */
    protected $reportFilter;

    /**
     * @var PosReportLocationFilterSource
     */
    protected $posReportLocationSource;

    /**
     * MetadataProvider constructor.
     *
     * @param Filter $filter
     * @param TimezoneInterface $localeDate
     * @param ResolverInterface $localeResolver
     * @param ReportFilter $reportFilter
     * @param PosReportLocationSource $posReportLocationSource
     * @param string $dateFormat
     * @param array $data
     */
    public function __construct(
        Filter $filter,
        TimezoneInterface $localeDate,
        ResolverInterface $localeResolver,
        ReportFilter $reportFilter,
        PosReportLocationSource $posReportLocationSource,
        $dateFormat = 'M j, Y h:i:s A',
        array $data = []
    ) {
        parent::__construct($filter, $localeDate, $localeResolver, $dateFormat, $data);
        $this->reportFilter = $reportFilter;
        $this->posReportLocationSource = $posReportLocationSource;
    }

    /**
     * Get fields component
     *
     * @param UiComponentInterface $component
     * @return array
     */
    public function getFieldsComponent(UiComponentInterface $component)
    {
        $row = [];
        foreach ($this->getColumns($component) as $column) {
            $row[] = $column;
        }
        return $row;
    }

    /**
     * Get field options
     *
     * @param UiComponentInterface $field
     * @param mixed $output
     * @return mixed
     */
    public function getFieldOptions(UiComponentInterface $field, &$output)
    {

        $fieldOptions = $field->getData('config/options');
        if ($fieldOptions && is_array($fieldOptions) && !empty($fieldOptions)) {
            $fieldOptionsValues = [];
            foreach ($fieldOptions as $fieldOption) {
                if (!is_array($fieldOption['value'])) {
                    $fieldOptionsValues[$fieldOption['value']] = $fieldOption['label'];
                } else {
                    $this->getComplexLabel(
                        $fieldOption['value'],
                        $fieldOption['label'],
                        $fieldOptionsValues
                    );
                }
            }
            $output[$field->getName()] = $fieldOptionsValues;
        }
        return $output;
    }

    /**
     * Is Pos report
     *
     * @param UiComponentInterface $component
     * @return bool
     */
    public function isPosReport(UiComponentInterface $component)
    {
        return $component instanceof \Magestore\PosReports\Ui\Component\Listing;
    }

    /**
     * Is export raw value
     *
     * @param UiComponentInterface $component
     * @return bool
     */
    public function isExportRawValue(UiComponentInterface $component)
    {
        if ($this->isPosReport($component)) {
            /**
             * @var PosReportInterface $report
             */
            $report = $component->getReport();
            return $report->isExportRawValue();
        }
        return true;
    }

    /**
     * Get report headers
     *
     * @param UiComponentInterface $component
     * @return array
     */
    public function getReportHeaders(UiComponentInterface $component)
    {
        $rows = [];
        if ($this->isPosReport($component)) {
            /**
             * @var PosReportInterface $report
             */
            $report = $component->getReport();

            $rows[] = $this->generateCustomLineData($component, $report->getReportTitle());

            $currentDate = $this->localeDate->date(
                null,
                $this->locale,
                true
            );

            $rows[] = $this->generateCustomLineData($component, $currentDate->format('d/m/y, h:i A'));

            $locations = $this->posReportLocationSource->getOptionArray();
            $locationId = $this->reportFilter->getFilter('location_id');
            if (isset($locations[$locationId])) {
                $rows[] = $this->generateCustomLineData($component, $locations[$locationId]);
            }

            $rows[] = $this->generateCustomLineData($component);

        }
        return $rows;
    }

    /**
     * Generate custom line data
     *
     * @param UiComponentInterface $component
     * @param string $data
     * @return array
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function generateCustomLineData(UiComponentInterface $component, $data = "")
    {
        $row = [];
        $index = 0;
        foreach ($this->getColumns($component) as $column) {
            $row[] = ($index === 0) ? $data : "";
            $index++;
        }
        return $row;
    }
}
