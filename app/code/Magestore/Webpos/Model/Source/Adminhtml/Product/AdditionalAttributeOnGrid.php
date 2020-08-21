<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Source\Adminhtml\Product;

/**
 * Source option AdditionalAttributeOnGrid
 */
class AdditionalAttributeOnGrid implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Add needed attributes to show on catalog product grid pos
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'sku', 'label' => 'SKU'],
        ];
    }
}
