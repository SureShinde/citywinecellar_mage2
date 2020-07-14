<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Block\Supplier;

use \Magento\Framework\App\ObjectManager;

/**
 * Sales order history block
 */
class Product extends AbstractSupplier
{
    /**
     * @var string
     */
    protected $_template = 'supplier/product.phtml';

    /**
     * @var
     */
    protected $products;

    /**
     * @return bool|\Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product\Collection
     */
    public function getProducts()
    {
        if (!($supplierId = $this->supplierSession->getSupplierId())) {
            return false;
        }
        if (!$this->products) {
            /** @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product products */
            $this->products = $this->productService->getProductsBySupplierId($supplierId);
        }
        return $this->products;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getProducts()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'supplier.product.pager'
            )->setCollection(
                $this->getProducts()
            );
            $this->setChild('pager', $pager);
            $this->getProducts()->load();
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
