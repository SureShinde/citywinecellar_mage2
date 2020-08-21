<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Ui\DataProvider\AdjustStock\Form\Modifier;

use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form;
use Magento\Ui\Component\Modal;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magestore\AdjustStock\Api\Data\AdjustStock\AdjustStockInterface;

/**
 * Class ProductList
 *
 * Modifier Product list
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class ProductList extends AdjustStock implements ModifierInterface
{
    protected $_groupContainer = 'os_adjuststock';
    protected $_groupLabel = 'Product List';
    protected $_sortOrder = '10';
    protected $_dataLinks = 'product_list';
    protected $_fieldsetContent = 'Please add or import products to adjust stock';
    protected $_buttonTitle = 'Add Products to Adjust Stock';
    protected $_modalTitle = 'Add Products to Adjust Stock';
    protected $_scanTitle = 'Scan barcode';
    protected $_modalDataId = 'adjuststock_id';
    protected $_modalDataColumn = 'source_code';
    protected $_useButtonSet = true;
    protected $_modalButtonTitle = 'Add Selected Products';
    protected $_importTitle = 'Import products';
    protected $_modalListingRenderParams = [];

    /**
     * @var array
     */
    protected $_modifierConfig = [
        'button_set' => 'product_stock_button_set',
        'modal' => 'product_stock_modal',
        'listing' => 'os_adjuststock_product_listing',
        'form' => 'os_adjuststock_form',
        'columns_ids' => 'product_columns.ids'
    ];

    /**
     * @var array
     */
    protected $_mapFields = [
        'id' => 'entity_id',
        'sku' => 'sku',
        'name' => 'name',
        'total_qty' => 'total_qty',
        'change_qty' => 'change_qty',
        'new_qty' => 'new_qty',
        'image' => 'image_url',
        'barcode' => 'barcode_original_data',
    ];

    /**
     * Get fieldset content
     *
     * @return string
     */
    public function getFieldsetContent()
    {
        if ($this->getAdjustStockStatus() != '1') {
            return $this->_fieldsetContent;
        }
        return '';
    }

    /**
     * Get use button set
     *
     * @return bool|int
     */
    public function getUseButtonSet()
    {
        if ($this->getAdjustStockStatus() != '1') {
            return $this->_useButtonSet;
        }
        return false;
    }

    /**
     * Get use modal title
     *
     * @return string
     */
    public function getModalTitle()
    {
        return $this->_modalTitle;
    }

    /**
     * Get use button title
     *
     * @return string
     */
    public function getButtonTitle()
    {
        return $this->_buttonTitle;
    }

    /**
     * @inheritdoc
     */
    public function modifyData(array $data)
    {
//        return parent::modifyData($data);
        $modelId = $this->request->getParam('id');
        if ($modelId) {
            $products = $this->collection->getAdjustedProducts($modelId);
            $data[$modelId]['links'][$this->_dataLinks] = [];
            if ($products->getSize() > 0) {
                foreach ($products as $product) {
                    $data[$modelId]['links'][$this->_dataLinks][] = $this->fillDynamicData($product);
                }
            }
        }
        return $data;
    }

    /**
     * Fill data column
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function fillDynamicData($product)
    {
        return [
            'id' => $product->getData('product_id'),
            'sku' => $product->getData('product_sku'),
            'name' => $product->getData('product_name'),
            'total_qty' => $product->getData('old_qty') * 1,
            'change_qty' => $product->getData('change_qty') * 1,
            'new_qty' => $product->getData('new_qty') * 1,
            'image' => $product->getData('image_url'),
            'barcode' => $product->getData('barcode'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta)
    {
        $meta = array_replace_recursive(
            $meta,
            [
                $this->_groupContainer => [
                    'children' => $this->getModifierChildren(),
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __($this->getGroupLabel()),
                                'collapsible' => $this->getCollapsible(),
                                'visible' => $this->getVisible(),
                                'opened' => $this->getOpened(),
                                'componentType' => Form\Fieldset::NAME,
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
     * Get modal title
     *
     * @return string
     */
    public function getImportTitle()
    {
        if ($this->getAdjustStockStatus() == AdjustStockInterface::STATUS_PROCESSING) {
            return 'Import products adjust stock';
        }
        return $this->_importTitle;
    }

    /**
     * Get use scan title
     *
     * @return string
     */
    public function getScanTitle()
    {
        return $this->_scanTitle;
    }

    /**
     * Retrieve child meta configuration
     *
     * @return array
     */
    public function getModifierChildren()
    {
        $children = [
            $this->_modifierConfig['button_set'] => $this->getCustomButtons(),
            $this->_modifierConfig['modal'] => $this->getCustomModal(),
            $this->_dataLinks => $this->getDynamicGrid(),
        ];

        /**
         * @var \Magento\Framework\Module\Manager $moduleManager
         */
        $moduleManager = \Magento\Framework\App\ObjectManager::getInstance()
            ->create(\Magento\Framework\Module\Manager::class);
        if ($moduleManager->isEnabled('Magestore_BarcodeSuccess')) {
            $children['product_barcode_scan_input'] = $this->getProductScanBarcodeInput();
        }

        return $children;
    }

    /**
     * Returns Modal configuration
     *
     * @return array
     */
    public function getCustomModal()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'dataScope' => '',
                        'provider' =>
                            $this->_modifierConfig['form']
                            . '.'
                            . $this->_modifierConfig['form']
                            . '_data_source',
                        'options' => [
                            'title' => __($this->getModalTitle()),
                            'buttons' => [
                                [
                                    'text' => __('Cancel'),
                                    'actions' => ['closeModal']
                                ],
                                [
                                    'text' => __($this->_modalButtonTitle),
                                    'class' => 'action-primary',
                                    'actions' => [
                                        [
                                            'targetName' => 'index = ' . $this->_modifierConfig['listing'],
                                            'actionName' => 'save'
                                        ],
                                        'closeModal'
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'children' => [$this->_modifierConfig['listing'] => $this->getModalListing()],
        ];
    }

    /**
     * Returns Listing configuration
     *
     * @return array
     */
    public function getModalListing()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'autoRender' => false,
                        'componentType' => 'insertListing',
                        'dataScope' => $this->_modifierConfig['listing'],
                        'externalProvider' =>
                            $this->_modifierConfig['listing']
                            . '.'
                            . $this->_modifierConfig['listing']
                            . '_data_source',
                        'selectionsProvider' =>
                            $this->_modifierConfig['listing']
                            . '.'
                            . $this->_modifierConfig['listing']
                            . '.'
                            . $this->_modifierConfig['columns_ids'],
                        'ns' => $this->_modifierConfig['listing'],
                        'render_url' => $this->urlBuilder
                            ->getUrl('mui/index/render', $this->_modalListingRenderParams),
                        'realTimeLink' => true,
                        'provider' =>
                            $this->_modifierConfig['form']
                            . '.'
                            . $this->_modifierConfig['form']
                            . '_data_source',
                        'dataLinks' => ['imports' => false, 'exports' => true],
                        'behaviourType' => 'simple',
                        'externalFilterMode' => true,
                        'imports' => [
                            $this->_modalDataId => '${ $.provider }:data.' . $this->_modalDataId,
                            $this->_modalDataColumn => '${ $.provider }:data.' . $this->_modalDataColumn,
                            '__disableTmpl' => [
                                $this->_modalDataId => false,
                                $this->_modalDataColumn => false
                            ],
                        ],
                        'exports' => [
                            $this->_modalDataId => '${ $.externalProvider }:params.' . $this->_modalDataId,
                            $this->_modalDataColumn => '${ $.externalProvider }:params.' . $this->_modalDataColumn,
                            '__disableTmpl' => [
                                $this->_modalDataId => false,
                                $this->_modalDataColumn => false,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Return scan barcode input
     *
     * @return array
     */
    public function getProductScanBarcodeInput()
    {
        $adjustStockId = $this->request->getParam('id');
        $sourceCode = $this->getCurrentAdjustment()->getSourceCode();
        $getBarcodeUrl = $this->urlBuilder->getUrl(
            'adjuststock/adjuststock/getBarcodeJson',
            [
                'adjuststock_id' => $adjustStockId,
                'source_code' => $sourceCode
            ]
        );
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => \Magento\Ui\Component\Container::NAME,
                        'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                        'component' => 'Magestore_AdjustStock/js/form/element/scan-barcode',
                        'label' => __('Scan barcode'),
                        'sortOrder' => 15,
                        'placeholder' => __('Scan/enter barcode'),
                        'getBarcodeUrl' => $getBarcodeUrl,
                        'sourceElement' => 'index = ' . $this->_modifierConfig['listing'],
                        'destinationElement' => $this->_modifierConfig['form'] . '.' .
                            $this->_modifierConfig['form'] . '.' .
                            $this->_groupContainer . '.' .
                            $this->_dataLinks,
                        'selectionsProvider' =>
                            $this->_modifierConfig['listing']
                            . '.' . $this->_modifierConfig['listing']
                            . '.product_columns.ids',
                        'qtyElement' => $this->_modifierConfig['form'] . '.' .
                            $this->_modifierConfig['form'] . '.' .
                            $this->_groupContainer . '.' .
                            $this->_dataLinks . '.%s.qty',
                        'inputElementName' => 'qty'
                    ],
                ],
            ],
        ];
    }

    /**
     * Returns Buttons Set configuration
     *
     * @return array
     */
    public function getCustomButtons()
    {
        if (!$this->getUseButtonSet()) {
            $customButtons = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'formElement' => 'container',
                            'componentType' => 'container',
                            'label' => false,
                            'template' => 'Magestore_AdjustStock/form/components/button-list',
                        ],
                    ],
                ]
            ];
        } else {
            $customButtons = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'formElement' => 'container',
                            'componentType' => 'container',
                            'label' => false,
                            'content' => __($this->getFieldsetContent()),
                            'template' => 'Magestore_AdjustStock/form/components/button-list',
                        ],
                    ],
                ],
                'children' => array_replace_recursive(
                    [
                        'dynamic_button' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement' => 'container',
                                        'componentType' => 'container',
                                        'component' => 'Magento_Ui/js/form/components/button',
                                        'actions' => [
                                            [
                                                'targetName' =>
                                                    $this->_modifierConfig['form'] . '.'
                                                    . $this->_modifierConfig['form']
                                                    . '.'
                                                    . $this->_groupContainer
                                                    . '.'
                                                    . $this->_modifierConfig['modal'],
                                                'actionName' => 'openModal',
                                            ],
                                            [
                                                'targetName' =>
                                                    $this->_modifierConfig['form'] . '.'
                                                    . $this->_modifierConfig['form']
                                                    . '.'
                                                    . $this->_groupContainer
                                                    . '.'
                                                    . $this->_modifierConfig['modal']
                                                    . '.'
                                                    . $this->_modifierConfig['listing'],
                                                'actionName' => 'render',
                                            ],
                                        ],
                                        'title' => __($this->getButtonTitle()),
                                        'provider' => null,
                                    ],
                                ],
                            ],
                        ],
                    ],
                    $this->getAdditionalButtons()
                )
            ];
        }
        if ($this->getUseButtonSet()) {
            $moduleManager = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Framework\Module\Manager::class);
            $showScanBarcodeButton = $moduleManager->isEnabled('Magestore_BarcodeSuccess') ? true : false;
            $customButtons['children'] = array_replace_recursive(
                [
                    'scan_button' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'formElement' => 'container',
                                    'componentType' => 'container',
                                    'component' => 'Magestore_AdjustStock/js/element/scan-barcode-button',
                                    'actions' => [],
                                    'title' => $this->getScanTitle(),
                                    'provider' => null,
                                    'visible' => $showScanBarcodeButton,
                                ],
                            ],
                        ],
                    ],
                ],
                $customButtons['children']
            );
        }
        return $customButtons;
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
                        'component' => 'Magestore_AdjustStock/js/dynamic-rows/dynamic-rows-grid',
                        'addButton' => false,
                        'itemTemplate' => 'record',
                        'dataScope' => 'data.links',
                        'deleteButtonLabel' => __('Remove'),
                        'dataProvider' => $this->_modifierConfig['listing'],
                        'map' => $this->_mapFields,
                        'links' => [
                            'insertData' => '${ $.provider }:${ $.dataProvider }',
                            '__disableTmpl' => ['insertData' => false],
                        ],
                        'sortOrder' => 20,
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
     * Add import product button to stocktake
     *
     * @return array
     */
    public function getAdditionalButtons()
    {
        return [
            'import_button' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'formElement' => 'container',
                            'componentType' => 'container',
                            'component' => 'Magestore_AdjustStock/js/element/import-button',
                            'actions' => [],
                            'title' => $this->getImportTitle(),
                            'provider' => null,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Get visible
     *
     * @return int|bool
     */
    public function getVisible()
    {
        $requestId = $this->request->getParam('id');
        if ($requestId) {
            return $this->_visible;
        }
        return false;
    }

    /**
     * Fill meta columns
     *
     * @return array
     */
    public function fillModifierMeta()
    {
        $additionalColumns = $this->getAdditionalColumns();
        $modifierColumns = array_replace_recursive(
            [
                'id' => $this->getTextColumn('id', true, __('ID'), 10),
                'sku' => $this->getTextColumn('sku', false, __('SKU'), 15),
                'name' => $this->getTextColumn('name', false, __('Name'), 20),
                'image' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Form\Field::NAME,
                                'formElement' => Form\Element\Input::NAME,
                                'elementTmpl' => 'Magestore_AdjustStock/dynamic-rows/cells/thumbnail',
                                'dataType' => Form\Element\DataType\Media::NAME,
                                'dataScope' => 'image',
                                'fit' => __('Thumbnail'),
                                'label' => __('Thumbnail'),
                                'sortOrder' => 30,
                                'visible' => $this->getVisibleImage()
                            ],
                        ],
                    ],
                ],
                'barcode' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Form\Field::NAME,
                                'formElement' => Form\Element\Input::NAME,
                                'elementTmpl' => 'Magestore_AdjustStock/dynamic-rows/cells/barcode',
                                'component' => 'Magestore_AdjustStock/js/form/element/barcode',
                                'dataType' => Form\Element\DataType\Text::NAME,
                                'dataScope' => 'barcode',
                                'fit' => __('Barcode'),
                                'label' => __('Barcode'),
                                'sortOrder' => 50,
                                'visible' => true
                            ],
                        ],
                    ],
                ],
                'total_qty' => $this->getTextColumn('total_qty', false, __('Old Qty'), 60),
            ],
            $additionalColumns
        );
        $modifierColumns = array_replace_recursive(
            $modifierColumns,
            $this->getActionColumns()
        );
        return $modifierColumns;
    }

    /**
     * Fill meta columns
     *
     * @return array
     */
    public function getAdditionalColumns()
    {
        if ($this->getAdjustStockStatus() == AdjustStockInterface::STATUS_COMPLETED) {
            return [
                'change_qty' => $this->getTextColumn('change_qty', false, __('Adjust Qty'), 70),
                'new_qty' => $this->getTextColumn('new_qty', false, __('New Qty'), 80),
            ];
        }

        return [
            'change_qty' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'dataType' => Form\Element\DataType\Number::NAME,
                            'formElement' => Form\Element\Input::NAME,
                            'componentType' => Form\Field::NAME,
                            'component' => 'Magestore_AdjustStock/js/form/element/adjust-qty',
                            'dataScope' => 'change_qty',
                            'label' => __('Adjust Qty'),
                            'fit' => true,
                            'additionalClasses' => 'admin__field-small',
                            'sortOrder' => 70,
                            'validation' => [
                                'validate-number' => true,
                                'validate-not-negative-number' => false,
                                'required-entry' => true,
                            ],
                        ],
                    ],
                ],
            ],
            'new_qty' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'dataType' => Form\Element\DataType\Number::NAME,
                            'formElement' => Form\Element\Input::NAME,
                            'componentType' => Form\Field::NAME,
                            'component' => 'Magestore_AdjustStock/js/form/element/new-qty',
                            'dataScope' => 'new_qty',
                            'label' => __('New Qty'),
                            'fit' => true,
                            'additionalClasses' => 'admin__field-small',
                            'sortOrder' => 80,
                            'validation' => [
                                'validate-number' => true,
                                'validate-not-negative-number' => true,
                                'required-entry' => true,
                            ],
                        ],
                    ],
                ],
            ]
        ];
    }

    /**
     * Fill action columns
     *
     * @return array
     */
    public function getActionColumns()
    {
        if ($this->getAdjustStockStatus() == AdjustStockInterface::STATUS_COMPLETED) {
            return [
                'position' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'dataType' => Form\Element\DataType\Number::NAME,
                                'formElement' => Form\Element\Input::NAME,
                                'componentType' => Form\Field::NAME,
                                'dataScope' => 'position',
                                'sortOrder' => 100,
                                'visible' => false,
                            ],
                        ],
                    ],
                ]
            ];
        }
        return [
            'actionDelete' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'additionalClasses' => 'data-grid-actions-cell',
                            'componentType' => 'actionDelete',
                            'dataType' => Form\Element\DataType\Text::NAME,
                            'label' => __('Actions'),
                            'sortOrder' => 90,
                            'fit' => true,
                        ],
                    ],
                ],
            ],
            'position' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'dataType' => Form\Element\DataType\Number::NAME,
                            'formElement' => Form\Element\Input::NAME,
                            'componentType' => Form\Field::NAME,
                            'dataScope' => 'position',
                            'sortOrder' => 100,
                            'visible' => false,
                        ],
                    ],
                ],
            ],
        ];
    }
}
