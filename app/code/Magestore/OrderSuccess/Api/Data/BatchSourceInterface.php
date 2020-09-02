<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Api\Data;

/**
 * Interface BatchSourceInterface
 * @package Magestore\OrderSuccess\Api\Data
 */
interface BatchSourceInterface extends \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray();

    /**
     * @return array
     */
    public function getOptionArray();
}