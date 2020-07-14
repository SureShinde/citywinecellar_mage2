<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\BarcodeSuccess\Ui\DataProvider\Barcode\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\HtmlContent;

/**
 * Class General
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class General implements ModifierInterface
{
    /**
     * @var \Magestore\BarcodeSuccess\Helper\Data
     */
    protected $helper;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * General constructor.
     * @param \Magestore\BarcodeSuccess\Helper\Data $helper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magestore\BarcodeSuccess\Helper\Data $helper,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->helper = $helper;
        $this->objectManager = $objectManager;
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
                'os_barcode_generate_form_general' => [
                    'children' => $this->getGeneralChildren(),
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('General Infomation'),
                                'collapsible' => false,
                                'visible' => true,
                                'opened' => true,
                                'componentType' => \Magento\Ui\Component\Form\Fieldset::NAME,
                                'sortOrder' => 5
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
    public function getGeneralChildren()
    {
        $children = [
            'warning_message' => [
                'arguments' => [
                    'data' => [
                        'type' => HtmlContent::NAME,
                        'name' => HtmlContent::NAME,
                        'config' => [
                            'componentType' => Container::NAME,
                            'component' => 'Magento_Ui/js/form/components/html',
                            'content' => $this->objectManager->create('Magestore\BarcodeSuccess\Block\Adminhtml\Barcode\Generate\WarningMessage')->toHtml()
                        ]
                    ],
                ],
            ],
            'generate_per_item' => $this->getPerItemField(),
            'generate_per_purchase' => $this->getPerPurchaseField(),
            'reason' => $this->getReasonField(),
        ];
        $one_barcode_per_sku = $this->helper->getStoreConfig('barcodesuccess/general/one_barcode_per_sku');
        if($one_barcode_per_sku){
            unset($children['generate_per_item']);
            unset($children['generate_per_purchase']);
        }
        return $children;
    }

    /**
     * Item mode field
     *
     * @return array
     */
    public function getPerItemField()
    {
        $field = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => 'field',
                        'dataScope' => 'data.general_information.generate_type',
                        'dataType' => 'text',
                        'formElement' =>'checkbox',
                        'prefer' =>'radio',
                        'description' =>__('Generate barcode per item (each item will generate a barcode with qty = 1)'),
                        'value' => 'item',
                        'checked' => true,
                        'sortOrder' => 1,
                    ],
                ],
            ],
        ];
        return $field;
    }

    /**
     * Purchase mode field
     *
     * @return array
     */
    public function getPerPurchaseField()
    {
        $field = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => 'field',
                        'dataScope' => 'data.general_information.generate_type',
                        'dataType' => 'text',
                        'formElement' =>'checkbox',
                        'prefer' =>'radio',
                        'description' =>__('Generate barcode per purchase (each product sku will generate a barcode)'),
                        'value' => 'purchase',
                        'checked' => false,
                        'sortOrder' => 2,
                    ],
                ],
            ],
        ];
        return $field;
    }

    /**
     * Reason field
     *
     * @return array
     */
    public function getReasonField()
    {
        $field = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => 'field',
                        'dataScope' => 'data.general_information.reason',
                        'dataType' => 'string',
                        'formElement' => 'textarea',
                        'label' =>__('Reason'),
                        'sortOrder' => 3
                    ],
                ],
            ],
        ];
        return $field;
    }
}