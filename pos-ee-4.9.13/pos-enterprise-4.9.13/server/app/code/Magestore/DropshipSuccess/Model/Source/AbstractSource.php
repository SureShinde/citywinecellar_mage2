<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class AbstractSource
 * @package Magestore\DropshipSuccess\Model\Source
 */

abstract class AbstractSource implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [];
    }
}
