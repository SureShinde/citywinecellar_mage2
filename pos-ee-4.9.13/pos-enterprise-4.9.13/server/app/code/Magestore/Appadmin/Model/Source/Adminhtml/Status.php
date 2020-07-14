<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Appadmin\Model\Source\Adminhtml;

/**
 * class \Magestore\Appadmin\Model\Source\Adminhtml\Status
 * 
 * Status source model
 * Methods:
 *  getOptionArray
 *  toOptionArray
 * 
 * @category    Magestore
 * @package     Magestore_Appadmin
 * @module      Appadmin
 * @author      Magestore Developer
 */
class Status implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    const STATUS_ENABLED = 1;
    /**
     *
     */
    const STATUS_DISABLED = 2;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('Enabled'), 'value' => self::STATUS_ENABLED],
            ['label' => __('Disabled'), 'value' => self::STATUS_DISABLED],
        ];
    }

    /**
     * @return array
     */
    public static function getOptionArray()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

}