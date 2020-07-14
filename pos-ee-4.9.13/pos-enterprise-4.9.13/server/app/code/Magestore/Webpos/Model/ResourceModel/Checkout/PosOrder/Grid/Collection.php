<?php

/**
 * Copyright © 2019 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Webpos\Model\ResourceModel\Checkout\PosOrder\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

/**
 * Class \Magestore\Webpos\Model\ResourceModel\Denomination\PosOrder\Grid\Collection
 */
class Collection extends SearchResult
{
    /**
     * @inheritdoc
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addFieldToFilter(
            'status',
            [
                'nin' => [\Magestore\Webpos\Model\Checkout\PosOrder::STATUS_COMPLETED]
            ]
        );
        return $this;
    }
}
