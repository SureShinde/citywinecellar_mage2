<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Ui\DataProvider\Dropship\DataForm\Modifier;

use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form;
use Magento\Ui\Component;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Modal;
use Magento\Framework\UrlInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Framework\Phrase;


/**
 * Data provider for Configurable panel
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class General implements ModifierInterface
{
    protected $_groupContainer = 'os_dropship_request';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * General constructor.
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
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
                                'label' => __('Test 1'),
                                'collapsible' => true,
                                'visible' => true,
                                'opened' => true,
                                'componentType' => Form\Fieldset::NAME,
                                'sortOrder' => 1
                            ],
                        ],
                    ],
                ],
            ],
            $this->getTest1Modal($meta),
            $this->getTest2Modal($meta),
            $this->getTest3Modal($meta)
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
            'tes1_button_set' => $this->getCustomButtons(),
            'test1_grid' => $this->getGrid(),
//            'test1_button_set' => $this->getCustomButtons(),
//            'test1' => $this->getGrid(),
//            'add_test1_modal' => $this->getTest1Modal(),
//            'associated' => $this->getDynamicGrid(),
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
                        'content' => __('Michael test 1'),
                        'template' => 'ui/form/components/complex',
                    ],
                ],
            ],
            'children' => //array_replace_recursive(
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
                                                'os_dropship_form.os_dropship_form.add_test1_modal',
                                            'actionName' => 'openModal',
                                        ],
                                        [
                                            'targetName' =>
                                                'os_dropship_form.os_dropship_form.add_test1_modal.test1_product_listing',
                                            'actionName' => 'destroyInserted',
                                        ],
                                        [
                                            'targetName' =>
                                                'os_dropship_form.os_dropship_form.add_test1_modal.test1_product_listing',
                                            'actionName' => 'render',
                                        ],
//                                        [
//                                            'targetName' =>
//                                                'os_dropship_form.os_dropship_form.add_test1_modal.test1_product_listing',
//                                            'actionName' => 'reload',
//                                        ],
                                    ],
                                    'title' => __('Test 1'),
                                    'provider' => null,
                                ],
                            ],
                        ],
                    ],
                ]
                //$this->getAdditionalButtons()
//            )
        ];
    }

    /**
     * @param array $meta
     * @return array
     */
    private function getTest1Modal(array $meta)
    {
        $meta['add_test1_modal']['arguments']['data']['config'] = [
            'isTemplate' => false,
            'componentType' => Component\Modal::NAME,
            'dataScope' => '',
            'provider' => 'os_dropship_form.os_dropship_form_data_source',
            'imports' => [
                'state' => '!index=product_attribute_add_form:responseStatus'
            ],
            'options' => [
                'title' => __('Add test 1'),
                'buttons' => [
                    [
                        'text' => 'Cancel',
                        'actions' => [
                            [
                                'targetName' => '${ $.name }',
                                'actionName' => 'actionCancel'
                            ]
                        ]
                    ],
                    [
                        'text' => __('Add test 1'),
                        'class' => 'action-primary',
                        'actions' => [
                            [
                                'targetName' => 'index = test1_product_listing',
                                'actionName' => 'save'
                            ],
                            'closeModal'
                        ]
                    ]
                ],
            ],
        ];

        $meta['add_test1_modal']['children'] = [
            'test1_button' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'additionalClasses' => 'admin__field-complex-attributes',
                            'formElement' => Container::NAME,
                            'componentType' => Container::NAME,
                            'content' => __('Select Attribute 1'),
                            'label' => false,
                            'template' => 'ui/form/components/complex',
                        ],
                    ],
                ],
                'children' => [
                    'test1_button' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'formElement' => Container::NAME,
                                    'componentType' => Container::NAME,
                                    'component' => 'Magento_Ui/js/form/components/button',
                                    'additionalClasses' => '',
                                    'actions' => [
                                        [
                                            'targetName' => 'os_dropship_form.os_dropship_form'
                                                . '.add_test2_modal',
                                            'actionName' => 'toggleModal',
                                        ],
                                        [
                                            'targetName'
                                            => 'os_dropship_form.os_dropship_form.add_test2_modal.test2_product_listing',
                                            'actionName' => 'render'
                                        ]
                                    ],
                                    'title' => __('Create New Attribute test 1'),
                                    'provider' => null,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'test1_product_listing' => $this->getModalListingTest1(),
            'test1_dynamic' => $this->getTest1Dynamic()
        ];
