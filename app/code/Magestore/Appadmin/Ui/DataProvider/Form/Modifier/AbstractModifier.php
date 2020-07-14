<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Appadmin\Ui\DataProvider\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\HtmlContent;
use Magento\Ui\Component\Form;

/**
 * Class AbstractModifier
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class AbstractModifier implements ModifierInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var string
     */
    protected $scopeName = '';

    /**
     * @var bool
     */
    protected $visible = true;

    /**
     * @var bool
     */
    protected $opened = false;

    /**
     * @var int
     */
    protected $sortOrder = 10;


    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var string
     */
    protected $groupContainer = '';

    /**
     * @var string
     */
    protected $groupLabel = '';

    protected $components = [
        'html' => 'Magento_Ui/js/form/components/html',
        'button' => 'Magestore_Appadmin/js/form/components/button'
    ];

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->objectManager = $objectManager;
        $this->registry = $registry;
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
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
     *
     *
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta){
        return $meta;
    }

    /**
     * Set fieldset is visble
     *
     * @return bool
     */
    public function getVisible(){
        return $this->visible;
    }

    /**
     * Set fieldset is opened
     *
     * @return bool
     */
    public function getOpened(){
        return $this->opened;
    }

    /**
     * Set fieldset sort order
     *
     * @return int
     */
    public function getSortOrder(){
        return $this->sortOrder;
    }

    /**
     * @param $field
     * @return array
     */
    public function setDefaultValue($field, $value){
        $field['arguments']['data']['config']['default'] = $value;
        return $field;
    }

    /**
     * @param $label
     * @param $dataType
     * @param $formElement
     * @param $sortOrder
     * @param array $validation
     * @param null $defaultValue
     * @param string $notice
     * @param bool $disable
     * @return array
     */
    public function addFormField($label, $dataType , $formElement, $sortOrder, $validation = [], $defaultValue = null, $notice = '', $disable = false){
        $field = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                        'label' => __($label),
                        'dataType' => $dataType,
                        'formElement' => $formElement,
                        'sortOrder' => $sortOrder,
                        'default' => $defaultValue,
                        'notice' => __($notice),
                        'validation' => $validation,
                        'disabled' => $disable
                    ],
                ],
            ],
        ];
        if($defaultValue)
            $field = $this->setDefaultValue($field, $defaultValue);
        return $field;
    }

    /**
     * @param $label
     * @param $formElement
     * @param $sortOrder
     * @param array $validation
     * @param null $defaultValue
     * @param string $notice
     * @param bool $disabled
     * @return array
     */
    public function addFormFieldText($label, $formElement, $sortOrder, $validation = [], $defaultValue = null, $notice = '', $disabled = false){
        return $this->addFormField($label, 'text', $formElement, $sortOrder, $validation, $defaultValue, $notice, $disabled);
    }

    /**
     * @param $label
     * @param $formElement
     * @param $sortOrder
     * @param $validation
     * @param null $defaultValue
     * @param string $notice
     * @param bool $disabled
     * @param string $classes
     * @return array
     */
    public function addFormFieldPassword($label, $formElement, $sortOrder, $validation , $classes = '', $defaultValue = null, $notice = '', $disabled = false){
        $field = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                        'elementTmpl' => 'Magestore_Appadmin/form/password',
                        'label' => $label,
                        'dataType' => 'text',
                        'formElement' => $formElement,
                        'sortOrder' => $sortOrder,
                        'validation' => $validation,
                        'default' => $defaultValue,
                        'notice' => __($notice),
                        'disabled' => $disabled,
                        'classes' => $classes
                    ],
                ],
            ]
        ];
        return $field;
    }

    /**
     * @param $label
     * @param $sortOrder
     * @param array $validation
     * @param null $defaultValue
     * @param string $notice
     * @return array
     */
    public function addFormFieldDate($label, $sortOrder, $validation = [], $defaultValue = null, $notice = ''){
        return $this->addFormField($label, 'date', 'date', $sortOrder, $validation, $defaultValue, $notice);
    }

    /**
     * @param $label
     * @param $sortOrder
     * @param array $validation
     * @param null $defaultValue
     * @param string $notice
     * @return array
     */
    public function addFormFieldTextArea($label, $sortOrder, $validation = [], $defaultValue = null, $notice = ''){
        return $this->addFormField($label, 'text', 'textarea', $sortOrder, $validation, $defaultValue, $notice);
    }

    /**
     * @param $label
     * @param $sortOrder
     * @param array $validation
     * @param null $defaultValue
     * @param string $notice
     * @return array
     */
    public function addFormFieldWysiwygArea(
        $label, $sortOrder, $validation = [], $defaultValue = null, $notice = ''){
        $field = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                        'label' => __($label),
                        'dataType' => 'text',
                        'formElement' => 'wysiwyg',
                        'sortOrder' => $sortOrder,
                        'default' => $defaultValue,
                        'validation' => $validation,
                        'notice' => __($notice),
                        'wysiwyg' => true
                    ],
                ],
            ],
        ];
        if($defaultValue)
            $field = $this->setDefaultValue($field, $defaultValue);
        return $field;
    }

    public function addSwitcherConfig($field, $switcherConfig){
        $field['arguments']['data']['config']['switcherConfig'] = [
            'enabled' => true,
            'rules' => $switcherConfig
        ];
        return $field;
    }

    /**
     * @param $label
     * @param $sortOrder
     * @param string $default
     * @param string $notice
     * @param null $switcherConfig
     * @return array
     */
    public function addFormFieldCheckbox($label, $sortOrder, $default = '', $notice = '', $switcherConfig = null)
    {
        $field = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __($label),
                        'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                        'formElement' => 'checkbox',
                        'dataType' => 'number',
                        'sortOrder' => $sortOrder,
                        'valueMap' => [
                            'false' => '0',
                            'true' => '1'
                        ],
                        'notice' => __($notice),
                    ],
                ],
            ],
        ];
        if($switcherConfig){
            $field = $this->addSwitcherConfig($field, $switcherConfig);
        }
        if($default){
            $field = $this->setDefaultValue($field, $default);
        }
        return $field;
    }

    /**
     * @param $label
     * @param array $options
     * @param $sortOrder
     * @param array $validation
     * @param null $defaultValue
     * @param string $notice
     * @param null $switcherConfig
     * @param bool $disabled
     * @return array
     */
    public function addFormFieldSelect(
        $label, $options = [], $sortOrder, $validation = [], $defaultValue = null, $notice = '', $switcherConfig = null, $disabled = false
    ){
        $field = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'dataType' => 'text',
                        'formElement' => 'select',
                        'options' => $options,
                        'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                        'label' => __($label),
                        'sortOrder' => $sortOrder,
                        'validation' => $validation,
                        'disabled' => $disabled,
                        'notice' => __($notice),
                    ],
                ],
            ],
        ];
        if($defaultValue)
            $field = $this->setDefaultValue($field, $defaultValue);
        if($switcherConfig)
            $field = $this->addSwitcherConfig($field, $switcherConfig);
        return $field;
    }

    /**
     * @param $label
     * @param array $options
     * @param $sortOrder
     * @param array $validation
     * @param null $defaultValue
     * @param string $notice
     * @param null $switcherConfig
     * @param bool $disabled
     * @return array
     */
    public function addFormFieldMultiSelect(
        $label, $options = [], $sortOrder, $validation = [], $defaultValue = null, $notice = '', $switcherConfig = null, $disabled = false
    ){
        $field = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'dataType' => 'text',
                        'formElement' => 'multiselect',
                        'options' => $options,
                        'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                        'label' => __($label),
                        'sortOrder' => $sortOrder,
                        'validation' => $validation,
                        'disabled' => $disabled,
                        'notice' => __($notice),
                    ],
                ],
            ],
        ];
        if($defaultValue)
            $field = $this->setDefaultValue($field, $defaultValue);
        return $field;
    }

    /**
     * Add html_content element.
     *
     * @param string $containerName
     * @param string $block
     * @return array
     */
    public function addHtmlContentContainer($containerName = '', $block = ''){
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => Container::NAME,
                        'componentType' => Container::NAME,
//                        'sortOrder' => 10,

                    ],
                ],
            ],
            'children' => [
                $containerName => [
                    'arguments' => [
                        'data' => [
                            'type' => HtmlContent::NAME,
                            'name' => HtmlContent::NAME,
                            'config' => [
                                'componentType' => Container::NAME,
                                'component' => $this->components['html'],
                                'content' => $this->objectManager->create($block)->toHtml()
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @param string $buttonTitle
     * @param array $actions
     * @param string|null $redirectUrl
     * @return array
     */
    public function addButton($buttonTitle = '', $actions = [], $redirectUrl = null){
        $button = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => Container::NAME,
                        'componentType' => Container::NAME,
                        'component' => $this->components['button'],
                        'title' => __($buttonTitle),
                        'actions' => $actions
                    ],
                ],
            ],
        ];
        if($redirectUrl)
            $button['arguments']['data']['config']['redirectUrl'] = $redirectUrl;
        return $button;
    }

    /**
     * Returns text column configuration for the dynamic grid
     *
     * @param string $dataScope
     * @param bool $fit
     * @param string $label
     * @param int $sortOrder
     * @return array
     */
    public function getTextColumn($dataScope, $fit, $label, $sortOrder)
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
                        'label' => __($label),
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];
        return $column;
    }

    /**
     * Returns input number column configuration for the dynamic grid
     *
     * @param string $dataScope
     * @param boolean $fit
     * @param string $label
     * @param int $sortOrder
     * @param array $validation
     * @return array
     */
    public function getInputNumberColumn($dataScope, $fit, $label, $sortOrder, $validation = []){
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'dataType' => Form\Element\DataType\Number::NAME,
                        'formElement' => Form\Element\Input::NAME,
                        'componentType' => Form\Field::NAME,
                        'dataScope' => $dataScope,
                        'label' => __($label),
                        'fit' => $fit,
                        'additionalClasses' => 'admin__field-small',
                        'sortOrder' => $sortOrder,
                        'validation' => $validation
                    ],
                ],
            ],
        ];
    }
}