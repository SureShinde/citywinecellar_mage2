<?php
/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Ui\DataProvider\Reports;

use Magestore\PosReports\Model\ResourceModel\Report\Grid\ByPaymentMethodCollectionFactory as CollectionFactory;
use Magestore\PosReports\Model\Source\Location as LocationSource;

/**
 * Class ByPaymentMethod
 *
 * Used to create By Payment Method
 */
class ByPaymentMethod extends AbstractDataProvider
{

    /**
     * ByPaymentMethod constructor.
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
                    'location_id' => LocationSource::ALL_LOCATION_ID,
                ]
            ]
        );
    }
}
