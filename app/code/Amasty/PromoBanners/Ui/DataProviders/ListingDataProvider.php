<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PromoBanners
 */


namespace Amasty\PromoBanners\Ui\DataProviders;

use Amasty\PromoBanners\Model\ResourceModel\Rule\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

class ListingDataProvider extends AbstractDataProvider
{
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

    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        $collection = $this->getCollection();

        if ($filter->getField() == 'stores') {
            $collection->addStoreFilter($filter->getValue());
        } else {
            parent::addFilter($filter);
        }

        return $collection;
    }
}
