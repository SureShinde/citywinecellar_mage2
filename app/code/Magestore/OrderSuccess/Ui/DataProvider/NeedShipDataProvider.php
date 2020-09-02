<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Ui\DataProvider;

/**
 * Class NeedShipDataProvider
 * @package Magestore\OrderSuccess\Ui\DataProvider
 */
class NeedShipDataProvider extends \Magestore\OrderSuccess\Ui\DataProvider\OrderDataProvider
{
    /**
     * {@inheritdoc}
     */
    public function getOrderCollection()
    {
        /** @var Collection $collection */
        $collection= $this->context->getNeedShipCollectionFactory()->create();
        return $collection;
    }

    public function getData()
    {
        $data = parent::getData();
        $data['totalRecords'] = count($data['items']);
        return $data;
    }
}
