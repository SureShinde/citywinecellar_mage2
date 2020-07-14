<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\PurchaseOrderSuccess\Model\System\Config;

/**
 * @api
 * @since 100.0.2
 */
class ProductSource implements \Magento\Framework\Option\ArrayInterface
{
    const TYPE_SUPPLIER = 1;
    const TYPE_STORE = 2;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::TYPE_SUPPLIER, 'label' => __('Supplier')],
            ['value' => self::TYPE_STORE, 'label' => __('All stores')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [self::TYPE_SUPPLIER => __('Supplier'), self::TYPE_STORE => __('All stores')];
    }
}
