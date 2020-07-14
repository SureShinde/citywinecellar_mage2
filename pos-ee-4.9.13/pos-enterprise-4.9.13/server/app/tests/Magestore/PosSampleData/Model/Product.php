<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosSampleData\Model;
/**
 * Class Currency
 * @package Magestore\Webpos\Model\Config\Data
 */
class Product extends \Magento\Framework\DataObject implements \Magestore\PosSampleData\Api\Data\ProductInterface
{
    /**
     * @inheritdoc
     */
    public function getSku(){
        return $this->getData(self::SKU);
    }

    /**
     * @inheritdoc
     */
    public function setSku($sku){
        $this->setData(self::SKU, $sku);
        return $this;
    }
}