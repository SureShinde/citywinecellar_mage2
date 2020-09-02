<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Api\Data;

/**
 * Interface TagSourceInterface
 * @package Magestore\OrderSuccess\Api\Data
 */
interface TagSourceInterface extends \Magento\Framework\Option\ArrayInterface
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