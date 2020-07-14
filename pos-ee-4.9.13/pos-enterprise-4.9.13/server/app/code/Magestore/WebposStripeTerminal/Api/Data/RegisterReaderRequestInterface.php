<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposStripeTerminal\Api\Data;

/**
 * Interface RegisterReaderRequestInterface
 * @package Magestore\WebposStripeTerminal\Api\Data
 */
interface RegisterReaderRequestInterface
{
    const LABEL = 'label';
    const REGISTRATION_CODE = 'registration_code';

    /**
     * @return string|null
     */
    public function getLabel();

    /**
     * @param string|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\RegisterReaderRequestInterface
     */
    public function setLabel($value);

    /**
     * @return string|null
     */
    public function getRegistrationCode();

    /**
     * @param string|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\RegisterReaderRequestInterface
     */
    public function setRegistrationCode($value);
}