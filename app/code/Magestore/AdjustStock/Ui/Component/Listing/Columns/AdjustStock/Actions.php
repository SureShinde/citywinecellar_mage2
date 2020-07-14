<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Ui\Component\Listing\Columns\AdjustStock;

/**
 * Class Actions.
 *
 * @category Magestore
 * @package  Magestore_AdjustStock
 * @module   AdjustStock
 * @author   Magestore Developer
 */
class Actions extends \Magestore\AdjustStock\Ui\Component\Listing\Columns\Actions
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    protected $_editUrl = 'adjuststock/adjuststock/edit';
}
