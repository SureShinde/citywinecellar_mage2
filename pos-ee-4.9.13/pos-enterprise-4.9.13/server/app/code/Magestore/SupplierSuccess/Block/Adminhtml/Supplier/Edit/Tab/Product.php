<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Block\Adminhtml\Supplier\Edit\Tab;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magestore\SupplierSuccess\Model\Locator\LocatorInterface;

class Product extends \Magento\Backend\Block\Widget\Grid\Extended
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

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var null
     */
    protected  $newProductIds = null;
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
        LocatorInterface $locator,
        array $data = []
    ) {
        $this->_supplierProductCollectionFactory = $supplierProductCollectionFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->jsonEncoder = $jsonEncoder;
        $this->locator = $locator;
        if ($this->locator->getSession(\Magestore\SupplierSuccess\Api\Data\SupplierProductInterface::SUPPLIER_PRODUCT_ADD_NEW)) {
            $this->newProductIds = $this->locator->getSession(\Magestore\SupplierSuccess\Api\Data\SupplierProductInterface::SUPPLIER_PRODUCT_ADD_NEW);
            $this->locator->unsetSession(\Magestore\SupplierSuccess\Api\Data\SupplierProductInterface::SUPPLIER_PRODUCT_ADD_NEW);
        }
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('os_supplier_product_listing');
        $this->setDefaultSort('product_id');
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
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filters for in category flag
        if ($column->getId() == 'in_supplier') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('product_id', ['in' => $productIds]);
            } elseif (!empty($productIds)) {
                $this->getCollection()->addFieldToFilter('product_id', ['nin' => $productIds]);
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
        $supplierId = $this->getRequest()->getParam('id', null);
        /** @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product\Collection $collection */
        $collection = $this->_supplierProductCollectionFactory->create()
            ->addFieldToFilter('supplier_id', $supplierId);

        if ($this->newProductIds) {
            $collection->getSelect()->order(new \Zend_Db_Expr('FIELD(product_id, "' . implode('","', $this->newProductIds) . '") DESC'));
        }
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_supplier',
            [
                'type' => 'checkbox',
                'name' => 'in_supplier',
                'values' => $this->_getSelectedProducts(),
                'index' => 'product_id',
                'header_css_class' => 'col-select col-massaction',
                'filter' => false,
                'column_css_class' => 'col-select col-massaction'
            ]
        );
        $this->addColumn(
            'product_sku',
            [
                'header' => __('SKU'),
                'index' => 'product_sku'
            ]
        );

        $this->addColumn('product_supplier_sku',
            [
                'header' => __('Supplier SKU'),
                'index' => 'product_supplier_sku',
                'type' => 'text',
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

        $this->addColumn(
            'delete',
            [
                'header' => __('Action'),
                'renderer' => 'Magestore\SupplierSuccess\Block\Adminhtml\Supplier\Edit\Tab\Product\Delete',
                'filter' => false,
                'sortable' => false,
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
        if ($this->newProductIds)
            return $this->newProductIds;
        return null;
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
            ['cost', 'number'],
            ['tax', 'number'],
            ['product_supplier_sku', 'text']
        ];
        return json_encode($fields);
        //return $this->jsonEncoder->encode($fields);
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('suppliersuccess/supplier_product/deleterow');
    }

    /**
     * @return string
     */
    public function getUpdateUrl()
    {
        return $this->getUrl('suppliersuccess/supplier/updateproduct');
    }
}