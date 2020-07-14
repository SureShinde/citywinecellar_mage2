<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\BarcodeSuccess\Model\Locator;

use Magestore\BarcodeSuccess\Api\Data\HistoryInterface;

/**
 * Interface LocatorInterface
 */
interface LocatorInterface
{
    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function add($key, $value);

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function get($key);

    /**
     * @param $key
     * @return mixed
     */
    public function remove($key);

}
