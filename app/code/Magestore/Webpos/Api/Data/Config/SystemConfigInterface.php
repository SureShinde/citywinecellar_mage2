<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Config;

/**
 * @api
 */
/**
 * Interface ConfigInterface
 * @package Magestore\Webpos\Api\Data\Config
 */
interface SystemConfigInterface
{
    const PATH = 'path';
    const VALUE = 'value';

    /**
     * Get path
     *
     * @api
     * @return string
     */
    public function getPath();

    /**
     * Set path
     *
     * @api
     * @param string $path
     * @return SystemConfigInterface
     */
    public function setPath($path);
    /**
     * Get value
     *
     * @api
     * @return string
     */
    public function getValue();

    /**
     * Set value
     *
     * @api
     * @param string $value
     * @return SystemConfigInterface
     */
    public function setValue($value);
}
