<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Ui\DataProvider\Product\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Form;

/**
 * Class Barcode
 *
 * Used to create barcode label
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class BarcodeLabel extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier implements
    ModifierInterface
{
    /**
     * @var string
     */
    protected $_groupContainer = "barcode_label";

    /**
     * @var string
     */
    protected $_groupLabel = "Barcode";

    /**
     * @var boolean
     */
    protected $_sortOrder = 200;

    /**
     * Modifier Config
     *
     * @var array
     */
    protected $_modifierConfig = [
        'listing' => 'os_product_detail_barcode_listing',
        'columns_ids' => 'os_product_detail_barcode_listing.ids',
        'form' => 'os_product_detail_barcode_listing'
    ];

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magestore\BarcodeSuccess\Model\Source\Template
     */
    protected $barcodeTemplate;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var int
     */
    protected $oneBarcodePerSku;
    /**
     * @var \Magestore\BarcodeSuccess\Model\BarcodeFactory
     */
    protected $barcodeFactory;

    /**
     * BarcodeLabel constructor.
     *
     * @param UrlInterface $urlBuilder
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magestore\BarcodeSuccess\Model\BarcodeFactory $barcodeFactory
     * @param \Magestore\BarcodeSuccess\Model\Source\Template $barcodeTemplate
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $modifierConfig
     */
    public function __construct(
        UrlInterface $urlBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Magestore\BarcodeSuccess\Model\BarcodeFactory $barcodeFactory,
        \Magestore\BarcodeSuccess\Model\Source\Template $barcodeTemplate,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $modifierConfig = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
        $this->barcodeFactory = $barcodeFactory;
        $this->barcodeTemplate = $barcodeTemplate;
        $this->scopeConfig = $scopeConfig;
        $this->oneBarcodePerSku = $this->scopeConfig->getValue('barcodesuccess/general/one_barcode_per_sku');
        $this->_modifierConfig = array_replace_recursive($this->_modifierConfig, $modifierConfig);
    }

    /**
     * @inheritdoc
     */
    public function modifyData(array $data)
    {
        if (!$this->oneBarcodePerSku) {
            return $data;
        }
        $barcode = $this->barcodeFactory->create();
        $barcode->getResource()->load($barcode, $this->request->getParam('id'), 'product_id');
        $data[$this->request->getParam('id')]['os_barcode'] = $barcode->getBarcode();
        $template = $this->scopeConfig->getValue('barcodesuccess/general/default_barcode_template');
        if (!$template) {
            $templates = $this->barcodeTemplate->toOptionArray();
            if (count($templates) > 0) {
                $template = $templates[0]['value'];
            }
        }
        $data[$this->request->getParam('id')]['os_barcode_template'] = $template;
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta)
    {
        if (!$this->oneBarcodePerSku) {
            return $meta;
        }
        $meta = array_replace_recursive(
            $meta,
            [
                $this->_groupContainer => [
                    'children' => $this->getModifierChildren(),
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __($this->_groupLabel),
                                'collapsible' => true,
                                'visible' => true,
                                'opened' => false,
                                'componentType' => Form\Fieldset::NAME,
                                'sortOrder' => $this->_sortOrder,
                                'dataScope' => 'data'
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
        if ($this->hasExistBarcode()) {
            $children = [
                'os_barcode' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Form\Field::NAME,
                                'formElement' => Form\Element\Input::NAME,
                                'dataScope' => 'os_barcode',
                                'label' => __('Barcode'),
                                'sortOrder' => 10,
                            ]
                        ]
                    ]
                ],
                'template' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'dataType' => 'text',
                                'componentType' => Form\Field::NAME,
                                'formElement' => Form\Element\Select::NAME,
                                'component' => 'Magestore_BarcodeSuccess/js/form/element/product/barcode-template',
                                'options' => $this->barcodeTemplate->toOptionArray(),
                                'dataScope' => 'os_barcode_template',
                                'label' => __('Barcode Template'),
                                'sortOrder' => 20,
                                'previewButton' => 'product_form.product_form.barcode_label.preview',
                            ]
                        ]
                    ]
                ],
                'preview' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Form\Field::NAME,
                                'formElement' => Form\Element\Input::NAME,
                                'component' => 'Magestore_BarcodeSuccess/js/form/element/product/preview-button',
                                'label' => __('Preview'),
                                'sortOrder' => 30,
                                'barcodeElement' => 'product_form.product_form.barcode_label.os_barcode',
                                'barcodeTemplateElement' => 'product_form.product_form.barcode_label.template',
                                'url' => $this->urlBuilder->getUrl('barcodesuccess/template/preview')
                            ]
                        ]
                    ]
                ],
                'print' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Form\Field::NAME,
                                'formElement' => Form\Element\Input::NAME,
                                'component' => 'Magestore_BarcodeSuccess/js/form/element/product/print-button',
                                'label' => __('Qty to Print'),
                                'sortOrder' => 40,
                                'previewButton' => 'product_form.product_form.barcode_label.preview',
                            ]
                        ]
                    ]
                ],
            ];
        } else {
            $children = [
                'os_barcode' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Form\Field::NAME,
                                'formElement' => Form\Element\Input::NAME,
                                'dataScope' => 'os_barcode',
                                'label' => __('Barcode'),
                                'sortOrder' => 10,
                            ]
                        ]
                    ]
                ],
            ];
            if ($this->request->getParam('id')) {
                $children = $this->appendGenerateButton($children);
            }
        }
        return $children;
    }

    /**
     * Append generate button
     *
     * @param array $children
     * @return mixed
     */
    public function appendGenerateButton($children)
    {
        $children['generate'] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Form\Field::NAME,
                        'formElement' => Form\Element\Input::NAME,
                        'component' => 'Magestore_BarcodeSuccess/js/form/element/product/generate-button',
                        'label' => __('Generate Barcode'),
                        'sortOrder' => 15,
                        'barcodeElement' => 'product_form.product_form.barcode_label.os_barcode',
                        'barcodeTemplateElement' => 'product_form.product_form.barcode_label.template',
                        'url' => $this->urlBuilder->getUrl('barcodesuccess/index/create'),
                        'productId' => $this->request->getParam('id')
                    ]
                ]
            ]
        ];
        return $children;
    }

    /**
     * Has exist barcode
     *
     * @return bool
     */
    public function hasExistBarcode()
    {
        $productId = $this->request->getParam('id');
        if (!$productId) {
            return false;
        } else {
            $barcodeModel = $this->barcodeFactory->create()->load($productId, 'product_id');
            if ($barcodeModel->getId()) {
                return true;
            } else {
                return false;
            }
        }
    }
}
