<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Source\Adminhtml\Product;

/**
 * class \Magestore\Webpos\Model\Source\Adminhtml\Product\AdditionalAttributeOnGrid
 * 
 * AdditionalAttributeOnGrid source model
 * Methods:
 *  toOptionArray
 * 
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class AdditionalAttributeOnGrid implements \Magento\Framework\Option\ArrayInterface
{
    /**
     *
     *  Add needed attributes to show on catalog product grid pos
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'sku', 'label' => 'SKU'),
        );
    }

}
