<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Ui\DataProvider\ReturnOrder\Form\Modifier\ReturnSumary\Modifier\ScanBarcodeDataProvider; //phpcs:disable

use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form;
use Magento\Ui\Component\DynamicRows;
use Magestore\PurchaseOrderSuccess\Ui\DataProvider\ReturnOrder\Form\Modifier\ReturnSumary\Modifier\AbstractModifier;

/**
 * Class ProductList
 *
 * Used for product list
 */
class ProductList extends AbstractModifier
{

    /**
     * @var string
     */
    protected $groupContainer = 'product_list';

    /**
     * @var string
     */
    protected $groupLabel = 'Product List';

    /**
     * @var int
     */
    protected $sortOrder = 20;

    protected $children = [
        'dynamic_grid' => 'dynamic_grid',
        'returned_product_modal_select_listing' => 'os_return_order_item_listing'
    ];

    protected $mapFields = [
        'id' => 'product_id',
        'product_sku' => 'product_sku',
        'product_name' => 'product_name',
        'product_supplier_sku' => 'product_supplier_sku',
    ];

    /**
     * modify data
     *
     * @return array
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * Modify return order form meta
     *
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        $meta = array_replace_recursive(
            $meta,
            [
                $this->groupContainer => [
                    'children' => $this->getProductListChildren(),
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __($this->groupLabel),
                                'collapsible' => true,
                                'visible' => $this->getVisible(),
                                'opened' => true,
                                'componentType' => \Magento\Ui\Component\Form\Fieldset::NAME,
                                'sortOrder' => $this->getSortOrder()
                            ],
                        ],
                    ],
                ],
            ]
        );
        return $meta;
    }

    /**
     * Add general form fields
     *
     * @return array
     */
    public function getProductListChildren()
    {
        $children = [
            $this->children['dynamic_grid'] => $this->getDynamicGrid()
        ];
        $children['returned_product_barcode_scan_input'] = $this->getReturnedProductScanBarcodeInput();
        return $children;
    }

