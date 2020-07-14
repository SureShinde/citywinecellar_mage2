<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\SupplierSuccess\Ui\DataProvider\Product\Form\Modifier;

use Magento\Ui\Component\Form;

/**
 * Class Related
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Supplier implements \Magento\Ui\DataProvider\Modifier\ModifierInterface
{
    /**
     * @var \Magento\Framework\UrlInterface|UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magestore\SupplierSuccess\Model\ResourceModel\Product\Supplier\Collection
     */
    protected $productSupplierCollection;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_currentProduct;

    /**
     * @var string
     */
    protected $_groupContainer = 'suppliers';

    /**
     * Data Links Type
     *
     * @var string
     */
    protected $_dataLinks = 'data';

    /**
     * Button Title
     *
     * @var string
     */
    protected $_buttonTitle = 'Add Supplier';

    /**
     * Modal Title
     *
     * @var string
     */
    protected $_modalTitle = 'Add Supplier';

    /**
     * Modal Button Title
     *
     * @var string
     */
    protected $_modalButtonTitle = 'Add Selected Supplier';

    /**
     * Modifier Config
     *
     * @var array
     */
    protected $_modifierConfig = [
        'button_set' => 'product_supplier_button_set',
        'modal' => 'product_supplier_modal',
        'listing' => 'os_product_supplier_listing',
        'form' => 'product_form',
        'columns_ids' => 'supplier_columns.ids',
    ];

    /**
     * Fields Map
     *
     * @var array
     */
    protected $_mapFields = [
        'id' => 'supplier_id',
        'supplier_code' => 'supplier_code',
        'product_supplier_sku' => 'product_supplier_sku',
        'cost' => 'cost',
        'tax' => 'tax'
    ];


    /**
     * Supplier constructor.
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Registry $registry
     * @param \Magestore\SupplierSuccess\Model\ResourceModel\Product\Supplier\Collection $productSupplierCollection
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Registry $registry,
        \Magestore\SupplierSuccess\Model\ResourceModel\Product\Supplier\Collection $productSupplierCollection
    )
    {
        $this->urlBuilder = $urlBuilder;
        $this->registry = $registry;
        $this->productSupplierCollection = $productSupplierCollection;
    }

    /**
     * Get current warehouse
     *
     * @return \Magento\Catalog\Model\Product
     * @throws NoSuchEntityException
     */
    public function getCurrentProduct()
    {
        if (!$this->_currentProduct)
            $this->_currentProduct = $this->registry->registry('current_product');
        return $this->_currentProduct;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        if (isset($data[array_keys($data)[0]]['product']))
            $data = array_replace_recursive(
                $data, [array_keys($data)[0] => ['product' => ['current_product_id' => array_keys($data)[0]]]]
            );
        $product = $this->getCurrentProduct();
        $data[$product->getId()]['add_supplier'] = 1;
        if ($product && $product->getId()) {
            $data[$product->getId()]['suppliers'] = ['data' => []];
            foreach ($this->productSupplierCollection as $item) {
                $data[$product->getId()]['suppliers']['data'][] = $this->fillDynamicData($item);
            }

        }
        return $data;
    }

    /**
     * {@inheritdoc}
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
                                'label' => __('Suppliers'),
                                'collapsible' => true,
                                'visible' => true,
                                'opened' => false,
                                'componentType' => \Magento\Ui\Component\Form\Fieldset::NAME,
                                'sortOrder' => 500
                            ],
                        ],
                    ],
                ],
            ]
        );
        return $meta;
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
        return $children;
    }

    /**
     * Returns Buttons Set configuration
     *
     * @return array
     */
    public function getCustomButtons()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'label' => false,
                        'template' => 'Magestore_SupplierSuccess/form/components/button-list',
                    ],
                ],
            ],
            'children' => [
                'grouped_products_button' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => 'container',
                                'componentType' => 'container',
                                'component' => 'Magento_Ui/js/form/components/button',
                                'actions' => [
                                    [
                                        'targetName' =>
                                            $this->_modifierConfig['form'] . '.' . $this->_modifierConfig['form']
                                            . '.'
                                            . $this->_groupContainer
                                            . '.'
                                            . $this->_modifierConfig['modal'],
                                        'actionName' => 'openModal',
                                    ],
                                    [
                                        'targetName' =>
                                            $this->_modifierConfig['form'] . '.' . $this->_modifierConfig['form']
                                            . '.'
                                            . $this->_groupContainer
                                            . '.'
                                            . $this->_modifierConfig['modal']
                                            . '.'
                                            . $this->_modifierConfig['listing'],
                                        'actionName' => 'render',
                                    ],
                                ],
                                'title' => __($this->_buttonTitle),
                                'provider' => null,
                            ],
                        ],
                    ],
                ],
            ],
        ];
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
                        'componentType' => \Magento\Ui\Component\Modal::NAME,
                        'dataScope' => '',
                        'provider' =>
                            $this->_modifierConfig['form']
                            . '.'
                            . $this->_modifierConfig['form']
                            . '_data_source',
                        'options' => [
                            'title' => __($this->_modalTitle),
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
                        'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
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
                            'product_id' => '${ $.provider }:data.product.current_product_id',
                        ],
                        'exports' => [
                            'product_id' => '${ $.externalProvider }:params.product_id',
                        ],
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
                        'componentType' => \Magento\Ui\Component\DynamicRows::NAME,
                        'label' => null,
                        'renderDefaultRecord' => false,
                        'template' => 'ui/dynamic-rows/templates/grid',
                        'component' => 'Magento_Ui/js/dynamic-rows/dynamic-rows-grid',
                        'addButton' => false,
                        'itemTemplate' => 'record',
                        'dataScope' => 'data.suppliers',
                        'deleteButtonLabel' => __('Remove'),
                        'dataProvider' => $this->_modifierConfig['listing'],
                        'map' => $this->_mapFields,
                        'links' => ['insertData' => '${ $.provider }:${ $.dataProvider }'],
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
     * Fill meta columns
     *
     * @return array
     */
    public function fillModifierMeta()
    {
        return [
            'id' => $this->getTextColumn('id', false, __('Supplier Id'), 10),
            'supplier_code' => $this->getTextColumn('supplier_code', false, __('Supplier Name'), 20),
            'product_supplier_sku' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'dataType' => Form\Element\DataType\Number::NAME,
                            'formElement' => Form\Element\Input::NAME,
                            'componentType' => Form\Field::NAME,
                            'dataScope' => 'product_supplier_sku',
                            'label' => __('Supplier SKU'),
                            'fit' => true,
                            'additionalClasses' => 'admin__field-small',
                            'sortOrder' => 30,
                            'validation' => [
//                                'required-entry' => true,
                            ],
                        ],
                    ],
                ],
            ],
            'cost' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'dataType' => Form\Element\DataType\Number::NAME,
                            'formElement' => Form\Element\Input::NAME,
                            'componentType' => Form\Field::NAME,
                            'dataScope' => 'cost',
                            'label' => __('Cost'),
                            'fit' => true,
                            'additionalClasses' => 'admin__field-small',
                            'sortOrder' => 40,
                            'validation' => [
                                'validate-number' => true,
                                'required-entry' => true,
                            ],
                        ],
                    ],
                ],
            ],
            'tax' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'dataType' => Form\Element\DataType\Number::NAME,
                            'formElement' => Form\Element\Input::NAME,
                            'componentType' => Form\Field::NAME,
                            'dataScope' => 'tax',
                            'label' => __('Tax'),
                            'fit' => true,
                            'additionalClasses' => 'admin__field-small',
                            'sortOrder' => 50,
                            'validation' => [
                                'validate-number' => true,
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

    /**
     * Returns text column configuration for the dynamic grid
     *
     * @param string $dataScope
     * @param bool $fit
     * @param Phrase $label
     * @param int $sortOrder
     * @return array
     */
    public function getTextColumn($dataScope, $fit, \Magento\Framework\Phrase $label, $sortOrder)
    {
        $column = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Form\Field::NAME,
                        'formElement' => Form\Element\Input::NAME,
                        'elementTmpl' => 'ui/dynamic-rows/cells/text',
                        'dataType' => Form\Element\DataType\Text::NAME,
                        'dataScope' => $dataScope,
                        'fit' => $fit,
                        'label' => $label,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];
        return $column;
    }

    /**
     * Fill data column
     *
     * @param \Magestore\SupplierSuccess\Model\Supplier $item
     * @return array
     */
    public function fillDynamicData($item)
    {
        return [
            'id' => $item->getId(),
            'supplier_code' => $item->getSupplierCode(),
            'product_supplier_sku' => $item->getProductSupplierSku(),
            'cost' => $item->getCost(),
            'tax' => $item->getTax(),
        ];
    }
}
