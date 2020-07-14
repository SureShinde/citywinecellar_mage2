<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */

namespace Amasty\Xlanding\Plugin;

class ProductListToolbar
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    public function __construct(
        \Magento\Framework\Registry $registry
    ) {
        $this->registry = $registry;
    }

    /**
     * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $toolbar
     * @param $sortField
     * @return array
     */
    public function beforeSetDefaultOrder(\Magento\Catalog\Block\Product\ProductList\Toolbar $toolbar, $sortField)
    {
        $page = $this->registry->registry('amasty_xlanding_page');

        if ($page && $page->getDefaultSortBy()) {
            $sortField = $page->getDefaultSortBy();
        }

        return [$sortField];
    }
}