    public function getReturnedProductScanBarcodeInput()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => Container::NAME,
                        'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                        'component' => 'Magestore_PurchaseOrderSuccess/js/form/element/barcode',
                        'label' => false,
                        'sortOrder' => 20,
                        'placeholder' => __('Scan product barcode here'),
                        'barcodeJson' => $this->getReturnedProductBarcodeJson(),
                        'sourceElement' => 'index = ' . $this->children['returned_product_modal_select_listing'],
                        'destinationElement' => $this->scopeName . '.' . $this->groupContainer . '.' .
                            $this->children['dynamic_grid'],
                        'selectionsProvider' =>
                            $this->children['returned_product_modal_select_listing']
                            . '.' . $this->children['returned_product_modal_select_listing']
                            . '.return_order_item_template_columns.ids',
                        'qtyElement' => $this->scopeName . '.' . $this->groupContainer . '.' .
                            $this->children['dynamic_grid'] . '.%s.returned_qty',
                        'inputElementName' => 'returned_qty',
                        'isVisibleDefault' => true
                    ],
                ],
            ],
        ];
    }

    /**
     * Returns dynamic rows configuration
     *
     * @return array
     */
    public function getDynamicGrid()
    {
        $grid = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__field-wide',
                        'componentType' => DynamicRows::NAME,
                        'label' => null,
                        'renderDefaultRecord' => false,
                        'template' => 'ui/dynamic-rows/templates/grid',
                        'component' => 'Magento_Ui/js/dynamic-rows/dynamic-rows-grid',
                        'addButton' => false,
                        'itemTemplate' => 'record',
                        'dataScope' => 'data',
                        'deleteButtonLabel' => __('Remove'),
                        'dataProvider' => $this->children['returned_product_modal_select_listing'],
                        'map' => $this->mapFields,
                        'links' => [
                            'insertData' => '${ $.provider }:${ $.dataProvider }',
                            '__disableTmpl' => [
                                'insertData' => false
                            ]
                        ],
                        'sortOrder' => 30,
                        'columnsHeader' => false,
                        'columnsHeaderAfterRender' => true,
                    ],
                ],
            ],
            'children' => $this->getRows(),
        ];
        return $grid;
    }

    /**
     * Returns Dynamic rows records configuration
     *
     * @return array
     */
    public function getRows()
    {
        return [
            'record' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => 'container',
                            'isTemplate' => true,
                            'is_collection' => true,
                            'component' => 'Magento_Ui/js/dynamic-rows/record',
                            'dataScope' => '',
                        ],
                    ],
                ],
                'children' => $this->fillModifierMeta(),
            ],
        ];
    }

    /**
     * Fill meta columns
     *
     * @return array
     */
    public function fillModifierMeta()
    {
        return [
            'id' => $this->getTextColumn('id', false, 'Product ID', 10),
            'product_sku' => $this->getTextColumn('product_sku', false, 'Product SKU', 20),
            'product_name' => $this->getTextColumn(
                'product_name',
                false,
                'Product Name',
                30
            ),
            'product_supplier_sku' => $this->getTextColumn(
                'product_supplier_sku',
                false,
                'Supplier SKU',
                40
            ),
            'returned_qty' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'dataType' => Form\Element\DataType\Number::NAME,
                            'formElement' => Form\Element\Input::NAME,
                            'componentType' => Form\Field::NAME,
                            'dataScope' => 'returned_qty',
                            'label' => __('Returned Qty'),
                            'fit' => true,
                            'additionalClasses' => 'admin__field-small',
                            'sortOrder' => 60,
                            'validation' => [
                                'validate-number' => true,
                                'validate-greater-than-zero' => true,
                                'required-entry' => true,
                            ],
                        ],
                    ],
                ],
            ],
            'actionDelete' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'additionalClasses' => 'data-grid-actions-cell',
                            'componentType' => 'actionDelete',
                            'dataType' => Form\Element\DataType\Text::NAME,
                            'label' => __('Actions'),
                            'sortOrder' => 70,
                            'fit' => true,
                        ],
                    ],
                ],
            ]
        ];
    }

    /**
     * Get returned product barcode json
     *
     * @return mixed
     */
    public function getReturnedProductBarcodeJson()
    {
        $result = [];
        $collection = $this->getReturnedProductBarcodeCollection();
        foreach ($collection->getItems() as $item) {
            $result[$item->getBarcode()] = $item->getData();
        }
        return \Magento\Framework\App\ObjectManager::getInstance()
            ->create(\Magento\Framework\Json\EncoderInterface::class)->encode($result);
    }

    /**
     * Get returned product barcode collection
     *
     * @return mixed
     */
    public function getReturnedProductBarcodeCollection()
    {
        $warehouseId = $this->request->getParam('warehouse_id', null);
        $supplierId = $this->request->getParam('supplier_id', null);
        $collection = \Magento\Framework\App\ObjectManager::getInstance()
            ->create(\Magestore\BarcodeSuccess\Model\ResourceModel\Barcode\Collection::class);
        /** @var \Magestore\PurchaseOrderSuccess\Service\ReturnOrder\Item\ItemService $returnItemService */
        $returnItemService = \Magento\Framework\App\ObjectManager::getInstance()
            ->create(\Magestore\PurchaseOrderSuccess\Service\ReturnOrder\Item\ItemService::class);
        $listProductsIdOnSource = $returnItemService->getProductIdOnCurrentWarehouse($warehouseId);
        $collection->addFieldToFilter('main_table.product_id', ['in' => $listProductsIdOnSource]);
        $collection->addFieldToSelect(['barcode']);
        $collection->getSelect()->joinInner(
            ['item' => $collection->getTable('os_supplier_product')],
            'main_table.product_id = item.product_id AND item.supplier_id = ' . $supplierId,
            '*'
        );
        return $collection;
    }
}
