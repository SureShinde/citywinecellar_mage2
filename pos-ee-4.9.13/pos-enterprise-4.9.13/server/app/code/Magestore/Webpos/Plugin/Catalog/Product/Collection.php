<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Plugin\Catalog\Product;

/**
 * Class Collection
 * @package Magestore\Webpos\Plugin\Catalog\Product
 */
class Collection
{
    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $subject
     * @param bool $printQuery
     * @param bool $logQuery
     * @return array
     */
    public function beforeLoad(\Magento\Catalog\Model\ResourceModel\Product\Collection $subject, $printQuery = false, $logQuery = false)
    {
        $subject->addFieldToFilter('type_id', ['nin' => [\Magestore\Webpos\Helper\Product\CustomSale::TYPE]]);
        return [$printQuery, $logQuery];
    }
}