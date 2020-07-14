<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\ReturnedProduct\Form\Modifier;

/**
 * Class General
 * @package Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\ReturnedProduct\Form\Modifier
 */
class General extends AbstractModifier
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
    ) {
        $this->localeDate = $localeDate;
        parent::__construct($objectManager, $registry, $request, $urlBuilder);
    }
    /**
     * @var string
     */
    protected $groupContainer = 'general_information';

    /**
     * @var string
     */
    protected $groupLabel = 'Returned Time';

    /**
     * @var int
     */
    protected $sortOrder = 10;
    
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
    public function modifyMeta(array $meta){
        $meta = array_replace_recursive(
            $meta,
            [
                $this->groupContainer => [
                    'children' => $this->getGeneralChildren(),
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __($this->groupLabel),
                                'collapsible' => true,
                                'dataScope' => 'data',
                                'visible' => $this->getVisible(),
                                'opened' => true,
                                'componentType' => \Magento\Ui\Component\Form\Fieldset::NAME,
                                'sortOrder' => $this->getSortOrder()
                            ],
                        ],
                    ],
                ],
            ]
        );
        return $meta;   
    }

    /**
     * Add general form fields
     * 
     * @return array
     */
    public function getGeneralChildren(){
        $children = [
            'purchase_order_id' => $this->addFormFieldText('', 'hidden', 10),
            'returned_at' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                            'label' => __('Returned Date'),
                            'dataType' => 'date',
                            'formElement' => 'date',
                            'sortOrder' => 20,
                            'dataScope' => 'returned_at',
                            'default' => $this->localeDate->date()->format('Y-m-d'),
                            'validation' => ['required-entry' => true]
                        ],
                    ],
                ],
            ]
        ];
        return $children;
    }
}