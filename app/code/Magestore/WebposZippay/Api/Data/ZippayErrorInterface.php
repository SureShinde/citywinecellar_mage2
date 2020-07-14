<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposZippay\Api\Data;

/**
 * Interface ZippayServiceInterface
 * @package Magestore\WebposZippay\Api\Data
 */
interface ZippayErrorInterface
{
    const CODE = 'code';
    const MESSAGE = 'message';
    const ITEMS = 'items';


    /**
     * @return string | float
     */
    public function getCode();

    /**
     * @param $code
     * @return ZippayErrorInterface | null
     */
    public function setCode($code);

    /**
     * @return string|float|null
     */
    public function getMessage();

    /**
     * @param string|float|null $message
     * @return ZippayErrorInterface
     */
    public function setMessage($message);

    /**
     * @return \Magestore\WebposZippay\Api\Data\ZippayErrorFieldInterface[] | null
     */
    public function getItems();

    /**
     * @param $items
     * @return ZippayErrorInterface
     */
    public function setItems($items);
}
