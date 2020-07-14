<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Ogrid
 */

namespace Magestore\PurchaseOrderSuccess\Ui\Component\Listing\Columns\PurchaseOrder;

use Magento\Framework\View\Element\UiComponentInterface;

class AbstractColumns extends \Magento\Ui\Component\Listing\Columns
{

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\Config\ProductConfig
     */
    protected $productConfig;

    protected $listColumnsSupplier = ['cost', 'product_supplier_sku'];

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magestore\PurchaseOrderSuccess\Service\Config\ProductConfig $productConfig,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->productConfig = $productConfig;
    }

    public function prepare()
    {
        $ret = parent::prepare();

        $this->_prepareColumns();
        return $ret;
    }

    protected function _prepareColumns()
    {
        foreach ($this->components as $id => $column) {
            if ($column instanceof \Magento\Ui\Component\Listing\Columns\Column) {
                if(!$this->checkProductSource() && in_array($id, $this->listColumnsSupplier)) {
                    unset($this->components[$id]);
                }
            }
        }
    }

    public function checkProductSource() {
        return (boolean)($this->productConfig->getProductSource() == \Magestore\PurchaseOrderSuccess\Model\System\Config\ProductSource::TYPE_SUPPLIER);
    }
}
