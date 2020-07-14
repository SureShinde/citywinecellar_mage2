<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Model\Source\Adminhtml\Product;

/**
 * Class Source
 * @package Magestore\TransferStock\Model\Source\Adminhtml
 */
class Type implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $options[] = array('value' => 'simple', 'label' => __('Simple Product'));
        $options[] = array('value' => 'virtual', 'label' => __('Virtual Product'));
        $options[] = array('value' => 'downloadable', 'label' => __('Downloadable Product'));
        $options[] = array('value' => 'giftvoucher', 'label' => __('Gift Card Product'));
        return $options;
    }

}
