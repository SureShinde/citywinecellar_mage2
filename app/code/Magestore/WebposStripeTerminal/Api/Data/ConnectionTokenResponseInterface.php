<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposStripeTerminal\Api\Data;

/**
 * Interface ConnectionTokenResponseInterface
 * @package Magestore\WebposStripeTerminal\Api\Data
 */
interface ConnectionTokenResponseInterface
{
    const OBJECT = 'object';
    const SECRET = 'secret';

    /**
     * @return string|null
     */
    public function getObject();

    /**
     * @param string|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\ConnectionTokenResponseInterface
     */
    public function setObject($value);

    /**
     * @return string|null
     */
    public function getSecret();

    /**
     * @param string|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\ConnectionTokenResponseInterface
     */
    public function setSecret($value);
}