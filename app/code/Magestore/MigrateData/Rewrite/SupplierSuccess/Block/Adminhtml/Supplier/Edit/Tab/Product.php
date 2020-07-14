<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\MigrateData\Rewrite\SupplierSuccess\Block\Adminhtml\Supplier\Edit\Tab;

class Product extends \Magestore\SupplierSuccess\Block\Adminhtml\Supplier\Edit\Tab\Product
{
    /**
     * Prepare Colums
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     * @throws \Exception
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
                'column_css_class' => 'col-select col-massaction',
                'sort_order' => 1
            ]
        );
        $this->addColumn(
            'product_sku',
            [
                'header' => __('SKU'),
                'index' => 'product_sku'
            ]
        );
        
        $this->addColumn(
            'item_lookup_code',
            [
                'header' => __('Item Lookup Code'),
                'index' => 'item_lookup_code'
            ]
        );
        
        $this->addColumn(
            'product_name',
            [
                'header' => __('Name'),
                'index' => 'product_name'
            ],
            'product_sku'
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
        /*Michael addd more field*/
        $this->addColumn(
            'minimum_order',
            [
                'header' => __('Minimum Order'),
                'index' => 'minimum_order',
                'renderer' => 'Magestore\SupplierSuccess\Block\Adminhtml\Supplier\Grid\Column\Renderer\Text',
                'editable' => true
            ]
        );
        $this->addColumn(
            'master_pack_quantity',
            [
                'header' => __('Master Pack Quantity'),
                'index' => 'master_pack_quantity',
                'renderer' => 'Magestore\SupplierSuccess\Block\Adminhtml\Supplier\Grid\Column\Renderer\Text',
                'editable' => true
            ]
        );
        /*Michael addd more field*/
        $this->addColumn(
            'delete',
            [
                'header' => __('Action'),
                'renderer' => 'Magestore\SupplierSuccess\Block\Adminhtml\Supplier\Edit\Tab\Product\Delete',
                'filter' => false,
                'sortable' => false,
            ]
        );
    
        $this->sortColumnsByOrder();
        
        return \Magento\Backend\Block\Widget\Grid\Extended::_prepareColumns();
    }
    
    /**
     * Get Editable Fields
     *
     * @return array|false|string
     */
    public function getEditableFields()
    {
        $fields = [
            ['cost', 'number'],
            ['tax', 'number'],
            ['product_supplier_sku', 'text'],
            /*Michael addd more field*/
            ['minimum_order', 'number'],
            ['master_pack_quantity', 'number']
            /*Michael addd more field*/
        ];
        return json_encode($fields);
        //return $this->jsonEncoder->encode($fields);
    }
}
