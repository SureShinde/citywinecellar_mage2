<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Source\Adminhtml\Config;

/**
 * class \Magestore\Webpos\Model\Source\Adminhtml\CustomerGroup
 *
 * Web POS CustomerGroup source model
 * Use to get magento customer group
 * Methods:
 *  filterCustomerCollection
 *  getAllowCustomerGroups
 *  getOptionArray
 *  toOptionArray
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
/**
 * Class CustomerGroup
 * @package Magestore\Webpos\Model\Source\Adminhtml
 */
class FptTaxType implements \Magento\Framework\Option\ArrayInterface {
    /**
     * @var array
     */
    protected $_options;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->_options = [
            ['value' => 0, 'label' => __('Excluding FPT')],
            ['value' => 1, 'label' => __('Including FPT')],
        ];
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_options;
    }
}