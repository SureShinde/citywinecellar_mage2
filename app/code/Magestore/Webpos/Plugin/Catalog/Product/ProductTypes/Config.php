<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Plugin\Catalog\Product\ProductTypes;

/**
 * Class Config
 * @package Magestore\Webpos\Plugin\Catalog\Product\ProductTypes
 */
class Config extends \Magento\Catalog\Model\ProductTypes\Config
{
    /**
     * @param \Magento\Catalog\Model\ProductTypes\Config $subject
     * @param $result
     * @return mixed
     */
    public function afterGetAll(\Magento\Catalog\Model\ProductTypes\Config $subject, $result)
    {
        if (isset($result[\Magestore\Webpos\Helper\Product\CustomSale::TYPE])) {
            unset($result[\Magestore\Webpos\Helper\Product\CustomSale::TYPE]);
        }
        return $result;
    }
}