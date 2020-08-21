<?php
/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Ui\DataProvider\Reports;

use Magestore\PosReports\Model\ResourceModel\Report\Grid\ByStaffCollectionFactory as CollectionFactory;
use Magestore\PosReports\Model\Source\Location as LocationSource;

/**
 * Class ByStaff
 *
 * Used to create By Staff
 */
class ByStaff extends AbstractDataProvider
{
    /**
     * ByStaff constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }

    /**
     * Get Data
     *
     * @return array
     */
    public function getData()
    {
        $data = parent::getData();
        return array_merge_recursive(
            $data,
            [
                "" => [
                    'date_used' => \Magestore\PosReports\Model\Source\DateUsed::CREATED_AT,
                    'location_id' => LocationSource::ALL_LOCATION_ID,
                    'time_range' => \Magestore\PosReports\Model\Source\TimeRange::TODAY
                ]
            ]
        );
    }
}
