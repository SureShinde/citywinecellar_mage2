<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Ui\DataProvider\ReturnOrder\Form\Modifier;

use Magento\Ui\Component\Container;
use Magento\Ui\Component\Modal;
use Magestore\PurchaseOrderSuccess\Model\ReturnOrder\Option\Status;

/**
 * Class PurchaseSumary
 * @package Magestore\PurchaseOrderSuccess\Ui\DataProvider\ReturnOrder\Form\Modifier
 */
class ReturnSumary extends AbstractModifier
{
    /** @var \Magento\Framework\Stdlib\DateTime\DateTime */
    protected $_dateTime;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var string
     */
    protected $groupContainer = 'return_sumary';

    /**
     * @var string
     */
    protected $groupLabel = 'Summary Information';

    /**
     * @var string
     */
    protected $scopeName = 'os_return_order_form.os_return_order_form';

    /**
     * @var int
     */
    protected $sortOrder = 10;

    /**
     * @var array
     */
    protected $children = [
        'item_grid_container' => 'item_grid_container',
        'return_sumary_supplier' => 'return_sumary_supplier',
        'item_grid_listing' => 'os_return_order_item_listing',
        'product_summary_buttons' => 'product_summary_buttons',
        'all_supplier_product_modal' => 'all_supplier_product_modal',
        'all_supplier_product_listing' => 'os_return_order_all_supplier_product',
        'import_product_modal' => 'import_product_modal',
        'import_product_form' => 'os_return_order_import_product_form',
        'scan_product_modal' => 'scan_product_modal',
        'scan_product_form' => 'os_return_order_scan_product_form',
    ];

