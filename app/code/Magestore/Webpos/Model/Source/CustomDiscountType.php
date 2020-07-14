<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Source;

/**
 * Class CustomDiscountType
 * @package Magestore\Webpos\Model\Source
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class CustomDiscountType implements \Magento\Framework\Option\ArrayInterface
{
    const TYPE_FIXED = "$";
    const TYPE_PERCENT = "%";

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        $options[] = array('value' => self::TYPE_FIXED, 'label' => __("Fixed"));
        $options[] = array('value' => self::TYPE_PERCENT, 'label' => __("Percent"));
        return $options;
    }

}