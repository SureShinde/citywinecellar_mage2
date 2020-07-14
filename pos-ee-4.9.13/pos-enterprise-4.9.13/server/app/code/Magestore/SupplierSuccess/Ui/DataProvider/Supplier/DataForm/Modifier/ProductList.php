<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\SupplierSuccess\Ui\DataProvider\Supplier\DataForm\Modifier;

use Magento\Ui\Component\Form;
use Magento\Ui\Component;
use Magento\Ui\Component\Container;
use Magestore\SupplierSuccess\Block\Adminhtml\Supplier\Edit\AssignProduct;

/**
 * Data provider for Configurable panel
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductList extends AbstractModifier
{

    /**
     * @inheritdoc
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta)
    {
        if ($this->locator->getSession(\Magestore\SupplierSuccess\Api\Data\SupplierInterface::SUPPLIER_ID)) {
            $meta = array_replace_recursive(
                $meta,
                [
                    'product_listing' => [
                        'children' => [
                            'product_listing_button' => $this->getProductListingButtons(),
                            'product_listing' => $this->getItemGrid()
                        ],
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'label' => __('Products'),
                                    'autoRender' => true,
                                    'collapsible' => true,
                                    'visible' => true,
                                    'opened' => true,
                                    'componentType' => Form\Fieldset::NAME,
                                    'sortOrder' => 25
                                ],
                            ],
                        ],
                    ],
                ],
                $this->getSupplierProductListingAddModal($meta),
                $this->getSupplierProductListingImportModal($meta),
                $this->getSupplierProductListingDeleteModal($meta)
            );
        }
        return $meta;
    }

    /**
     * Get purchase order item grid
     *
     * @return array
     */
    public function getItemGrid()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'sortOrder' => 10,

                    ],
                ],
            ],

            'children' => [
                'html_content' => [
                    'arguments' => [
                        'data' => [
                            'type' => 'html_content',
                            'name' => 'html_content',
                            'config' => [
                                'componentType' => 'container',
                                'component' => 'Magento_Ui/js/form/components/html',
                                'content' => \Magento\Framework\App\ObjectManager::getInstance()
                                    ->create(AssignProduct::class)
                                    ->toHtml()
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Returns Buttons Set configuration
     *
     * @return array
     */
    public function getProductListingButtons()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'label' => false,
                        'content' => __(
                            '<i>If you have found some products with wrong information. Please run command line:</i>' .
                            '<pre>bin/magento supplier:product</pre>'
                        ),
                        'template' => 'ui/form/components/complex',
                    ],
                ],
            ],
            'children' => [
                'add_product_button' => $this->getAddProductButtonMeta(),

                'import_product_button' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => 'container',
                                'componentType' => 'container',
                                'component' => 'Magestore_SupplierSuccess/js/element/import-button',
                                'actions' => [],
                                'title' => __('Import Product'),
                                'provider' => null,
                            ],
                        ],
                    ],
                ],

                'delete_product_button' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => 'container',
                                'componentType' => 'container',
                                'component' => 'Magento_Ui/js/form/components/button',
                                'actions' => [
                                    [
                                        'targetName' =>
                                            'os_supplier_form' . '.' . 'os_supplier_form'
                                            . '.'
                                            . 'supplier_product_listing_delete',
                                        'actionName' => 'openModal'
                                    ],
                                    [
                                        'targetName' =>
                                            'os_supplier_form' . '.' . 'os_supplier_form'
                                            . '.'
                                            . 'supplier_product_listing_delete'
                                            . '.'
                                            . 'os_supplier_product_modal_delete_listing',
                                        'actionName' => 'destroyInserted'
                                    ],
                                    [
                                        'targetName' =>
                                            'os_supplier_form' . '.' . 'os_supplier_form'
                                            . '.'
                                            . 'supplier_product_listing_delete'
                                            . '.'
                                            . 'os_supplier_product_modal_delete_listing',
                                        'actionName' => 'render'
                                    ],
                                ],
                                'imports' => [
                                    'supplier_id' => '${ $.provider }:data.supplier_id',
                                ],
                                'exports' => [
                                    'supplier_id' => '${ $.externalProvider }:params.supplier_id',
                                ],
                                'title' => __('Delete Product'),
                                'provider' => null,
                            ],
                        ],
                    ],
                ]
            ],
        ];
    }

    /**
     * Get add product button meta
     *
     * @return array
     */
    public function getAddProductButtonMeta()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'component' => 'Magento_Ui/js/form/components/button',
                        'actions' => [
                            [
                                'targetName' =>
                                    'os_supplier_form' . '.' . 'os_supplier_form'
                                    . '.'
                                    . 'supplier_product_listing_add',
                                'actionName' => 'openModal'
                            ],
                            [
                                'targetName' =>
                                    'os_supplier_form' . '.' . 'os_supplier_form'
                                    . '.'
                                    . 'supplier_product_listing_add'
                                    . '.'
                                    . 'os_supplier_product_modal_add_listing',
                                'actionName' => 'destroyInserted'
                            ],
                            [
                                'targetName' =>
                                    'os_supplier_form' . '.' . 'os_supplier_form'
                                    . '.'
                                    . 'supplier_product_listing_add'
                                    . '.'
                                    . 'os_supplier_product_modal_add_listing',
                                'actionName' => 'render'
                            ],
                        ],
                        'imports' => [
                            'supplier_id' => '${ $.provider }:data.supplier_id',
                        ],
                        'exports' => [
                            'supplier_id' => '${ $.externalProvider }:params.supplier_id',
                        ],
                        'title' => __('Add Product'),
                        'provider' => null,
                    ],
                ],
            ],
        ];
    }

    /**
     * Get supplier products data
     *
     * @param array $meta
     * @return array
     */
    public function getSupplierProductListingAddModal(array $meta)
    {
        $meta['supplier_product_listing_add']['arguments']['data']['config'] = [
            'isTemplate' => false,
            'componentType' => Component\Modal::NAME,
            'type' => 'container',
            'dataScope' => '',
            'provider' => 'os_supplier_form.os_supplier_form_data_source',
            'options' => [
                'title' => __('Add products to supplier'),
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
                        'text' => __('Add selected product(s)'),
                        'class' => 'action-primary',
                        'actions' => [
                            [
                                'targetName' => 'index = os_supplier_product_modal_add_listing',
                                'actionName' => 'save'
                            ],
//                            'closeModal'
                        ]
                    ]
                ],
            ],
        ];

        $meta['supplier_product_listing_add']['children'] = [
            'adding_button' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'additionalClasses' => 'admin__field-complex-attributes',
                            'formElement' => Container::NAME,
                            'componentType' => Container::NAME,
                            'content' => __('Select product(s) to add to this supplier'),
                            'label' => false,
                            'template' => 'ui/form/components/complex',
                        ],
                    ],
                ],
            ],
            'os_supplier_product_modal_add_listing' => $this->getSupplierProductListingAddModalSelect()
        ];
        return $meta;
    }

    /**
     * Get supplier products to delete modal
     *
     * @param array $meta
     * @return array
     */
    public function getSupplierProductListingDeleteModal(array $meta)
    {
        $meta['supplier_product_listing_delete']['arguments']['data']['config'] = [
            'isTemplate' => false,
            'componentType' => Component\Modal::NAME,
            'type' => 'container',
            'dataScope' => '',
            'provider' => 'os_supplier_form.os_supplier_form_data_source',
            'options' => [
                'title' => __('Delete products from the supplier'),
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
                        'text' => __('Delete selected product(s)'),
                        'class' => 'action-primary',
                        'actions' => [
                            [
                                'targetName' => 'index = os_supplier_product_modal_delete_listing',
                                'actionName' => 'save'
                            ],
//                            'closeModal'
                        ]
                    ]
                ],
            ],
        ];

        $meta['supplier_product_listing_delete']['children'] = [
            'adding_button' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'additionalClasses' => 'admin__field-complex-attributes',
                            'formElement' => Container::NAME,
                            'componentType' => Container::NAME,
                            'content' => __('Select product(s) to delete'),
                            'label' => false,
                            'template' => 'ui/form/components/complex',
                        ],
                    ],
                ],
            ],
            'os_supplier_product_modal_delete_listing' => $this->getSupplierProductListingDeleteModalSelect()
        ];
        return $meta;
    }

    /**
     * Get supplier products to import modal
     *
     * @param array $meta
     * @return array
     */
    public function getSupplierProductListingImportModal(array $meta)
    {
        $meta['supplier_product_listing_import']['arguments']['data']['config'] = [
            'isTemplate' => false,
            'componentType' => Component\Modal::NAME,
            'type' => 'container',
            'dataScope' => '',
            'provider' => 'os_supplier_form.os_supplier_form_data_source',
            'options' => [
                'title' => __('Import products to the supplier'),
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
                        'text' => __('Import Product'),
                        'class' => 'action-primary',
                        'actions' => [
                            [
                                'targetName' => 'index = os_supplier_product_modal_import_listing',
                                'actionName' => 'save'
                            ],
//                            'closeModal'
                        ]
                    ]
                ],
            ],
        ];

        $meta['supplier_product_listing_import']['children'] = [
            'adding_button' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'additionalClasses' => 'admin__field-complex-attributes',
                            'formElement' => Container::NAME,
                            'componentType' => Container::NAME,
                            'content' => __('Select product(s) to import'),
                            'label' => false,
                            'template' => 'ui/form/components/complex',
                        ],
                    ],
                ],
            ],
            'os_supplier_product_modal_import_listing' => $this->getSupplierProductListingDeleteModalSelect()
        ];
        return $meta;
    }

    /**
     * Returns Listing configuration
     *
     * @return array
     */
    public function getSupplierProductListingAddModalSelect()
    {
        $jsObjectName = \Magento\Framework\App\ObjectManager::getInstance()->create(
            \Magestore\SupplierSuccess\Block\Adminhtml\Supplier\Edit\Tab\Product::class
        )->getJsObjectName();
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'component' => 'Magestore_SupplierSuccess/js/form/components/insert-listing',
                        'autoRender' => false,
                        'componentType' => 'insertListing',
                        'dataScope' => 'os_supplier_product_modal_add_listing',
                        'externalProvider' => 'os_supplier_product_modal_add_listing'
                            . '.' . 'os_supplier_product_modal_add_listing'
                            . '_data_source',
                        'ns' => 'os_supplier_product_modal_add_listing',
                        'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                        'realTimeLink' => true,
                        'dataLinks' => [
                            'imports' => false,
                            'exports' => true
                        ],
                        'behaviourType' => 'simple',
                        'externalFilterMode' => true,
                        'imports' => [
                            'supplier_id' => '${ $.provider }:data.supplier_id',
                        ],
                        'exports' => [
                            'supplier_id' => '${ $.externalProvider }:params.supplier_id',
                        ],
                        'selectionsProvider' =>
                            'os_supplier_product_modal_add_listing'
                            . '.' . 'os_supplier_product_modal_add_listing'
                            . '.supplier_product_select_columns.ids',
                        'save_url' => $this->urlBuilder->getUrl(
                            'suppliersuccess/supplier_product/save',
                            [
                                'supplier_id' => $this->requestInterface->getParam('id')
                            ]
                        ),
                        'reloadObjects' => [
                            [
                                'name' => $jsObjectName,
                                'type' => 'block'
                            ]
                        ],
                        'closeModal' => 'os_supplier_form'.'.'.'os_supplier_form'.'.'.'supplier_product_listing_add'
                    ]
                ]
            ]
        ];
    }

    /**
     * Returns Listing configuration
     *
     * @return array
     */
    public function getSupplierProductListingDeleteModalSelect()
    {
        $jsObjectName = \Magento\Framework\App\ObjectManager::getInstance()->create(
            \Magestore\SupplierSuccess\Block\Adminhtml\Supplier\Edit\Tab\Product::class
        )->getJsObjectName();
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'component' => 'Magestore_SupplierSuccess/js/form/components/insert-listing',
                        'autoRender' => false,
                        'componentType' => 'insertListing',
                        'dataScope' => 'os_supplier_product_modal_delete_listing',
                        'externalProvider' => 'os_supplier_product_modal_delete_listing'
                            . '.' . 'os_supplier_product_modal_delete_listing'
                            . '_data_source',
                        'ns' => 'os_supplier_product_modal_delete_listing',
                        'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                        'realTimeLink' => true,
                        'dataLinks' => [
                            'imports' => false,
                            'exports' => true
                        ],
                        'behaviourType' => 'simple',
                        'externalFilterMode' => true,
                        'imports' => [
                            'supplier_id' => '${ $.provider }:data.supplier_id',
                        ],
                        'exports' => [
                            'supplier_id' => '${ $.externalProvider }:params.supplier_id',
                        ],
                        'selectionsProvider' =>
                            'os_supplier_product_modal_delete_listing'
                            . '.' . 'os_supplier_product_modal_delete_listing'
                            . '.supplier_product_select_columns.ids',
                        'save_url' => $this->urlBuilder->getUrl(
                            'suppliersuccess/supplier_product/delete',
                            [
                                'supplier_id' => $this->requestInterface->getParam('id')
                            ]
                        ),
                        'reloadObjects' => [
                            [
                                'name' => $jsObjectName,
                                'type' => 'block'
                            ]
                        ],
                        'closeModal' => 'os_supplier_form'.'.'.'os_supplier_form'.'.'.'supplier_product_listing_delete'
                    ]
                ]
            ]
        ];
    }
}
