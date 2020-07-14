<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\BarcodeSuccess\Ui\DataProvider\Barcode\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Framework\Phrase;
use Magento\Ui\Component\Modal;
use Magento\Ui\Component\Form;
use Magento\GroupedProduct\Model\Product\Type\Grouped as GroupedProductType;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\DynamicRows;


/**
 * Class AbstractModifier
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class AbstractModifier extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier
    implements ModifierInterface
{
    /**
     * Collapsible
     *
     * @var string
     */
    protected $_collapsible = true;

    /**
     * Group Container
     *
     * @var string
     */
    protected $_visible = true;

    /**
     * Group Container
     *
     * @var string
     */
    protected $_opened = true;

    /**
     * Group Container
     *
     * @var string
     */
    protected $_groupContainer = 'os_adjuststock';

    /**
     * Group Template
     *
     * @var string
     */
    protected $_groupLabel = 'Product List';

    /**
     * Fieldset Content
     *
     * @var string
     */
    protected $_fieldsetContent = '';
    /**
     * sort Sales
     *
     * @var string
     */
    protected $_sortOrder = '1';

    /**
     * Data Links Type
     *
     * @var string
     */
    protected $_dataLinks = 'associated';

    /**
     * Button Title
     *
     * @var string
     */
    protected $_buttonTitle = 'Add Products';

    /**
     * Modal Title
     *
     * @var string
     */
    protected $_modalTitle = 'Add Products';

    /**
     * Modal Button Title
     *
     * @var string
     */
    protected $_modalButtonTitle = 'Add Selected Products';

    /**
     * Container Prefix
     *
     * @var string
     */
    protected $_containerPrefix = 'container_';

    /**
     * Modifier Config
     *
     * @var array
     */
    protected $_modifierConfig = [
        'button_set' => 'product_stock_button_set',
        'modal' => 'product_stock_modal',
        'listing' => 'os_adjuststock_product_listing',
        'form' => 'os_adjuststock_form',
        'columns_ids' => 'product_columns.ids',
    ];

    /**
     * Fields Map
     *
     * @var array
     */
    protected $_mapFields = [
        'id' => 'entity_id',
        'sku' => 'sku',
        'name' => 'name',
    ];

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var string
     */
    private static $quantityCode = 'quantity';

    /**
     * @var string
     */
    private static $qtyContainerCode = 'quantity_container';

    /**
     * @var string
     */
    private static $qtyCode = 'qty';

    /**
     * @var \Magestore\BarcodeSuccess\Helper\Data
     */
    protected $helper;

    /**
     * @param UrlInterface $urlBuilder
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param array $_modifierConfig
     */
    public function __construct(
        UrlInterface $urlBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Magestore\BarcodeSuccess\Helper\Data $helper,
        array $modifierConfig = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
        $this->_modifierConfig = array_replace_recursive($this->_modifierConfig, $modifierConfig);
        $this->helper = $helper;
    }

    /**
     * set visible
     *
     * @param boolean
     * @return
     */
    public function setVisible($visible)
    {
        $this->_visible = $visible;
    }

    /**
     * get visible
     *
     * @param
     * @return
     */
    public function getVisible()
    {
        return $this->_visible;
    }

    /**
     * set opened
     *
     * @param boolean
     * @return
     */
    public function setOpened($opened)
    {
        $this->_opened = $opened;
    }

    /**
     * get opened
     *
     * @param
     * @return boolean
     */
    public function getOpened()
    {
        return $this->_opened;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
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
                                'label' => __($this->_groupLabel),
                                'collapsible' => $this->_collapsible,
                                'visible' => $this->getVisible(),
                                'opened' => $this->getOpened(),
                                'componentType' => Form\Fieldset::NAME,
                                'sortOrder' => $this->_sortOrder
                            ],
                        ],
                    ],
                ],
            ]
        );
        $meta = $this->modifyQtyAndStockStatus($meta);
        return $meta;
    }

    /**
     * Disable Qty and Stock status fields
     *
     * @param array $meta
     * @return array
     */
    public function modifyQtyAndStockStatus(array $meta)
    {
        if ($groupCode = $this->getFieldGroupCode($meta, 'container_' . self::$quantityCode)) {
            $parentChildren = &$meta[$groupCode]['children'];
            if (!empty($parentChildren['container_' . self::$quantityCode])) {
                $parentChildren['container_' . self::$quantityCode] = array_replace_recursive(
                    $parentChildren['container_' . self::$quantityCode],
                    [
                        'children' => [
                            self::$quantityCode => [
                                'arguments' => [
                                    'data' => [
                                        'config' => ['disabled' => false],
                                    ],
                                ],
                            ],
                        ]
                    ]
                );
            }
        }
        if ($groupCode = $this->getFieldGroupCode($meta, self::$qtyContainerCode)) {
            $parentChildren = &$meta[$groupCode]['children'];
            if (!empty($parentChildren[self::$qtyContainerCode])) {
                $parentChildren[self::$qtyContainerCode] = array_replace_recursive(
                    $parentChildren[self::$qtyContainerCode],
                    [
                        'children' => [
                            self::$qtyCode => [
                                'arguments' => [
                                    'data' => [
                                        'config' => ['disabled' => true],
                                    ],
                                ],
                            ],
                        ],
                    ]
                );
            }
        }
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
                        'component' => 'Magestore_BarcodeSuccess/js/form/components/insert-listing',
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
                            'storeId' => '${ $.provider }:data.product.current_store_id',
                        ],
                        'exports' => [
                            'storeId' => '${ $.externalProvider }:params.current_store_id',
                        ],
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
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'label' => false,
                        'content' => __($this->_fieldsetContent),
                        'template' => 'ui/form/components/complex',
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
                        'component' => 'Magestore_BarcodeSuccess/js/dynamic-rows/dynamic-rows-grid',
                        'addButton' => false,
                        'itemTemplate' => 'record',
                        'dataScope' => 'data.links',
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
            'id' => $this->getTextColumn('id', true, __('ID'), 10),
            'sku' => $this->getTextColumn('sku', false, __('SKU'), 20),
            'name' => $this->getTextColumn('name', false, __('Name'), 30),
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
    public function getTextColumn($dataScope, $fit, Phrase $label, $sortOrder)
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
     * Get field group code
     *
     * @param array $meta
     * @param string $field
     * @return string|bool
     */
    public function getFieldGroupCode(array $meta, $field)
    {
        foreach ($meta as $groupCode => $groupData) {
            if (
                isset($groupData['children'][$field])
                || isset($groupData['children'][$this->_containerPrefix . $field])
            ) {
                return $groupCode;
            }
        }

        return false;
    }

}

