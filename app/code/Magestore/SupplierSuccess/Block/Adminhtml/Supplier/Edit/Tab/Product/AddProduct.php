<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Block\Adminhtml\Supplier\Edit\Tab\Product;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class AddProduct extends \Magento\Backend\Block\Widget\Grid\Extended
{

    protected $_hiddenInputField = 'supplier_products';
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product\CollectionFactory
     */
    protected $_supplierProductCollectionFactory;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    protected $_defaultLimit = 20;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product\CollectionFactory $supplierProductCollectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        $this->_supplierProductCollectionFactory = $supplierProductCollectionFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->jsonEncoder = $jsonEncoder;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('os_supplier_product_listing_add');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    /**
     * @return array|null
     */
    public function getCategory()
    {
        return $this->_coreRegistry->registry('category');
    }

    /**
     * @param Column $column
     * @return $this
     */
    public function _addColumnFilterToCollection($column)
    {
        // Set custom filters for in category flag
        if ($column->getId() == 'in_category') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } elseif (!empty($productIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @return Grid
     */
    protected function _prepareCollection()
    {
//        $supplierId = $this->getRequest()->getParam('id', null);
//        $collection = $this->_supplierProductCollectionFactory->create();
        $collection = \Magento\Framework\App\ObjectManager::getInstance()->create(
            'Magento\Catalog\Model\ResourceModel\Product\Collection'
        );
//            ->addFieldToFilter('supplier_id', $supplierId);
//        var_dump()
        $this->setCollection($collection);
//        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     */
    protected function _prepareColumns()
    {
//        if (!$this->getCategory()->getProductsReadonly()) {
        $this->addColumn(
            'in_supplier',
            [
                'type' => 'checkbox',
                'name' => 'in_supplier',
                'values' => $this->_getSelectedProducts(),
                'index' => 'entity_id',
                'header_css_class' => 'col-select col-massaction',
                'column_css_class' => 'col-select col-massaction'
            ]
        );
//        }
//        $this->addColumn(
//            'product_id',
//            [
//                'header' => __('ID'),
//                'sortable' => true,
//                'index' => 'product_id',
//                'header_css_class' => 'col-id',
//                'column_css_class' => 'col-id'
//            ]
//        );
//        $this->addColumn('product_name', ['header' => __('Name'), 'index' => 'product_name']);
        $this->addColumn(
            'sku',
            [
                'header' => __('SKU'),
                'index' => 'sku'
            ]
        );

        $this->addColumn('product_supplier_sku',
            [
                'header' => __('Supplier SKU'),
                'index' => 'product_supplier_sku',
                'renderer' => 'Magestore\SupplierSuccess\Block\Adminhtml\Supplier\Grid\Column\Renderer\Text',
                'editable' => true
            ]
        );
        $this->addColumn(
            'cost',
            [
                'header' => __('Cost'),
                'index' => 'cost',
                'renderer' => 'Magestore\SupplierSuccess\Block\Adminhtml\Supplier\Grid\Column\Renderer\Text',
                'editable' => true
            ]
        );
        $this->addColumn(
            'tax',
            [
                'header' => __('Tax'),
                'index' => 'tax',
                'renderer' => 'Magestore\SupplierSuccess\Block\Adminhtml\Supplier\Grid\Column\Renderer\Text',
                'editable' => true
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('suppliersuccess/supplier_product/grid', ['_current' => true]);
    }

    /**
     * @return array
     */
    public function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('selected_products');
        return $products;
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $item
     * @return string
     */
    public function getRowUrl($item)
    {
        return parent::getRowUrl($item); // TODO: Change the autogenerated stub
    }

    /**
     * get hidden input field name for selected products
     *
     * @return string
     */
    public function getHiddenInputField(){
        return $this->_hiddenInputField;
    }

    /**
     * @return array
     */
    public function getEditableFields()
    {
        $fields = [
            'cost',
            'tax'
        ];
        return $this->jsonEncoder->encode($fields);
    }
}