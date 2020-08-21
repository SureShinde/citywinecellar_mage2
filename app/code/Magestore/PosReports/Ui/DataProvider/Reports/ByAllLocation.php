<?php
/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Ui\DataProvider\Reports;

use Magestore\PosReports\Model\ResourceModel\Report\Grid\ByAllLocationCollectionFactory as CollectionFactory;

/**
 * Class ByAllLocation
 *
 * Used to create By All Location
 */
class ByAllLocation extends AbstractDataProvider
{

    /**
     * ByAllLocation constructor.
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
     * Get data
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
                    'time_range' => \Magestore\PosReports\Model\Source\TimeRange::TODAY
                ]
            ]
        );
    }
}
