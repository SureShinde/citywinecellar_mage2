<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Model\Source\Adminhtml\Product;

/**
 * Product type model
 */
class Type implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $options = [];
        $options[] = ['value' => 'simple', 'label' => __('Simple Product')];
        $options[] = ['value' => 'virtual', 'label' => __('Virtual Product')];
        $options[] = ['value' => 'downloadable', 'label' => __('Downloadable Product')];
        $options[] = ['value' => 'giftvoucher', 'label' => __('Gift Card Product')];
        return $options;
    }
}
