<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Ui\Component\Listing\Columns\BackOrder;

/**
 * Class Actions
 * @package Magestore\OrderSuccess\Ui\Component\Listing\Columns\BackOrder
 */
class Actions extends \Magestore\OrderSuccess\Ui\Component\Listing\Columns\Actions
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var string
     */
    protected $actionKey = 'backorder';
}
