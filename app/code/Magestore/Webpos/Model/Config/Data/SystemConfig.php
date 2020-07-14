<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Config\Data;
/**
 * Class SystemConfig
 * @package Magestore\Webpos\Model\Config\Data
 */
class SystemConfig extends \Magento\Framework\DataObject implements \Magestore\Webpos\Api\Data\Config\SystemConfigInterface
{

    /**
     * Get path
     *
     * @api
     * @return string
     */
    public function getPath() {
        return $this->getData(self::PATH);
    }

    /**
     * Set path
     *
     * @api
     * @param string $path
     * @return $this
     */
    public function setPath($path) {
        return $this->setData(self::PATH, $path);
    }
    /**
     * Get value
     *
     * @api
     * @return string
     */
    public function getValue() {
        return $this->getData(self::VALUE);
    }

    /**
     * Set value
     *
     * @api
     * @param string $value
     * @return $this
     */
    public function setValue($value) {
        return $this->setData(self::VALUE, $value);
    }
}