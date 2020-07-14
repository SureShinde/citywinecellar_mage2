<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Ui\Component;


class Filters extends \Magento\Ui\Component\Filters
{
    /**
     * Maps filter declaration to type
     *
     * @var array
     */
    protected $filterMap = [
        'text' => 'filterInput',
        'textRange' => 'filterRange',
        'select' => 'filterSelect',
        'dateRange' => 'filterDate',
        'filterTag' => 'filterSelect',
        'filterBatch' => 'filterSelect',
    ];
}
