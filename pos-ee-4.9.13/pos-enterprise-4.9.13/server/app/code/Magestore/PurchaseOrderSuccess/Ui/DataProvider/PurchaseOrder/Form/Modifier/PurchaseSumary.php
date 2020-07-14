<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Form\Modifier;

use Magento\Ui\Component\Container;
use Magento\Ui\Component\Modal;
use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Status;

/**
 * Class PurchaseSumary
 * @package Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Form\Modifier
 */
class PurchaseSumary extends AbstractModifier
{
    /** @var \Magento\Framework\Stdlib\DateTime\DateTime */
    protected $_dateTime;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var bool
     */
    protected $isInventoryEnable;

    /**
     * @var string
     */
    protected $groupContainer = 'purchase_sumary';

    /**
     * @var string
     */
    protected $groupLabel = 'Summary Information';

    /**
     * @var string
     */
    protected $scopeName = 'os_purchase_order_form.os_purchase_order_form';

    /**
     * @var int
     */
    protected $sortOrder = 10;

    /**
     * @var array
     */
    protected $children = [
        'item_grid_container' => 'item_grid_container',
        'purchase_sumary_supplier' => 'purchase_sumary_supplier',
        'item_grid_listing' => 'os_purchase_order_item_listing',
        'product_summary_buttons' => 'product_summary_buttons',
        'all_supplier_product_modal' => 'all_supplier_product_modal',
        'all_supplier_product_listing' => 'os_purchase_order_all_supplier_product',
        'back_order_product_modal' => 'back_order_product_modal',
        'back_order_product_listing' => 'os_purchase_order_back_order_product',
        'import_product_modal' => 'import_product_modal',
        'import_product_form' => 'os_purchase_order_import_product_form',
    ];

    /**
     * @var string
     */
    protected $jsObjectName;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\Config\ProductConfig
     */
    protected $productConfig;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magestore\PurchaseOrderSuccess\Service\Config\ProductConfig $productConfig,
        \Magento\Framework\Module\Manager $moduleManager
    )
    {
        parent::__construct($objectManager, $registry, $request, $urlBuilder);
        $this->_dateTime = $dateTime;
        $this->productConfig = $productConfig;
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
     * Modify purchase order form meta
     *
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        if (!$this->getPurchaseOrderId()) {
            return $meta;
        }
        $actions = null;
        $purchaseOrder = $this->getCurrentPurchaseOrder();
        if ($purchaseOrder->getStatus() != Status::STATUS_PENDING)
            $actions = [
                [
                    'targetName' => $this->scopeName . '.' . $this->groupContainer . '.' .
                        $this->children['item_grid_container'],
                    'actionName' => 'render',
                ],
            ];
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
        //$purchaseOrder = $this->getCurrentPurchaseOrder();
        //if($purchaseOrder->getStatus() == Status::STATUS_PENDING)
        //    return true;
        /* always open summary tab */
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
        $children[$this->children['back_order_product_modal']] = $this->getBackOrderProductModal();
        $children[$this->children['import_product_modal']] = $this->getImportProductModal();

        $purchaseOrder = $this->getCurrentPurchaseOrder();
        $children['purchase_sumary_supplier'] = $this->getPurchaseSumarySupplier();
        if ($purchaseOrder->getStatus() != Status::STATUS_PENDING) {
            $children[$this->children['item_grid_container']] = $this->getItemGridUi();
            $children['purchase_sumary_total'] = $this->getPurchaseSumaryTotal();
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
            'purchase_sumary_supplier',
            'Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Fieldset\PurchaseSumary\Supplier'
        );
    }

    /**
     * Get purchase order item grid
     *
     * @return array
     */
    public function getItemGrid()
    {
        return $this->addHtmlContentContainer(
            'grid_container',
            'Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Fieldset\PurchaseSumary'
        );
    }

    /**
     * Get purchase order item grid
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
                            'purchase_id' => '${ $.provider }:data.purchase_order_id'
                        ],
                        'exports' => [
                            'purchase_id' => '${ $.externalProvider }:params.purchase_id'
                        ],
                        'selectionsProvider' =>
                            $this->children['item_grid_listing']
                            . '.' . $this->children['item_grid_listing']
                            . '.purchase_order_item_template_columns.ids'
                    ]
                ]
            ]
        ];
    }

    /**
     * Get purchase sumary total block
     *
     * @return array
     */
    public function getPurchaseSumaryTotal()
    {
        return $this->addHtmlContentContainer(
            'purchase_sumary_total_container',
            'Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Fieldset\PurchaseSumary\Total'
        );
    }

    /**
     * Add action for buttons purchase sumary
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
        $children['back_order_product_button'] = $this->addButton(
            'Back Sales Products',
            $this->addButtonAction(
                $this->children['back_order_product_modal'],
                $this->children['back_order_product_listing']
            )
        );
        $title = __('All Store Products');
        if($this->productConfig->getProductSource() == \Magestore\PurchaseOrderSuccess\Model\System\Config\ProductSource::TYPE_SUPPLIER) {
            $title = __('All Supplier Products');
        }
        $children['all_supplier_product_button'] = $this->addButton(
            $title,
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
                ->get('Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Fieldset\PurchaseSumary\Item')
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
                                    'purchase_id' => '${ $.provider }:data.purchase_order_id',
                                    'currency_code' => '${ $.provider }:data.currency_code',
                                    'currency_rate' => '${ $.provider }:data.currency_rate'
                                ],
                                'exports' => [
                                    'supplier_id' => '${ $.externalProvider }:params.supplier_id',
                                    'purchase_id' => '${ $.externalProvider }:params.purchase_id',
                                    'currency_code' => '${ $.externalProvider }:params.currency_code',
                                    'currency_rate' => '${ $.externalProvider }:params.currency_rate',
                                ],
                                'selectionsProvider' =>
                                    $this->children[$dataScope]
                                    . '.' . $this->children[$dataScope]
                                    . '.supplier_product_template_columns.ids',
                                'save_url' => $this->urlBuilder->getUrl(
                                    '*/purchaseOrder_product/save',
                                    [
                                        'purchase_id' => $this->getPurchaseOrderId(),
                                        'supplier_id' => $this->getCurrentPurchaseOrder()->getSupplierId()
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
        $title = __('All Store Products');
        if($this->productConfig->getProductSource() == \Magestore\PurchaseOrderSuccess\Model\System\Config\ProductSource::TYPE_SUPPLIER) {
            $title = __('All Supplier Products');
        }
        return $this->addProductModal($title, 'all_supplier_product_listing', 'all_supplier_product_modal');
    }


    /**
     * Get back order product modal
     *
     * @return array
     */
    public function getBackOrderProductModal()
    {
        return $this->addProductModal('Back Sales Products', 'back_order_product_listing', 'back_order_product_modal');
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
                                    'purchase_id' => $this->getPurchaseOrderId(),
                                    'supplier_id' => $this->getCurrentPurchaseOrder()->getSupplierId(),
                                    'type' => $this->getCurrentPurchaseOrder()->getType()
                                ],
                                'formSubmitId' => 'import-purchase-order-product-form'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}