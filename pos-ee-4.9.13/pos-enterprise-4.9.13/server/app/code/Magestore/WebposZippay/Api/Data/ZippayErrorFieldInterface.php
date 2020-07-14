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
interface ZippayErrorFieldInterface
{
    const FIELD = 'field';
    const MESSAGE = 'message';


    /**
     * @return string | float
     */
    public function getField();

    /**
     * @param $field
     * @return ZippayErrorFieldInterface
     */
    public function setField($field);

    /**
     * @return string | float
     */
    public function getMessage();

    /**
     * @param $message
     * @return ZippayErrorFieldInterface
     */
    public function setMessage($message);
}