    /**
     * @var string
     */
    protected $jsObjectName;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Module\Manager $moduleManager
    )
    {
        parent::__construct($objectManager, $registry, $request, $urlBuilder);
        $this->_dateTime = $dateTime;
        $this->moduleManager = $moduleManager;
    }

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
        if (!$this->getReturnOrderId()) {
            return $meta;
        }
        $actions = null;
        $returnOrder = $this->getCurrentReturnOrder();
        if ($returnOrder->getStatus() != Status::STATUS_PENDING) {
            $actions = [
                [
                    'targetName' => $this->scopeName . '.' . $this->groupContainer . '.' .
                        $this->children['item_grid_container'],
                    'actionName' => 'render',
                ],
            ];
        }
        $meta = array_replace_recursive(
            $meta,
            [
                $this->groupContainer => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __($this->groupLabel),
                                'collapsible' => true,
                                'component' => 'Magestore_PurchaseOrderSuccess/js/form/components/fieldset',
                                'dataScope' => 'data',
                                'visible' => $this->getVisible(),
                                'opened' => $this->getOpened(),
                                'componentType' => \Magento\Ui\Component\Form\Fieldset::NAME,
                                'sortOrder' => $this->getSortOrder(),
                                'actions' => $actions
                            ],
                        ],
                    ],
                    'children' => $this->getPurchaseSumaryChildren()
                ],
            ]
        );
        return $meta;
    }

    /**
     *
     * @return boolean
     */
    public function getOpened()
    {
        return true;
    }

    /**
     * Add general form fields
     *
     * @return array
     */
    public function getPurchaseSumaryChildren()
    {
        $children = [
            $this->children['all_supplier_product_modal'] => $this->getAllSupplierProductModal()
        ];
        $children[$this->children['import_product_modal']] = $this->getImportProductModal();
        /**
         * @var \Magento\Framework\Module\Manager $moduleManager
         */
        $moduleManager = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magento\Framework\Module\Manager');
        if($moduleManager->isEnabled('Magestore_BarcodeSuccess')) {
            $children[$this->children['scan_product_modal']] = $this->getScanProductModal();
        }

        $returnOrder = $this->getCurrentReturnOrder();
        $children['return_sumary_supplier'] = $this->getPurchaseSumarySupplier();
        if ($returnOrder->getStatus() != Status::STATUS_PENDING) {
            $children[$this->children['item_grid_container']] = $this->getItemGridUi();
        } else {
            $children[$this->children['product_summary_buttons']] = $this->getProductSumaryButton();
            $children[$this->children['item_grid_container']] = $this->getItemGrid();
        }
        return $children;
    }

    /**
     * @return array
     */
    public function getPurchaseSumarySupplier()
    {
        return $this->addHtmlContentContainer(
            'return_sumary_supplier',
            'Magestore\PurchaseOrderSuccess\Block\Adminhtml\ReturnOrder\Edit\Fieldset\ReturnSumary\Supplier'
        );
    }

    /**
     * Get return order item grid
     *
     * @return array
     */
    public function getItemGrid()
    {
        return $this->addHtmlContentContainer(
            'grid_container',
            'Magestore\PurchaseOrderSuccess\Block\Adminhtml\ReturnOrder\Edit\Fieldset\ReturnSumary'
        );
    }

    /**
     * Get return order item grid
     *
     * @return array
     */
    public function getItemGridUi()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'autoRender' => true,
                        'componentType' => 'insertListing',
                        'dataScope' => $this->children['item_grid_listing'],
                        'externalProvider' => $this->children['item_grid_listing']
                            . '.' . $this->children['item_grid_listing'] . '_data_source',
                        'ns' => $this->children['item_grid_listing'],
                        'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                        'realTimeLink' => true,
                        'dataLinks' => [
                            'imports' => false,
                            'exports' => true
                        ],
                        'behaviourType' => 'simple',
                        'externalFilterMode' => true,
                        'imports' => [
                            'return_id' => '${ $.provider }:data.return_id'
                        ],
                        'exports' => [
                            'return_id' => '${ $.externalProvider }:params.return_id'
                        ],
                        'selectionsProvider' =>
                            $this->children['item_grid_listing']
                            . '.' . $this->children['item_grid_listing']
                            . '.return_order_item_template_columns.ids'
                    ]
                ]
            ]
        ];
    }

    /**
     * Add action for buttons return sumary
     *
     * @param string $modalName
     * @param string $modalListingName
     * @return array
     */
    public function addButtonAction($modalName, $modalListingName)
    {
        return [
            [
                'targetName' => $this->scopeName . '.' . $this->groupContainer . '.' . $modalName,
                'actionName' => 'openModal'
            ], [
                'targetName' => $this->scopeName . '.' . $this->groupContainer . '.' . $modalName
                    . '.' . $modalListingName,
                'actionName' => 'render',
            ], [
                'targetName' => $this->scopeName . '.' . $this->groupContainer . '.' . $modalName
                    . '.' . $modalListingName,
                'actionName' => 'reload',
            ],
        ];
    }

    /**
     * Get product sumary buttons
     *
     * @return array
     */
    public function getProductSumaryButton()
    {
        $children = [
            'import_product_button' => $this->addButton(
                'Import Products',
                [
                    [
                        'targetName' => $this->scopeName . '.' . $this->groupContainer
                            . '.' . $this->children['import_product_modal'],
                        'actionName' => 'openModal'
                    ], [
                        'targetName' => $this->scopeName . '.' . $this->groupContainer
                            . '.' . $this->children['import_product_modal']
                            . '.' . $this->children['import_product_form'],
                        'actionName' => 'render'
                    ]
                ]
            ),
        ];
        if ($this->objectManager->create('Magento\Framework\Module\Manager')
            ->isEnabled('Magestore_BarcodeSuccess')
        ) {
            $children['scan_product_button'] = $this->addButton(
                'Scan Products',
                [
                    [
                        'targetName' => $this->scopeName . '.' . $this->groupContainer
                            . '.' . $this->children['scan_product_modal'],
                        'actionName' => 'openModal'
                    ], [
                    'targetName' => $this->scopeName . '.' . $this->groupContainer
                        . '.' . $this->children['scan_product_modal']
                        . '.' . $this->children['scan_product_form'],
                    'actionName' => 'render'
                    ]
                ]
            );
        }
        $children['all_supplier_product_button'] = $this->addButton(
            'All Supplier Products',
            $this->addButtonAction(
                $this->children['all_supplier_product_modal'],
                $this->children['all_supplier_product_listing']
            )
        );
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => Container::NAME,
                        'componentType' => Container::NAME,
                        'label' => false,
                        'template' => 'Magestore_PurchaseOrderSuccess/form/components/button-list',
                    ],
                ],
            ],
            'children' => $children,
        ];
    }

    public function getJsObjectName()
    {
        if (!$this->jsObjectName)
            $this->jsObjectName = $this->objectManager
                ->get('Magestore\PurchaseOrderSuccess\Block\Adminhtml\ReturnOrder\Edit\Fieldset\ReturnSumary\Item')
                ->getJsObjectName();
        return $this->jsObjectName;
    }

    /**
     * Add Product Modal
     *
     * @param string $title
     * @param string $dataScope
     * @return array
     */
    public function addProductModal($title, $dataScope, $modal)
    {
        $jsObjectName = $this->getJsObjectName();
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'type' => 'container',
                        'options' => [
                            'onCancel' => 'actionCancel',
                            'title' => __($title),
                            'buttons' => [
                                [
                                    'text' => __('Cancel'),
                                    'actions' => ['closeModal']
                                ],
                                [
                                    'text' => __('Add Selected Products'),
                                    'class' => 'action-primary',
                                    'actions' => [
                                        [
                                            'targetName' => 'index = ' . $this->children[$dataScope],
                                            'actionName' => 'save',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'children' => [
                $this->children[$dataScope] => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'component' => 'Magestore_PurchaseOrderSuccess/js/form/components/insert-listing',
                                'autoRender' => false,
                                'componentType' => 'insertListing',
                                'dataScope' => $this->children[$dataScope],
                                'externalProvider' => $this->children[$dataScope] . '.' . $this->children[$dataScope]
                                    . '_data_source',
                                'ns' => $this->children[$dataScope],
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
                                    'return_id' => '${ $.provider }:data.return_id',
                                    'warehouse_id' => '${ $.provider }:data.warehouse_id'
                                ],
                                'exports' => [
                                    'supplier_id' => '${ $.externalProvider }:params.supplier_id',
                                    'return_id' => '${ $.externalProvider }:params.return_id',
                                    'warehouse_id' => '${ $.externalProvider }:params.warehouse_id'
                                ],
                                'selectionsProvider' =>
                                    $this->children[$dataScope]
                                    . '.' . $this->children[$dataScope]
                                    . '.supplier_product_template_columns.ids',
                                'save_url' => $this->urlBuilder->getUrl(
                                    '*/returnOrder_product/save',
                                    [
                                        'return_id' => $this->getReturnOrderId(),
                                        'supplier_id' => $this->getCurrentReturnOrder()->getSupplierId()
                                    ]
                                ),
                                'reloadObjects' => [
                                    [
                                        'name' => $jsObjectName,
                                        'type' => 'block'
                                    ]
                                ],
                                'closeModal' => $this->scopeName . '.' . $this->groupContainer . '.' . $this->children[$modal]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Get all supplier product modal
     *
     * @return array
     */
    public function getAllSupplierProductModal()
    {
        return $this->addProductModal('All Supplier Products', 'all_supplier_product_listing', 'all_supplier_product_modal');
    }

    public function getImportProductModal()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'type' => 'container',
                        'options' => [
                            'onCancel' => 'actionCancel',
                            'title' => __('Import Product'),
                            'buttons' => [
                                [
                                    'text' => __('Cancel'),
                                    'actions' => ['closeModal']
                                ],
                                [
                                    'text' => __('Import'),
                                    'class' => 'action-primary',
                                    'actions' => [
                                        [
                                            'targetName' => 'index = ' . $this->children['import_product_form'],
                                            'actionName' => 'submit',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'children' => [
                $this->children['import_product_form'] => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender' => false,
                                'componentType' => 'insertForm',
                                'component' => 'Magestore_PurchaseOrderSuccess/js/form/components/insert-form',
                                'ns' => $this->children['import_product_form'],
                                'sortOrder' => '25',
                                'params' => [
                                    'return_id' => $this->getReturnOrderId(),
                                    'supplier_id' => $this->getCurrentReturnOrder()->getSupplierId()
                                ],
                                'formSubmitId' => 'import-return-order-product-form'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function getScanProductModal()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'type' => 'container',
                        'options' => [
                            'onCancel' => 'actionCancel',
                            'title' => __('Scan Products'),
                            'buttons' => [
                                [
                                    'text' => __('Cancel'),
                                    'actions' => ['closeModal']
                                ],
                                [
                                    'text' => __('Add Products'),
                                    'class' => 'action-primary',
                                    'actions' => [
                                        [
                                            'targetName' => $this->children['scan_product_form']
                                                . '.' . $this->children['scan_product_form'],
                                            'actionName' => 'save',
                                        ],
//                                        'closeModal'
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'children' => [
                $this->children['scan_product_form'] => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender' => false,
                                'componentType' => 'insertForm',
                                'component' => 'Magestore_PurchaseOrderSuccess/js/form/components/insert-form',
                                'ns' => $this->children['scan_product_form'],
                                'sortOrder' => '25',
                                'params' => [
                                    'return_id' => $this->getReturnOrderId(),
                                    'supplier_id' => $this->getCurrentReturnOrder()->getSupplierId(),
                                    'warehouse_id' => $this->getCurrentReturnOrder()->getWarehouseId()
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}