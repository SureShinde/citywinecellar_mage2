<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Plugin\Catalog\Product;

/**
 * Class Type
 * @package Magestore\Webpos\Plugin\Catalog\Product
 */
class Type extends \Magento\Catalog\Model\Product\Type
{
    /**
     * @param \Magento\Catalog\Model\Product\Type $subject
     * @param $result
     * @return mixed
     */
    public function afterGetOptionArray(\Magento\Catalog\Model\Product\Type $subject, $result)
    {
        if (isset($result[\Magestore\Webpos\Helper\Product\CustomSale::TYPE])) {
            unset($result[\Magestore\Webpos\Helper\Product\CustomSale::TYPE]);
        }
        return $result;
    }
}