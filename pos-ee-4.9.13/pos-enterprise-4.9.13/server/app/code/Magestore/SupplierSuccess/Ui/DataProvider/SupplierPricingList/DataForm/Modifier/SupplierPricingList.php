<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\SupplierSuccess\Ui\DataProvider\SupplierPricingList\DataForm\Modifier;

use Magento\Ui\Component\Form;
use Magestore\SupplierSuccess\Ui\DataProvider\Supplier\DataForm\Modifier\AbstractModifier;
use Magento\Ui\Component;
use Magento\Ui\Component\Container;

/**
 * Data provider for Configurable panel
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SupplierPricingList extends AbstractModifier
{

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
                'pricing_listing' => [
                    'children' => [
                        'button' => $this->getCustomButtons(),
                        'listing' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'autoRender' => true,
                                        'componentType' => 'insertListing',
//                                        'component' => 'Magestore_SupplierSuccess/js/form/components/insert-listing',
                                        'ns' => 'os_supplier_pricing_listing',
                                        'sortOrder' => '10',
                                        'params' => ['id' => $this->requestInterface->getParam('id', null)]
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => null,
                                'autoRender' => true,
                                'collapsible' => false,
                                'visible' => true,
                                'opened' => true,
                                'componentType' => Form\Fieldset::NAME,
                                'sortOrder' => 25
                            ],
                        ],
                    ],
                ],
            ],
            $this->getSupplierProductListingAddModal($meta)
        );
        return $meta;
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
                        'content' => '',
                        'template' => 'ui/form/components/complex',
                    ],
                ],
            ],
            'children' => [
                'add_product_button' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => 'container',
                                'componentType' => 'container',
                                'component' => 'Magento_Ui/js/form/components/button',
                                'actions' => [
                                    [
                                        'targetName' =>
                                            'os_supplier_pricinglist_form' . '.' . 'os_supplier_pricinglist_form'
                                            . '.'
                                            . 'supplier_pricinglist_listing_add',
                                        'actionName' => 'openModal'
                                    ],
                                    [
                                        'targetName' =>
                                            'os_supplier_pricinglist_form' . '.' . 'os_supplier_pricinglist_form'
                                            . '.'
                                            . 'supplier_pricinglist_listing_add'
                                            . '.'
                                            . 'os_supplier_pricinglist_modal_add_listing',
                                        'actionName' => 'render'
                                    ],
                                ],
                                'title' => __('Add Pricelist'),
                                'provider' => null,
                            ],
                        ],
                    ],
                ],
                'import_product_button' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => 'container',
                                'componentType' => 'container',
                                'component' => 'Magestore_SupplierSuccess/js/element/import-button',
                                'actions' => [],
                                'title' => __('Import Pricelist'),
                                'provider' => null,
                            ],
                        ],
                    ],
                ]
            ]
        ];
    }

    /**
     * @param array $meta
     * @return array
     */
    public function getSupplierProductListingAddModal(array $meta)
    {
        $meta['supplier_pricinglist_listing_add']['arguments']['data']['config'] = [
            'componentType' => Component\Modal::NAME,
            'type' => 'container',
            'options' => [
                'title' => __('Add Pricelist to supplier'),
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
//                                'targetName' => 'index = os_supplier_pricinglist_modal_add_listing',
                                'targetName' => 'os_supplier_pricinglist_modal_add_listing.os_supplier_pricinglist_modal_add_listing',
                                'actionName' => 'save'
                            ],
//                            'closeModal'
                        ]
                    ]
                ],
            ],
        ];

        $meta['supplier_pricinglist_listing_add']['children'] = [
            'os_supplier_pricinglist_modal_add_listing' => $this->getSupplierProductListingAddModalSelect()
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
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'autoRender' => false,
                        'componentType' => 'insertForm',
                        'component' => 'Magestore_SupplierSuccess/js/form/components/insert-form',
                        'ns' => 'os_supplier_pricinglist_modal_add_listing',
                        'sortOrder' => '25',
//                        'params' => [
//                            'purchase_id' => 1,
//                            'supplier_id' => 1
//                        ]
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
    public function getSupplierProductListingAddModalSelect1()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'component' => 'Magestore_SupplierSuccess/js/form/components/insert-listing',
                        'autoRender' => false,
                        'componentType' => 'insertListing',
                        'dataScope' => 'os_supplier_pricinglist_modal_add_listing',
                        'externalProvider' => 'os_supplier_pricinglist_modal_add_listing'
                            . '.' . 'os_supplier_pricinglist_modal_add_listing'
                            . '_data_source',
                        'ns' => 'os_supplier_pricinglist_modal_add_listing',
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
                            'os_supplier_pricinglist_modal_add_listing'
                            . '.' . 'os_supplier_pricinglist_modal_add_listing'
                            . '.supplier_pricinglist_select_columns.ids',
                        'save_url' => $this->urlBuilder->getUrl(
                            'suppliersuccess/supplier_pricinglist/save',
                            [
                                'supplier_id' => $this->requestInterface->getParam('id')
                            ]
                        ),
                        'reloadObjects' => [
                            [
//                                'name' => 'os_supplier_pricinglist_form' . '.' . 'os_supplier_pricinglist_form' . '.' . 'pricing_listing',
                                'name' => 'os_supplier_pricinglist_form' . '.' . 'os_supplier_pricinglist_form' . '.' . 'pricing_listing' . '.pricing_listing',
                                'type' => 'ui'
                            ]
                        ],
                        'closeModal' => 'os_supplier_pricinglist_form' . '.' . 'os_supplier_pricinglist_form' . '.' . 'supplier_pricinglist_listing_add'
                    ]
                ]
            ]
        ];
    }
}
