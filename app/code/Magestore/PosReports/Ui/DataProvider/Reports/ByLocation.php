<?php
/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Ui\DataProvider\Reports;

use Magestore\PosReports\Model\ResourceModel\Report\Grid\ByLocationCollectionFactory as CollectionFactory;
use Magestore\Webpos\Model\Source\Adminhtml\LocationFactory as LocationSourceFactory;

/**
 * Class ByLocation
 *
 * Used to create By All Location
 */
class ByLocation extends AbstractDataProvider
{

    /**
     * @var LocationSourceFactory
     */
    protected $locationSourceFactory;

    /**
     * @var int
     */
    protected $defaultLocationId = 0;

    /**
     * ByLocation constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param LocationSourceFactory $locationSourceFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        LocationSourceFactory $locationSourceFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->locationSourceFactory = $locationSourceFactory;
    }

    /**
     * Get default location id
     *
     * @return int|mixed
     */
    public function getDefaultLocationId()
    {
        if (!$this->defaultLocationId) {
            $locationSource = $this->locationSourceFactory->create();
            $options = $locationSource->getOptionArray();
            if (!empty($options)) {
                reset($options);
                $this->defaultLocationId = key($options);
            }
        }
        return $this->defaultLocationId;
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
                    'location_id' => $this->getDefaultLocationId(),
                    'period_type' => \Magestore\PosReports\Model\Source\PeriodType::DAY,
                    'time_range' => \Magestore\PosReports\Model\Source\TimeRange::TODAY
                ]
            ]
        );
    }
}