//        \Zend_Debug::dump($meta);die();
        return $meta;
    }

    /**
     * Returns Listing configuration
     *
     * @return array
     */
    public function getModalListingTest1()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'autoRender' => false,
                        'componentType' => 'insertListing',
                        'dataScope' => 'os_adjuststock_product_listing',
                        'externalProvider' =>
                            'os_adjuststock_product_listing.os_adjuststock_product_listing_data_source',
                        'selectionsProvider' =>
                            'os_adjuststock_product_listing.os_adjuststock_product_listing.product_columns.ids',
                        'ns' => 'os_adjuststock_product_listing',
                        'render_url' => $this->urlBuilder
                            ->getUrl('mui/index/render'),
                        'realTimeLink' => true,
                        'provider' =>
                            'os_dropship_form'
                            . '.'
                            . 'os_dropship_form'
                            . '_data_source',
                        'dataLinks' => ['imports' => false, 'exports' => true],
                        'behaviourType' => 'simple',
                        'externalFilterMode' => true,
                        'imports' => [
                            'entity_id' => '${ $.provider }:data.'.'entity_id',
//                            $this->_modalDataColumn => '${ $.provider }:data.'.$this->_modalDataColumn ,
                        ],
                        'exports' => [
                            'entity_id' => '${ $.externalProvider }:params.'.'entity_id',
//                            $this->_modalDataColumn => '${ $.externalProvider }:params.'.$this->_modalDataColumn,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Returns Listing configuration
     *
     * @return array
     */
    public function getModalListingTest2()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'autoRender' => false,
                        'componentType' => 'insertListing',
                        'dataScope' => 'os_adjuststock_product_listing',
                        'externalProvider' =>
                            'os_adjuststock_product_listing.os_adjuststock_product_listing_data_source',
                        'selectionsProvider' =>
                            'os_adjuststock_product_listing.os_adjuststock_product_listing.product_columns.ids',
                        'ns' => 'os_adjuststock_product_listing',
                        'render_url' => $this->urlBuilder
                            ->getUrl('mui/index/render'),
                        'realTimeLink' => true,
                        'provider' =>
                            'os_dropship_form'
                            . '.'
                            . 'os_dropship_form'
                            . '_data_source',
                        'dataLinks' => ['imports' => false, 'exports' => true],
                        'behaviourType' => 'simple',
                        'externalFilterMode' => true,
                        'imports' => [
                            'entity_id' => '${ $.provider }:data.'.'entity_id',
//                            $this->_modalDataColumn => '${ $.provider }:data.'.$this->_modalDataColumn ,
                        ],
                        'exports' => [
                            'entity_id' => '${ $.externalProvider }:params.'.'entity_id',
//                            $this->_modalDataColumn => '${ $.externalProvider }:params.'.$this->_modalDataColumn,
                        ],
                    ],
                ],
            ],
        ];
    }


    /**
     * @param array $meta
     * @return array
     */
    private function getTest2Modal(array $meta)
    {
        $meta['add_test2_modal']['arguments']['data']['config'] = [
            'isTemplate' => false,
            'componentType' => Component\Modal::NAME,
            'dataScope' => '',
            'provider' => 'os_dropship_form.os_dropship_form_data_source',
            'imports' => [
                'state' => '!index=product_attribute_add_form:responseStatus'
            ],
            'options' => [
                'title' => __('Add Attribute'),
                'buttons' => [
                    [
                        'text' => 'Cancel',
                        'actions' => [
                            [
                                'targetName' => '${ $.name }',
                                'actionName' => 'actionCancel'
                            ]
                        ]
                    ],
                    [
                        'text' => __('Add Test 2'),
                        'class' => 'action-primary',
                        'actions' => [
//                            [
//                                'targetName' => '${ $.name }.product_attributes_grid',
//                                'actionName' => 'save'
//                            ],
                            [
                                'targetName' =>
                                    'os_dropship_form.os_dropship_form.add_test1_modal.test1_product_listing',
                                'actionName' => 'reload',
                            ],
                            'closeModal'
                        ]
                    ]
                ],
            ],
        ];

        $meta['add_test2_modal']['children'] = [
            'add_new_attribute_button' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'additionalClasses' => 'admin__field-complex-attributes',
                            'formElement' => Container::NAME,
                            'componentType' => Container::NAME,
                            'content' => __('Select Attribute'),
                            'label' => false,
                            'template' => 'ui/form/components/complex',
                        ],
                    ],
                ],
                'children' => [
                    'add_new_attribute_button' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'formElement' => Container::NAME,
                                    'componentType' => Container::NAME,
                                    'component' => 'Magento_Ui/js/form/components/button',
                                    'additionalClasses' => '',
                                    'actions' => [
                                        [
                                            'targetName' => 'os_dropship_form.os_dropship_form'
                                                . '.add_test3_modal',
                                            'actionName' => 'toggleModal',
                                        ],
//                                        [
//                                            'targetName'
//                                            => 'os_dropship_form.os_dropship_form.add_test1_modal'
//                                                . '.create_new_attribute_modal.product_attribute_add_form',
//                                            'actionName' => 'render'
//                                        ]
                                    ],
                                    'title' => __('Create New Attribute test 2'),
                                    'provider' => null,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'test2_product_listing' => $this->getModalListingTest2(),
        ];
        return $meta;
    }


    /**
     * @param array $meta
     * @return array
     */
    private function getTest3Modal(array $meta)
    {
        $meta['add_test3_modal']['arguments']['data']['config'] = [
            'isTemplate' => false,
            'componentType' => Component\Modal::NAME,
            'dataScope' => '',
            'provider' => 'os_dropship_form.os_dropship_form_data_source',
            'imports' => [
                'state' => '!index=product_attribute_add_form:responseStatus'
            ],
            'options' => [
                'title' => __('Add Attribute'),
                'buttons' => [
                    [
                        'text' => 'Cancel',
                        'actions' => [
                            [
                                'targetName' => '${ $.name }',
                                'actionName' => 'actionCancel'
                            ]
                        ]
                    ],
                    [
                        'text' => __('Add Selected'),
                        'class' => 'action-primary',
                        'actions' => [
                            [
                                'targetName' => '${ $.name }.product_attributes_grid',
                                'actionName' => 'save'
                            ],
                            [
                                'closeModal'
                            ]
                        ]
                    ]
                ],
            ],
        ];

        $meta['add_test3_modal']['children'] = [
            'add_new_attribute_button' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'additionalClasses' => 'admin__field-complex-attributes',
                            'formElement' => Container::NAME,
                            'componentType' => Container::NAME,
                            'content' => __('Select Attribute'),
                            'label' => false,
                            'template' => 'ui/form/components/complex',
                        ],
                    ],
                ],
                'children' => [
                    'add_new_attribute_button' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'formElement' => Container::NAME,
                                    'componentType' => Container::NAME,
                                    'component' => 'Magento_Ui/js/form/components/button',
                                    'additionalClasses' => '',
                                    'actions' => [
                                        [
                                            'targetName' => 'os_dropship_form.os_dropship_form'
                                                . '.add_test4_modal',
                                            'actionName' => 'toggleModal',
                                        ],
//                                        [
//                                            'targetName'
//                                            => 'os_dropship_form.os_dropship_form.add_test1_modal'
//                                                . '.create_new_attribute_modal.product_attribute_add_form',
//                                            'actionName' => 'render'
//                                        ]
                                    ],
                                    'title' => __('Create New Attribute test 3'),
                                    'provider' => null,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        return $meta;
    }

    /**
     * Returns dynamic rows configuration
     *
     * @return array
     */
    public function getGrid()
    {
        $test =  [
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
                        'dataScope' => 'data.links',
                        'deleteButtonLabel' => __('Remove'),
                        'dataProvider' => 'os_adjuststock_product_listing',
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

//        \Zend_Debug::dump($test);die();
        return $test;
    }

    /**
     * Returns dynamic rows configuration
     *
     * @return array
     */
    public function getTest1Dynamic()
    {
        $test =  [
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
                        'dataScope' => 'data.links',
                        'deleteButtonLabel' => __('Remove'),
                        'dataProvider' => 'os_adjuststock_product_listing',
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
//        \Zend_Debug::dump($test);die();
        return $test;
    }

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
     * Returns Dynamic rows records configuration
     *
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getRows()
    {
        return [
            'record' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Container::NAME,
                            'isTemplate' => true,
                            'is_collection' => true,
                            'component' => 'Magento_Ui/js/dynamic-rows/record',
                            'dataScope' => '',
                        ],
                    ],
                ],
                'children' => [
                    'id' => $this->getTextColumn('id', true, __('ID'), 10),
                    'sku' => $this->getTextColumn('sku', false, __('SKU'), 20),
                    'name' => $this->getTextColumn('name', false, __('Name'), 30),
                    'actionsList' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'formElement' => Container::NAME,
                                    'componentType' => Container::NAME,
                                    'component' => 'Magento_Ui/js/form/components/button',
                                    'additionalClasses' => '',
//                                    'actions' => [
//                                        [
//                                            [
//                                                'targetName' =>
//                                                    'os_dropship_form.os_dropship_form.add_test1_modal',
//                                                'actionName' => 'openModal',
//                                            ],
//                                            [
//                                                'targetName' =>
//                                                    'os_dropship_form.os_dropship_form.add_test1_modal.test1_product_listing',
//                                                'actionName' => 'destroyInserted',
//                                            ],
//                                            [
//                                                'targetName' =>
//                                                    'os_dropship_form.os_dropship_form.add_test1_modal.test1_product_listing',
//                                                'actionName' => 'render',
//                                                'params' => [
//                                                    true,
//                                                    [
//                                                        'test' => '1',//'${ $.provider }:data.record.id'
//                                                    ]
//                                                ],
//                                            ],
//                                        ],
//                                        [
//                                            'targetName'
//                                            => 'os_dropship_form.os_dropship_form.add_test1_modal'
//                                                . '.create_new_attribute_modal.product_attribute_add_form',
//                                            'actionName' => 'render'
//                                        ]
//                                    ],
                                    'actions' => [
                                        [
                                            'targetName' =>
                                                'os_dropship_form.os_dropship_form.add_test1_modal',
                                            'actionName' => 'openModal',
                                        ],
                                        [
                                            'targetName' =>
                                                'os_dropship_form.os_dropship_form.add_test1_modal.test1_product_listing',
                                            'actionName' => 'destroyInserted',
                                        ],
                                        [
                                            'targetName' =>
                                                'os_dropship_form.os_dropship_form.add_test1_modal.test1_product_listing',
                                            'actionName' => 'render',
                                            'params' => [
                                                true,
                                                [
                                                    'test' => '1',//'${ $.provider }:data.record.id'
                                                ]
                                            ],
                                        ]
                                    ],
                                    'title' => __('Create New Attribute test 2121'),
                                    'provider' => null,
                                ],
                            ],
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
     * Get configuration of column
     *
     * @param string $name
     * @param \Magento\Framework\Phrase $label
     * @param array $editConfig
     * @param array $textConfig
     * @return array
     */
    public function getColumn(
        $name,
        \Magento\Framework\Phrase $label,
        $editConfig = [],
        $textConfig = []
    ) {
        $fieldEdit['arguments']['data']['config'] = [
            'dataType' => Form\Element\DataType\Number::NAME,
            'formElement' => Form\Element\Input::NAME,
            'componentType' => Form\Field::NAME,
            'dataScope' => $name,
            'fit' => true,
            'visibleIfCanEdit' => true,
            'imports' => [
                'visible' => '${$.provider}:${$.parentScope}.canEdit'
            ],
        ];
        $fieldText['arguments']['data']['config'] = [
            'componentType' => Form\Field::NAME,
            'formElement' => Form\Element\Input::NAME,
            'elementTmpl' => 'Magento_ConfigurableProduct/components/cell-html',
            'dataType' => Form\Element\DataType\Text::NAME,
            'dataScope' => $name,
            'visibleIfCanEdit' => false,
            'imports' => [
                'visible' => '!${$.provider}:${$.parentScope}.canEdit'
            ],
        ];
        $fieldEdit['arguments']['data']['config'] = array_replace_recursive(
            $fieldEdit['arguments']['data']['config'],
            $editConfig
        );
        $fieldText['arguments']['data']['config'] = array_replace_recursive(
            $fieldText['arguments']['data']['config'],
            $textConfig
        );
        $container['arguments']['data']['config'] = [
            'componentType' => Container::NAME,
            'formElement' => Container::NAME,
            'component' => 'Magento_Ui/js/form/components/group',
            'label' => $label,
            'dataScope' => '',
        ];
        $container['children'] = [
            $name . '_edit' => $fieldEdit,
            $name . '_text' => $fieldText,
        ];

        return $container;
    }
}
